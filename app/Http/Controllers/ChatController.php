<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    public function index()
    {
        return view('dashboard', [
            'users' => User::where('id', '!=', Auth::id())->get(),
            'groups' => Group::all()
        ]);
    }
    public function sendMessage(Request $request)
    {
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->type === 'user' ? $request->id : null,
            'group_id' => $request->type === 'group' ? $request->id : null,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        $senderName = Auth::user()->name;

        return response()->json(['message' => $message, 'sender' => $senderName]);
    }

    public function getMessages($id, Request $request)
    {
        $query = Message::query();

        if ($request->type === 'user') {
            $query->where(function ($q) use ($id) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $id);
            })->orWhere(function ($q) use ($id) {
                $q->where('sender_id', $id)->where('receiver_id', Auth::id());
            });
        } else {
            $query->where('group_id', $id);
        }

        return $query->orderBy('created_at', 'asc')->get()->map(function ($message) {
            return [
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name,
                'message' => $message->message,
            ];
        });
    }
}
