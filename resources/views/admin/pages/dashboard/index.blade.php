@extends("admin.layouts.admin-layout")

@section("title")
    @lang("labels.dashboard")
@stop

@section("bodyClasses", "page")

@section("content")
    
    @include('admin.pages.dashboard.small-boxes')
 
@stop
