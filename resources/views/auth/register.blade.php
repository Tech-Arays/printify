@extends('layouts.app')

@section('title')
    @lang('labels.registration')
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-default">
                <div class="card-header bg-light">
                    @lang('labels.registration')
                </div>
                <div class="card-body bg-white">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                        <input type="hidden" name="plan" value="{{ request()->get('plan') }}">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} row">
                            <label class="col-md-4 col-form-label text-md-right">
                                @lang('labels.email')
                            </label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ session('shop_email') ?: old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
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

                        <div class="form-group">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i>
                                    @lang('actions.sign_up')
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
