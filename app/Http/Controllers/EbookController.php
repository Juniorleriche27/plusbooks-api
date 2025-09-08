<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EbookController extends Controller
{
    /**
     * GET /api/ebooks
     * Renvoie une pagination des e-books (12 par page) + file_url calculée.
     */
    public function index()
    {
        $paginated = Ebook::query()->latest()->paginate(12);

        // Ajoute file_url pour chaque élément si file_path existe
        $paginated->getCollection()->transform(function (Ebook $e) {
            $e->file_url = $e->file_path
                ? Storage::disk('public')->url($e->file_path)
                : null;
            return $e;
        });

        return $paginated;
    }

    /**
     * GET /api/ebooks/{ebook}
     */
    public function show(Ebook $ebook)
    {
        $ebook->file_url = $ebook->file_path
            ? Storage::disk('public')->url($ebook->file_path)
            : null;

        return $ebook;
    }

    /**
     * POST /api/ebooks
     * Accepté en multipart/form-data ; champ "file" optionnel (PDF).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'file'        => ['nullable', 'file', 'mimes:pdf', 'max:20480'], // 20 Mo
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            // Stocké dans storage/app/public/ebooks/...
            $path = $request->file('file')->store('ebooks', 'public');
        }

        $ebook = new Ebook();
        $ebook->user_id     = $request->user()?->id; // si protégé -> id ; sinon null autorisé
        $ebook->title       = $data['title'];
        $ebook->description = $data['description'] ?? null;
        $ebook->price       = $data['price'] ?? 0;
        $ebook->file_path   = $path;
        $ebook->save();

        $ebook->file_url = $path ? Storage::disk('public')->url($path) : null;

        return response()->json($ebook, 201);
    }

    /**
     * PUT /api/ebooks/{ebook}
     * Mise à jour des champs + remplacement éventuel du PDF.
     */
    public function update(Request $request, Ebook $ebook)
    {
        $data = $request->validate([
            'title'       => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'file'        => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        // Remplacement du fichier ?
        if ($request->hasFile('file')) {
            if ($ebook->file_path) {
                Storage::disk('public')->delete($ebook->file_path);
            }
            $ebook->file_path = $request->file('file')->store('ebooks', 'public');
        }

        // Champs simples
        if (array_key_exists('title', $data))       $ebook->title = $data['title'];
        if (array_key_exists('description', $data)) $ebook->description = $data['description'];
        if (array_key_exists('price', $data))       $ebook->price = $data['price'] ?? 0;

        $ebook->save();

        $ebook->file_url = $ebook->file_path
            ? Storage::disk('public')->url($ebook->file_path)
            : null;

        return response()->json($ebook);
    }

    /**
     * DELETE /api/ebooks/{ebook}
     */
    public function destroy(Ebook $ebook)
    {
        if ($ebook->file_path) {
            Storage::disk('public')->delete($ebook->file_path);
        }
        $ebook->delete();

        return response()->json(['message' => 'Supprimé']);
    }

    /**
     * GET /api/ebooks/{ebook}/download
     * Stream le PDF directement (bypass du lien symbolique /storage).
     */
    public function download(Ebook $ebook)
    {
        if (!$ebook->file_path) {
            return response()->json(['message' => 'Aucun fichier attaché à cet e-book.'], 404);
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($ebook->file_path)) {
            return response()->json(['message' => 'Fichier manquant sur le serveur.'], 404);
        }

        $path = $disk->path($ebook->file_path);

        return response()->file($path, [
            'Content-Type'  => 'application/pdf',
            'Cache-Control' => 'private, max-age=0, no-cache',
        ]);
    }
}
