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
    <div class="col-xs-12">
        <div class="box box-primary">
            @if (!empty($filter))
                <div class="box-header">
                    {!! $filter !!}
                </div>
            @endif
            <div class="box-body table-responsive">
                {!! $grid !!}
            </div>
            @if (!empty($footer))
                <div class="box-footer">
                    {!! $footer !!}
                </div>
            @endif
        </div>
    </div>
</div>


@stop
