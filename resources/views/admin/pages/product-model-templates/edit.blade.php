@extends("admin.pages.default.edit")

@section("content")

<div class="row">
    <div class="col-xs-12 col-md-8 col-lg-6">
        <div class="box box-primary">
            <div class="box-body">
                {!! $form !!}
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-8 col-lg-6">


        <div class="box box-default">
            <div class="box-header ta-c">
                <h3 class="box-title">@lang('labels.product_variants')</h3>
                {!! BootForm::open()
                    ->action('/admin/product-models/'.$formObject->model->id.'/pull-variants')
                    ->post() !!}
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-refresh"></i>
                        @lang('actions.sync_product_variants')
                    </button>
                    <a href='/admin/product-variants/{{ $formObject->model->id }}/add' class="btn btn-success">
                        <i class="fa fa-plus"></i>
                        @lang('actions.add_product_variants')
                    </a>
                {!! BootForm::close() !!}
            </div>
            {!! BootForm::open()->post() !!}
                <div class="box-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>
                                    @lang('labels.options')
                                </th>
                                <th>
                                    @lang('labels.inventory_status')
                                </th>
                                <th>
                                    @lang('labels.prices')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($formObject->model->models as $model)
                                <tr>
                                    <td>
                                        #{{ $model->id }}
                                    </td>
                                    <td>
                                        <div>@lang('labels.color'): {{ $model->getColorOption() ? $model->getColorOption()->name : '' }}</div>
                                        <div>@lang('labels.size'): {{ $model->getSizeOption() ? $model->getSizeOption()->name : '' }}</div>
                                    </td>
                                    <td>
                                        {!! BootForm::select(trans('labels.choose'), 'inventory_statuses['.$model->id.']')
                                            ->options(\App\Models\ProductModel::listInventoryStatuses())
                                            ->select($model->inventory_status)
                                            ->label(false)
                                        !!}
                                        @if($model->inventory_status == \App\Models\ProductModel::INVENTORY_STATUS_OUT_OF_STOCK)
                                            <i class="fa fa-exclamation-triangle color-warning" title="@lang('labels.out_of_stock')"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            @lang('labels.price'): @price($model->frontPrice())

                                            @if(!$model->price)
                                                <i class="fa fa-exclamation-triangle color-warning" title="@lang('labels.price_not_set')"></i>
                                            @endif
                                        </div>
                                        <div>@lang('labels.back_price'): @price($model->backPrice())</div>
                                        <div>@lang('labels.both_sides_price'): @price($model->bothSidesPrice())</div>
                                    </td>
                                </tr>
                                
                                @empty
                                   
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="box-footer">
                    {!! BootForm::submit(trans('actions.save'))
                        ->attribute('class', 'btn btn-primary') !!}
                </div>
            {!! BootForm::close() !!}
        </div>

        <div class="box box-default">
            <div class="box-header ta-c">
                <h3 class="box-title">@lang('labels.price_modifiers')</h3>
            </div>
            <div class="box-body">
                <price-modifiers :is-on-user-page="false"></price-modifiers>
            </div>
        </div>

    </div>
</div>


@stop

@section('scripts')
    <script>
        App.data.ProductModelTemplate = {!! json_encode($formObject->model->transformBrief()) !!};
        App.data.Users = {!! json_encode($users) !!};
        App.data.PriceModifiers = {!! json_encode($priceModifiers) !!};
    </script>
@append
