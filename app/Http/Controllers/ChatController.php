<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class ChatController extends Controller
{
    public function index()
    {
        return Inertia::render('Chat/ChatPage', [
            'users' => $this->getUsers(),
            'auth' => Auth::user(),
        ]);
    }
    public function getUsers()
    {
        return User::where('id', '<>', auth()->id())
            ->with('profile') // Assuming a Profile relationship exists
            ->get();
    }

    public function startChat(Request $request)
    {
        $fromUserId = auth()->id();
        $toUserId = $request->user_id;

        $chat = Chat::with('messages')
            ->whereHas('users', fn($q) => $q->where('user_id', $fromUserId))
            ->whereHas('users', fn($q) => $q->where('user_id', $toUserId))
            ->first();

        if (!$chat) {
            $chat = Chat::create(['name' => "Chat between $fromUserId and $toUserId"]);
            $chat->users()->attach([$fromUserId, $toUserId]);
        }

        return response()->json([
            'chat' => $chat,
            'messages' => $chat->messages()->orderBy('created_at')->get(),
        ]);
    }

    public function sendMessage(Request $request)
    {
        $message = Message::create([
            'chat_id' => $request->chat_id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    public function getMessages($chatId)
    {
        // Fetch messages for the specific chat
        $messages = Message::where('chat_id', $chatId)->get();

        // Return the messages as JSON
        return response()->json([
            'messages' => $messages
        ]);
    }
}
