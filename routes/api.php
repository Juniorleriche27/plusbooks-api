<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EbookController;
Route::get('/ping', fn () => ['status' => 'ok']);

// Ebooks (GET publics, CRUD simple)
Route::get('/ebooks', [EbookController::class, 'index']);
Route::get('/ebooks/{ebook}', [EebookController::class, 'show']); // ← attention, corrige en EbookController si auto-correct ne marche pas
Route::post('/ebooks', [EbookController::class, 'store']);
Route::post('/ebooks/{ebook}', [EbookController::class, 'update']); // simple pour aller vite
Route::delete('/ebooks/{ebook}', [EbookController::class, 'destroy']);
