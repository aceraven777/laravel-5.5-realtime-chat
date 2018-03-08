@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent

<script type="text/javascript">
    Echo.private('App.User.1')
        .listen('UserRegistered', (e) => {
            console.log(e);
            // console.log(e.order.name);
        });
</script>
@endsection
