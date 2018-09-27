@extends("admin.layouts.admin-layout")

@section("title")
    @if (!empty($subtitle))
        {{ $subtitle }} - 
    @endif
    {{ $title }}
@stop

@section("bodyClasses", "page")

@section("content")
    
<div class="row">
    <div class="col-xs-12 col-md-8 col-lg-6">
        <div class="box box-primary">
            <div class="box-body">
                {!! $form !!} 
            </div>
        </div>
    </div>
</div>


@stop
