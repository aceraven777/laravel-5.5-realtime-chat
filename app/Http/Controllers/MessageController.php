<?php

namespace App\Http\Controllers;

use App\User;
use App\Message;
use App\Events\ChatSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
	public function chatMessages($to_user_id)
    {
        $current_user = Auth::user();
        $to_user = User::findOrFail($to_user_id);

        if ($current_user->id == $to_user->id) {
            return redirect('/');
        }

        $messages = Message::with('fromUser')
            ->where(function ($query) use ($current_user, $to_user) {
                $query->orWhere(function ($query) use ($current_user, $to_user) {
                    $query->where('from_user_id', $current_user->id);
                    $query->where('to_user_id', $to_user->id);
                });

                $query->orWhere(function ($query) use ($current_user, $to_user) {
                    $query->where('to_user_id', $current_user->id);
                    $query->where('from_user_id', $to_user->id);
                });
            })
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(50)
            ->get();

    	return view('messages.chat_messages', compact('current_user', 'to_user', 'messages'));
    }

    public function getNextChatMessages(Request $request)
    {
        $current_user = Auth::user();
        $to_user = User::findOrFail($to_user_id);

        $messages = Message::with('fromUser')
            ->where(function ($query) use ($current_user, $to_user) {
                $query->orWhere(function ($query) use ($current_user, $to_user) {
                    $query->where('from_user_id', $current_user->id);
                    $query->where('to_user_id', $to_user->id);
                });

                $query->orWhere(function ($query) use ($current_user, $to_user) {
                    $query->where('to_user_id', $current_user->id);
                    $query->where('from_user_id', $to_user->id);
                });
            })
            ->where('created_at', '<=', $request->input('last_created_at'))
            ->where('id', '<', $request->input('last_message_id'))
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(50)
            ->get();

        return $messages;
    }

    public function chatUser(Request $request, $to_user_id)
    {
        $this->validate($request, ['text' => 'required']);

        $current_user = Auth::user();
        $to_user = User::findOrFail($to_user_id);

        if ($current_user->id == $to_user->id) {
            return redirect('/');
        }

        $message = Message::create([
            'from_user_id' => $current_user->id,
            'to_user_id' => $to_user->id,
            'text' => $request->input('text'),
        ]);

        event(new ChatSent($message));

        return ['status' => true];
    }
}
