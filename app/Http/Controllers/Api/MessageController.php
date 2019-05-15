<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Message;
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
        $limit = 25;
        $currentUser = Auth::user();

        if ($currentUser->id == $toUser->id) {
            return response('Forbidden Access.', 403);
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
            ->orderBy('id', 'DESC');
        
        if ($request->input('last_message_id')) {
            $query->where('id', '<', $request->input('last_message_id'));
        }

        $total_messages = $query->count();

        $query->limit($limit);
        
        $messages = $query->get();

    	return [
            'has_more_messages' => $total_messages > $messages->count(),
            'messages' => $messages,
        ];
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
            return ['status' => false, 'message' => 'You cannot send a message to yourself.'];
        }

        $message = Message::create([
            'from_user_id' => $currentUser->id,
            'to_user_id' => $toUser->id,
            'text' => $request->input('text'),
        ]);

        return ['status' => true, 'message' => $message];
    }
}
