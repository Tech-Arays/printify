@extends('layouts.app')

@section('title')
    {{ $store->name }} - @lang('labels.product_list')
@stop

@section('bodyClasses', 'page')

@section('content')
    
    <store-sync inline-template>
        <div class="container">
            <nav aria-label="breadcrumb">
                
                <ul class="breadcrumb bg-light">

                    <li class="breadcrumb-item"><a href="{{ url('/dashboard/store') }}">@lang('actions.stores')</a></li>

                    <li class="breadcrumb-item active">{{ $store->name }}</li>

                </ul>

            </nav>

            <div class="pull-right">
                <a href="{{ url('/dashboard/store/'.$store->id.'/update') }}" class="btn btn-link">
                    <span class="fa fa-cog"></span>
                    @lang('actions.settings')
                </a>
                @if (getenv('TURN_ON_FEATURE__PULL_PRODUCTS_FROM_PROVIDER'))
                    <a href="{{ url('/dashboard/store/'.$store->id.'/reload') }}" class="btn btn-link">
                        <span class="fa fa-refresh"></span>
                        @lang('actions.refresh_data')
                    </a>
                @endif
            </div>

            <div class="clearfix"></div>

            <h5 class="mt-5">{{ $store->name }}</h5>

            <div class="card bg-light py-3" id="modal-spark">

                <div class="card-body text-center">
                
                    <button type="button" class="btn btn-warning btn-lg text-white px-5 py-1" @click="showAddProductModal = true">

                    Add product

                    </button>

                    @include('widgets.dashboard.product.add-product-modal')    
                </div>

            </div>

            @if (!$store->products)
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <p class="mb-10">
                                To start accepting orders you need to <b>upload the print files</b> for the
                                products you wish for the Printable to process.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <section id="tabs">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 ">
                            
                            <tabs v-model="activeTab" nav-style="tabs" class=" nav-link store-tabs printfile-tabs" justified>
                                <tab class="show" header="@lang('actions.synced') {{ ($store->vendorProductsSynced)
                                    ? '('.count($store->vendorProductsSynced).')'
                                    : ''
                                    }}">
                                    @include('dashboard.store.sync.synced-tab', [
                                        'products' => $store->vendorProductsSynced
                                    ])
                                </tab>
                                <tab class="show" header="@lang('actions.pending') {{ ($store->vendorProductsPending)
                                    ? '('.count($store->vendorProductsPending).')'
                                    : ''
                                    }}">
                                    @include('dashboard.store.sync.pending-tab', [
                                        'products' => $store->vendorProductsPending
                                    ])
                                </tab>
                            </tabs>
                        </div>    
                    </div>    
                </div>
            </section>   
        </div>         
    </store-syn>

@endsection
