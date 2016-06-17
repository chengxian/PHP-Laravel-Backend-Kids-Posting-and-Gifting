@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Invite User(Beta Version)</div>

                <div class="panel-body">
                    <form class="" role="form" method="POST" action="{{ url("/$slug") }}">
                        {!! csrf_field() !!}
                        <div class="form-group row @if( isset($errors)){{ $errors->has('email') ? ' has-error' : '' }}@endif">
                            <div class="col-sm-4">
                                <label class="control-label">Email Address</label>
                            </div>
                            <div class="col-sm-6">
                                <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-envelope"></i>Invite
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
