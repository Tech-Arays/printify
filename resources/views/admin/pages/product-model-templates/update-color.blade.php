@extends("admin.pages.default.edit")

@section("content")
    
<update-color-form inline-template="true">
<div class="row">
    <div class="col-xs-12 col-md-8 col-lg-6">
        <div class="box box-primary">
            <div class="box-body" 
                {!! $form !!}
            </div>
        </div>
    </div>
</div>
</update-color-form>

@stop

@section('footer')
    <script>
        App.data.CurrentForm = {!! json_encode($formObject->model->toArray()) !!};
    </script>
@append
