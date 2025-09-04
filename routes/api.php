<?php

use Illuminate\Support\Facades\Route;

// Healthcheck simple
Route::get('/ping', fn () => ['status' => 'ok']);

// Temporaire (si le modèle/DB n'est pas prêt, on renvoie un tableau vide)
Route::get('/ebooks', fn () => []);
