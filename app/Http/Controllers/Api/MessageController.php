<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // GET /api/messages/with/{user}
    public function thread(Request $req, User $user) {
        $me = $req->user()->id;

        $messages = Message::where(function($q) use ($me, $user) {
                $q->where('sender_id', $me)->where('recipient_id', $user->id);
            })
            ->orWhere(function($q) use ($me, $user) {
                $q->where('sender_id', $user->id)->where('recipient_id', $me);
            })
            ->orderBy('created_at')
            ->get();

        // marquer comme lus les messages de l'autre
        Message::where('sender_id', $user->id)
            ->where('recipient_id', $me)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $messages->load(['sender:id,name', 'recipient:id,name']);
    }

    // POST /api/messages
    public function send(Request $req) {
        $data = $req->validate([
            'recipient_id' => ['required','exists:users,id','different:sender_id'],
            'body'         => ['required','string','max:5000'],
        ]);

        $msg = Message::create([
            'sender_id'    => $req->user()->id,
            'recipient_id' => $data['recipient_id'],
            'body'         => $data['body'],
        ]);

        return response()->json($msg->load(['sender:id,name','recipient:id,name']), 201);
    }

    // GET /api/messages/unread_count
    public function unreadCount(Request $req) {
        $me = $req->user()->id;
        $count = Message::where('recipient_id', $me)->whereNull('read_at')->count();
        return ['unread' => $count];
    }
}
