@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <div class='chat-messages'>
                        @foreach ($messages->reverse() as $message)
                            @include('messages._single_message')
                        @endforeach
                    </div>

                    <form id="chat-user-form" class="form-horizontal" method="POST" action="{{ route('users.chat-user', [$to_user->id]) }}">
                        {{ csrf_field() }}

                        <div class="text-input form-inline">
                            <input type="text" name="text" class="form-control" placeholder="Type your message here..." />
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent

<script type="text/javascript">
    var from_user = {
        id: {{ $current_user->id }},
        name: "{{ $current_user->name }}",
    };
    var to_user = {
        id: {{ $to_user->id }},
        name: "{{ $to_user->name }}",
    };

    function insertChat(user_id, text)
    {
        var new_chat = "";
        new_chat += "<div class='single-message'>";
            new_chat += "<h5>" + getUserName(user_id) + "</h5>";
            new_chat += "<p>" + text + "</p>";
        new_chat += "</div>";

        $('.chat-messages').append(new_chat);
    }

    function getUserName(user_id)
    {
        if (from_user.id == user_id) {
            return from_user.name;
        }
        else if (to_user.id == user_id) {
            return to_user.name;
        }

        return '';
    }

    Echo.private('App.Message.' + from_user.id + '.' + to_user.id)
        .listen('ChatSent', (e) => {
            console.log(e);

            var message = e.message;
            
            insertChat(message.from_user_id, message.text);
        });

    Echo.private('App.Message.' + to_user.id + '.' + from_user.id)
        .listen('ChatSent', (e) => {
            console.log(e);

            var message = e.message;
            
            insertChat(message.from_user_id, message.text);
        });

    $('#chat-user-form').on('submit', function(e) {
        e.preventDefault();

        var $this = $(this);
        var $text = $this.find('[name=text]');
        var text = $.trim($text.val());

        if ($text.hasClass('disabled')) {

        }

        if (! text) {
            $text.val('');
            return;
        }

        $text.addClass('disabled');

        $.ajax({
            type: 'POST',
            url: $this.attr('action'),
            data: $this.serialize(),
            error: function() {
                alertify.error("Can't send message! Please try again.", 10);
            },
            success: function(data) {
                if (! data.status) {
                    alertify.error("Can't send message! Please try again.", 10);
                    return;
                }

                $text.val('');
            },
            complete: function() {
                $text.removeClass('disabled');
            }
        });
    });
</script>
@endsection
