<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    // GET /api/groups
    public function index() {
        return Group::withCount(['members','posts'])->latest()->paginate(12);
    }

    // GET /api/groups/{group}
    public function show(Group $group) {
        $group->load(['owner:id,name', 'members:id,name']);
        return $group;
    }

    // POST /api/groups
    public function store(Request $req) {
        $data = $req->validate([
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'is_public'   => ['boolean'],
        ]);

        $data['owner_id'] = $req->user()->id;
        $group = Group::create($data);

        // ajouter le créateur comme admin
        $group->members()->attach($req->user()->id, ['role' => 'admin']);

        return response()->json($group->load('owner:id,name'), 201);
    }

    // PUT /api/groups/{group}
    public function update(Request $req, Group $group) {
        if ($group->owner_id !== $req->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $data = $req->validate([
            'name'        => ['sometimes','string','max:255'],
            'description' => ['nullable','string'],
            'is_public'   => ['boolean'],
        ]);

        $group->update($data);
        return $group->load('owner:id,name');
    }

    // DELETE /api/groups/{group}
    public function destroy(Request $req, Group $group) {
        if ($group->owner_id !== $req->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $group->delete();
        return response()->noContent();
    }

    // POST /api/groups/{group}/join
    public function join(Request $req, Group $group) {
        $uid = $req->user()->id;
        if (!$group->members()->where('users.id', $uid)->exists()) {
            $group->members()->attach($uid, ['role' => 'member']);
        }
        return ['joined' => true];
    }

    // POST /api/groups/{group}/leave
    public function leave(Request $req, Group $group) {
        $uid = $req->user()->id;
        if ($group->owner_id === $uid) {
            return response()->json(['message' => "Le propriétaire ne peut pas quitter son groupe."], 422);
        }
        $group->members()->detach($uid);
        return ['left' => true];
    }

    // GET /api/groups/{group}/members
    public function members(Group $group) {
        return $group->members()->select('users.id','users.name')->withPivot('role')->get();
    }

    // POST /api/groups/{group}/members/{user}/make-admin
    public function makeAdmin(Request $req, Group $group, $userId) {
        if ($group->owner_id !== $req->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $group->members()->updateExistingPivot($userId, ['role' => 'admin']);
        return ['ok' => true];
    }
}
