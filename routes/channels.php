<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Message.{from_user_id}.{to_user_id}', function ($user, $from_user_id, $to_user_id) {
    return (int) $user->id === (int) $from_user_id || (int) $user->id === (int) $to_user_id;
});

Broadcast::channel('App.User.{user_id}', function ($user, $user_id) {
    return true;
    return (int) $user->id === (int) $from_user_id || (int) $user->id === (int) $to_user_id;
});
