@extends('beautymail::templates.sunny')

@section("content")
    
    @include ('beautymail::templates.sunny.heading' , [
        'heading' => trans('labels.click_to_reset_password'),
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        

    @include('beautymail::templates.sunny.contentEnd')

    @include('beautymail::templates.sunny.button', [
        'title' => trans('actions.reset'),
        'link' => url('password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset())
    ])

@stop



