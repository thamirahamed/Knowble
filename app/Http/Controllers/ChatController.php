<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class ChatController extends Controller
{
    public function index()
    {
        // Get all users except the currently logged-in user
        $users = User::where('id', '!=', Auth::id())->select('id', 'name', 'email')->get();

        // Pass the users list to the ChatPage component
        return Inertia::render('Chat/ChatPage', ['users' => $users]);
    }
}
