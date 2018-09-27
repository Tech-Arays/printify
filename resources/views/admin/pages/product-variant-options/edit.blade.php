@extends("admin.pages.default.edit")

@section("content")

<div class="row">
    <div class="col-xs-12 col-md-6 col-lg-6">
        <div class="box box-primary">
            <div class="box-body">
                {!! $form !!}
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
