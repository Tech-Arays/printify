@extends('layouts.app')

@section('title')
    @lang('labels.login')
@stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="card card-default">
                <div class="card-header bg-light">@lang('labels.login')</div>
                <div class="card-body bg-white">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        {!! csrf_field() !!}
                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }} row">
                            <label class="col-md-4 control-label text-md-right">
                                @lang('labels.username')
                            </label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="username" value="{{ old('username') }}">

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} row">
                            <label class="col-md-4 control-label text-md-right">
                                @lang('labels.password')
                            </label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" />
                                        @lang('labels.remember_me')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-sign-in"></i>
                                    @lang('actions.login')
                                </button>

                                <a class="btn btn-link" href="{{ url('/password/reset') }}">
                                    @lang('actions.forgot_password')
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
