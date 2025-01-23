<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Tutor;
use App\Models\Profile;
use App\Models\DegreeProgram;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class ChatController extends Controller
{
    public function index()
    {
        return Inertia::render('Chat/ChatPage', [
            'users' => $this->chatusers(),
            'auth' => Auth::user(),
        ]);
    }
    public function getUsers()
    {
        $users = User::where('id', '<>', auth()->id())->get();
        foreach($users as $user){
            $userprofile = Profile::where('user_id', $user->id)->first();
            if ($userprofile) {
                $userdegree = DegreeProgram::where('id', $userprofile->degree_id)->first();
                $isTutor = Tutor::where('user_id', $user->id)->exists();
                $userid[] = [
                    'id' => $user->id,
                    'user' => $user->name,
                    'profile_pic' => $userprofile->profile_pic,
                    'isTutor' => $isTutor? 'Yes' : 'No',
                    'degree' => $userdegree ? $userdegree->degree_name : 'N/A', // Handle missing degree gracefully
                ];
            } 
        }

        return $userid;
    }

    public function chatusers()
    {
        $authuser = Auth::user();
        //chated with
        $chatusers = Chat::where('user_id_1', $authuser->id)->orWhere('user_id_2', $authuser->id)->get();

        $userid = [];
        foreach ($chatusers as $chat) {
            if ($chat->user_id_1 == $authuser->id) {
                $user = $chat->user_id_2;
            } else {
                $user = $chat->user_id_1;
            }

            $userdetails = User::where('id', $user)->first();
            $userprofile = Profile::where('user_id', $user)->first();
            $userdegree = DegreeProgram::where('id', $userprofile->degree_id)->first();
            $isTutor = Tutor::where('user_id', $user)->exists();
            $lastMessage = Message::where('chat_id', $chat->id) // Assuming 'chat_id' is the foreign key
                ->orderBy('created_at', 'desc') // Sort by the latest message
                ->first(); // Get the latest message

            $userid[] = [
                'id' => $userdetails->id,
                'user' => $userdetails->name,
                'profile_pic' => $userprofile->profile_pic,
                'degree' => $userdegree->degree_name,
                'isTutor' => $isTutor ? 'Yes' : 'No',
                'lastMessage' => $lastMessage !== null ? $lastMessage->message : null,
                'lastMessageTime' => $lastMessage !== null ? $lastMessage->created_at : null,
            ];
        }

        // Sort $userid by lastMessageTime in descending order (newest first)
        usort($userid, function ($a, $b) {
            return strtotime($b['lastMessageTime']) <=> strtotime($a['lastMessageTime']);
        });

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
                'chat' => $user1 ? $user1 : $user2,
                'messages' => $user1 ? $user1->messages()->orderBy('created_at')->get() : $user2->messages()->orderBy('created_at')->get(),
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
