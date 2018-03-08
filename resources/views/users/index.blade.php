@extends('layouts.app')

@section('content')
<div class="container">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>
                        <a href="{{ route('users.chat-messages', [$user->id]) }}" class="btn btn-primary" role="button">Chat User</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-center">
        {!! $users->links() !!}
    </div>
</div>
@endsection