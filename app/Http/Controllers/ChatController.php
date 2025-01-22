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

    public function chatusers()
    {
        $authuser = Auth::user();
        //chated with
        $chatusers = Chat::where('user_id_1', $authuser->id)->orWhere('user_id_2', $authuser->id)->get();

        $userid = [];
        foreach($chatusers as $chat){
            if($chat->user_id_1 == $authuser->id){
                $user = $chat->user_id_2;
                $userdetails = User::where('id', $user)->with('profile')->first();
                $userid[] = $userdetails;
            }else{
                $user = $chat->user_id_1;
                $userdetails = User::where('id', $user)->with('profile')->first();
                $userid[] = $userdetails;
            }
        }

        return $userid;
    }

    public function startChat(Request $request)
    {
        $fromUserId = auth()->id();
        $toUserId = $request->user_id;



        //check if chat already exists
        $user1 = Chat::where('user_id_1', $fromUserId)->where('user_id_2', $toUserId)->first();

        //check if chat already exists
        $user2 = Chat::where('user_id_1', $toUserId)->where('user_id_2', $fromUserId)->first();

        if($user1 || $user2){
            return response()->json([
                'chat' => $user1,
                'messages' => $user1->messages()->orderBy('created_at')->get(),
            ]);
        }

        $chat = Chat::create([
            'user_id_1' => $fromUserId,
            'user_id_2' => $toUserId,
        ]);

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
