<?php

use Illuminate\Support\Facades\Route;

// ----- Auth & Ebooks controllers (namespace App\Http\Controllers)
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EbookController;

// ----- Community controllers (namespace App\Http\Controllers\Api)
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\ContactController;


/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::post('/contact', [ContactController::class, 'store']);

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Ebooks (lecture)
Route::get('/ebooks',         [EbookController::class, 'index']);
Route::get('/ebooks/{ebook}', [EbookController::class, 'show']);

// Posts & comments (lecture seule)
Route::get('/posts',                 [PostController::class, 'index']);
Route::get('/posts/{post}',          [PostController::class, 'show']);
Route::get('/posts/{post}/comments', [CommentController::class, 'index']);

// Groups (listing & détail publics — rends-les privés si tu préfères)
Route::get('/groups',        [GroupController::class, 'index']);
Route::get('/groups/{group}',[GroupController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Protected routes (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Session utilisateur
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Ebooks (écriture)
    Route::post('/ebooks',               [EbookController::class, 'store']);
    Route::put('/ebooks/{ebook}',        [EbookController::class, 'update']);
    Route::delete('/ebooks/{ebook}',     [EbookController::class, 'destroy']);

    // Posts (écriture)
    Route::post('/posts',                [PostController::class, 'store']);
    Route::put('/posts/{post}',          [PostController::class, 'update']);
    Route::delete('/posts/{post}',       [PostController::class, 'destroy']);

    // Comments (écriture)
    Route::post('/posts/{post}/comments',[CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // Messages privés
    Route::get('/messages/with/{user}',  [MessageController::class, 'thread']);
    Route::post('/messages',             [MessageController::class, 'send']);
    Route::get('/messages/unread_count', [MessageController::class, 'unreadCount']);

    // Groups (gestion & adhésion)
    Route::post('/groups',                          [GroupController::class, 'store']);
    Route::put('/groups/{group}',                   [GroupController::class, 'update']);
    Route::delete('/groups/{group}',                [GroupController::class, 'destroy']);

    Route::post('/groups/{group}/join',             [GroupController::class, 'join']);
    Route::post('/groups/{group}/leave',            [GroupController::class, 'leave']);
    Route::get('/groups/{group}/members',           [GroupController::class, 'members']);
    // ...
    Route::get('/ebooks/{ebook}/download', [EbookController::class, 'download'])->whereNumber('ebook');
    Route::get('/ebooks/{ebook}',   [EbookController::class, 'show'])->whereNumber('ebook');

    // NOTE: on garde {userId} car ton contrôleur a la signature ($req, Group $group, $userId)
    // Si tu veux le binding de modèle User, renomme la route en {user} et type-hinte User $user.
    Route::post('/groups/{group}/members/{userId}/make-admin', [GroupController::class, 'makeAdmin']);
});
