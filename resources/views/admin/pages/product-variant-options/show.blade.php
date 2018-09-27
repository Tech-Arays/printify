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
    <div class="col-xs-12 col-md-4">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-title">
                    @lang('labels.product_details')
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-8">
        <div class="box box-info">
            <div class="box-header">
                <div class="box-title">
                    @lang('labels.add_variations')
                </div>
            </div>
            <div class="box-body">
                {!! $form !!}
            </div>
        </div>
    </div>
</div>
@endsection