<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EbookController extends Controller
{
    // GET /api/ebooks
    public function index()
    {
        return Ebook::query()->latest()->paginate(12);
    }

    // GET /api/ebooks/{ebook}
    public function show(Ebook $ebook)
    {
        return $ebook;
    }

    // POST /api/ebooks  (multipart/form-data)
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'file' => ['nullable','file','mimes:pdf','max:20480'], // 20MB
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('ebooks', 'public');
        }

        $ebook = Ebook::create([
            'user_id' => null, // rapide pour l’instant (on branchera l’auth après)
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'file_path' => $path,
        ]);

        return response()->json($ebook, 201);
    }

    // POST /api/ebooks/{ebook}  (update rapide)
    public function update(Request $request, Ebook $ebook)
    {
        $data = $request->validate([
            'title' => ['sometimes','required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['sometimes','required','numeric','min:0'],
            'file' => ['nullable','file','mimes:pdf','max:20480'],
        ]);

        if ($request->hasFile('file')) {
            if ($ebook->file_path) Storage::disk('public')->delete($ebook->file_path);
            $ebook->file_path = $request->file('file')->store('ebooks','public');
        }

        $ebook->fill($data)->save();

        return response()->json($ebook);
    }

    // DELETE /api/ebooks/{ebook}
    public function destroy(Ebook $ebook)
    {
        if ($ebook->file_path) Storage::disk('public')->delete($ebook->file_path);
        $ebook->delete();
        return response()->json(['message'=>'Supprimé']);
    }
}
