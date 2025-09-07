<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EbookController;
use App\Http\Controllers\AuthController;

// --- Auth ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// --- Ebooks (public: liste + détail) ---
Route::get('/ebooks',           [EbookController::class, 'index']);
Route::get('/ebooks/{ebook}',   [EbookController::class, 'show']);

// --- Ebooks (protégé: créer/modifier/supprimer) + profil ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/ebooks',                [EbookController::class, 'store']);
    Route::put('/ebooks/{ebook}',         [EbookController::class, 'update']);
    Route::delete('/ebooks/{ebook}',      [EbookController::class, 'destroy']);

    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout',[AuthController::class, 'logout']);
});
