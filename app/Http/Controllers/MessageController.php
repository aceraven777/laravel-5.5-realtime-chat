<?php

namespace App\Http\Controllers;

use App\User;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Chat page for of current and specific user
     *
     * @param User $to_user
     * @return \Illuminate\View\View
     */
	public function index(User $to_user)
    {
        $current_user = Auth::user();

        if ($current_user->id == $to_user->id) {
            return redirect('/');
        }

    	return view('messages.chat_messages', compact('to_user', 'messages'));
    }
}
