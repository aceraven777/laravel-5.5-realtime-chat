<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Message;
use App\Events\ChatSent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Get messages of logged in user to specific user
     *
     * @param Request $request
     * @param User $toUser
     * @return array
     */
	public function index(Request $request, User $toUser)
    {
        $currentUser = Auth::user();

        if ($currentUser->id == $toUser->id) {
            return redirect('/');
        }

        $query = Message::with('fromUser')
            ->where(function ($query) use ($currentUser, $toUser) {
                $query->orWhere(function ($query) use ($currentUser, $toUser) {
                    $query->where('from_user_id', $currentUser->id);
                    $query->where('to_user_id', $toUser->id);
                });

                $query->orWhere(function ($query) use ($currentUser, $toUser) {
                    $query->where('to_user_id', $currentUser->id);
                    $query->where('from_user_id', $toUser->id);
                });
            })
            ->orderBy('id', 'DESC')
            ->limit(50);
        
        if ($request->input('last_message_id')) {
            $query->where('id', '<', $request->input('last_message_id'));
        }
        
        $messages = $query->get();

    	return $messages;
    }

    /**
     * Send message to specific user
     *
     * @param Request $request
     * @param User $toUser
     * @return array
     */
    public function chatUser(Request $request, User $toUser)
    {
        $this->validate($request, ['text' => 'required']);

        $currentUser = Auth::user();

        if ($currentUser->id == $toUser->id) {
            return redirect('/');
        }

        $message = Message::create([
            'from_user_id' => $currentUser->id,
            'to_user_id' => $toUser->id,
            'text' => $request->input('text'),
        ]);

        event(new ChatSent($message));

        return ['status' => true, 'message' => $message];
    }
}
