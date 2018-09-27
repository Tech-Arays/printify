<div class="row">
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>120</h3>
                <p>@lang('labels.orders')</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="{{ url('/admin/orders') }}" class="small-box-footer">
                @lang('actions.more_info') <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <h3>10</h3>
                <p>@lang('labels.stores')</p>
            </div>
            <div class="icon">
                <i class="ion ion-shop"></i>
            </div>
            <a href="{{ url('/admin/users') }}" class="small-box-footer">
                @lang('actions.more_info') <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>3</h3>
                <p>@lang('labels.user_registrations')</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="{{ url('/admin/users') }}" class="small-box-footer">
                @lang('actions.more_info') <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <h3>5</h3>
                <p>@lang('labels.products')</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{ url('/admin/products') }}" class="small-box-footer">
                @lang('actions.more_info') <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
</div>
