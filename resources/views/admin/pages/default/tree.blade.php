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
            <div class="box-header">
                <div class="box-title">
                    @lang('labels.categories')
                </div>
                <div class="pull-right box-tools">
                    <a href="{{ url('/admin/catalog-categories/add') }}" class="btn btn-primary">
                        @lang('actions.add_category')
                    </a>
                </div>
            </div>
            <div class="box-body">
                {!! $tree !!} 
            </div>
        </div>
    </div>
</div>


@stop
