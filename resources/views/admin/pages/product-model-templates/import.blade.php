@extends("admin.layouts.admin")

@section("title")
    @if (!empty($subtitle))
        {{ $subtitle }} -
    @endif
    {{ $title }}
@stop

@section("bodyClasses", "page")

@section("content")

<div class="row">
    <div class="col-xs-12 col-md-8">
        <div class="box box-primary">
            <div class="box-header ta-c">
                {!! BootForm::open()
                    ->action('/admin/product-models/pull-product-model-templates')
                    ->post() !!}
                    <button type="submit" class="btn btn-info" {{ $processing ? 'disabled="true"' : '' }}>
                        <i class="fa fa-cloud-download"></i>
                        @lang('labels.import_new_products')
                    </button>
                {!! BootForm::close() !!}

                <div class="ta-c mt-10">
                    @if ($processing)
                        <div class="d-ib">
                            <div class="la-ball-spin color-primary"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                        </div>

                        @lang('labels.import_will_take_time')

                        <a
                            class="btn btn-default"
                            href="{{ url('/admin/product-models/pull-product-model-templates') }}">
                                <i class="fa fa-refresh"></i>
                                @lang('labels.refresh')
                        </a>
                    @endif
                </div>
            </div>
            <div class="box-body">
                <h3>@lang('labels.debug_output')</h3>
                <pre>{{ $output }}</pre>
            </div>
        </div>
    </div>
</div>

@stop
