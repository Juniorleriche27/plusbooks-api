<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // GET /api/posts?group_id=...
    public function index(Request $req) {
        $q = Post::with(['user:id,name', 'group:id,name'])
            ->latest();

        if ($req->filled('group_id')) {
            $q->where('group_id', $req->integer('group_id'));
        }

        return $q->paginate(12);
    }

    // GET /api/posts/{id}
    public function show(Post $post) {
        $post->load(['user:id,name', 'group:id,name', 'comments.user:id,name']);
        return $post;
    }

    // POST /api/posts
    public function store(Request $req) {
        $data = $req->validate([
            'title'     => ['nullable','string','max:255'],
            'body'      => ['required','string'],
            'group_id'  => ['nullable','integer','exists:groups,id'],
        ]);

        $user = $req->user();

        // si group_id présent, vérifier l'adhésion
        if (!empty($data['group_id'])) {
            $group = Group::find($data['group_id']);
            $isMember = $group->members()->where('users.id', $user->id)->exists()
                      || $group->owner_id === $user->id;
            if (!$isMember) {
                return response()->json(['message' => 'Vous devez être membre du groupe.'], 403);
            }
        }

        $data['user_id'] = $user->id;
        $post = Post::create($data);

        return response()->json($post->load(['user:id,name','group:id,name']), 201);
    }

    // PUT /api/posts/{post}
    public function update(Request $req, Post $post) {
        if ($post->user_id !== $req->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $data = $req->validate([
            'title' => ['nullable','string','max:255'],
            'body'  => ['required','string'],
        ]);

        $post->update($data);
        return $post->load(['user:id,name','group:id,name']);
    }

    // DELETE /api/posts/{post}
    public function destroy(Request $req, Post $post) {
        if ($post->user_id !== $req->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $post->delete();
        return response()->noContent();
    }
}
