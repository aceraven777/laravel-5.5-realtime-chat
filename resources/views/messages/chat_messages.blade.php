@extends('layouts.app')

@section('content')

<div class="chatbox container">
    <div class="row">
        <div class="col-xs-12">
            <chatbox :to-user="{{ $to_user }}"></chatbox>
        </div>
    </div>
</div>
@endsection
