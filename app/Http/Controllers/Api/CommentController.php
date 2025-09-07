<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // GET /api/posts/{post}/comments
    public function index(Post $post) {
        return $post->comments()
            ->with('user:id,name')
            ->oldest()
            ->paginate(20);
    }

    // POST /api/posts/{post}/comments
    public function store(Request $req, Post $post) {
        // si post de groupe, s'assurer que l'utilisateur est membre
        if ($post->group_id) {
            $isMember = $post->group->members()->where('users.id', $req->user()->id)->exists()
                      || $post->group->owner_id === $req->user()->id;
            if (!$isMember) {
                return response()->json(['message' => 'Vous devez être membre du groupe.'], 403);
            }
        }

        $data = $req->validate([
            'body' => ['required','string','max:5000'],
        ]);

        $comment = Comment::create([
            'user_id' => $req->user()->id,
            'post_id' => $post->id,
            'body'    => $data['body'],
        ]);

        return response()->json($comment->load('user:id,name'), 201);
    }

    // DELETE /api/comments/{comment}
    public function destroy(Request $req, Comment $comment) {
        $userId = $req->user()->id;
        $isOwner = $comment->user_id === $userId;
        $isPostOwner = $comment->post->user_id === $userId;

        if (!$isOwner && !$isPostOwner) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $comment->delete();
        return response()->noContent();
    }
}
