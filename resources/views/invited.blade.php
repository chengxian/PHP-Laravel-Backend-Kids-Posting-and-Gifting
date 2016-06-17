@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Successfully Invited {{$email}}</div>

                <div class="panel-body">
                    <a href="{{ url('/invite-user') }}" class="btn btn-primary">Invite another user</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
