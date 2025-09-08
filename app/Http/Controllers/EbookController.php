<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EbookController extends Controller
{
    // GET /api/ebooks  (renvoie la pagination Laravel + file_url)
    public function index()
    {
        $paginated = Ebook::query()->latest()->paginate(12);

        // Ajouter l'URL publique du PDF si présent
        $paginated->getCollection()->transform(function ($e) {
            $e->file_url = $e->file_path
                ? Storage::disk('public')->url($e->file_path)
                : null;
            return $e;
        });

        return $paginated;
    }

    // GET /api/ebooks/{ebook}
    public function show(Ebook $ebook)
    {
        $ebook->file_url = $ebook->file_path
            ? Storage::disk('public')->url($ebook->file_path)
            : null;

        return $ebook;
    }

    // POST /api/ebooks  (multipart/form-data, champ "file" optionnel)
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price'       => ['nullable','numeric','min:0'],   // <- pas obligatoire
            'file'        => ['nullable','file','mimes:pdf','max:20480'], // 20 Mo
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            // stocke dans storage/app/public/ebooks/...
            $path = $request->file('file')->store('ebooks', 'public');
        }

        $ebook = new Ebook();
        $ebook->user_id     = optional($request->user())->id; // si route protégée -> id, sinon null autorisé
        $ebook->title       = $data['title'];
        $ebook->description = $data['description'] ?? null;
        $ebook->price       = $data['price'] ?? 0;
        $ebook->file_path   = $path;
        $ebook->save();

        $ebook->file_url = $path ? Storage::disk('public')->url($path) : null;

        return response()->json($ebook, 201);
    }

    // PUT /api/ebooks/{ebook}
    public function update(Request $request, Ebook $ebook)
    {
        $data = $request->validate([
            'title'       => ['sometimes','required','string','max:255'],
            'description' => ['nullable','string'],
            'price'       => ['sometimes','nullable','numeric','min:0'],
            'file'        => ['nullable','file','mimes:pdf','max:20480'],
        ]);

        // Fichier remplacé ?
        if ($request->hasFile('file')) {
            if ($ebook->file_path) {
                Storage::disk('public')->delete($ebook->file_path);
            }
            $ebook->file_path = $request->file('file')->store('ebooks','public');
        }

        // Appliquer champs simples
        if (array_key_exists('title', $data))       $ebook->title = $data['title'];
        if (array_key_exists('description', $data)) $ebook->description = $data['description'];
        if (array_key_exists('price', $data))       $ebook->price = $data['price'] ?? 0;

        $ebook->save();

        $ebook->file_url = $ebook->file_path
            ? Storage::disk('public')->url($ebook->file_path)
            : null;

        return response()->json($ebook);
    }

    // DELETE /api/ebooks/{ebook}
    public function destroy(Ebook $ebook)
    {
        if ($ebook->file_path) {
            Storage::disk('public')->delete($ebook->file_path);
        }
        $ebook->delete();

        return response()->json(['message' => 'Supprimé']);
    }
}
