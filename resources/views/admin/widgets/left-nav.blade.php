<ul class="sidebar-menu tree" data-widget="tree">
    <li>
        <a  href="{{ url('/') }}">
            <i class="fa fa-arrow-left"></i>
            <i class="fa fa-home"></i>
            <span>@lang('labels.home')</span>
        </a>
    </li>

    <li class="{{ Request::is('admin') ? 'active' : '' }}">
        <a  href="{{ url('/admin') }}">
            <i class="fa fa-dashboard"></i>
            <span>@lang('labels.dashboard')</span>
        </a>
    </li>

    <li class="treeview {{ Request::is('admin/users*') || Request::is('admin/products*') || Request::is('admin/product-variants*') || Request::is('admin/stores*') ? 'active' : '' }}">
        <a href="{{ url('/admin/users') }}">
            <i class="fa fa-user"></i>
            <span>@lang('labels.users')</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>

        <ul class="treeview-menu">
            <li class="{{ Request::is('admin/users') ? 'active' : '' }}">
                <a href="{{ url('/admin/users') }}">
                    <i class="fa fa-list"></i>
                    <span>@lang('actions.list')</span>
                </a>
            </li>
            <li class="{{ Request::is('admin/users/add') ? 'active' : '' }}">
                <a href="{{ url('/admin/users/add') }}">
                    <i class="fa fa-user-plus"></i>
                    <span>@lang('actions.add')</span>
                </a>
            </li>

            <li class="{{ Request::is('admin/stores*') ? 'active' : '' }}">
                <a href="{{ url('/admin/stores') }}">
                    <i class="fa fa-shopping-basket"></i>
                    <span>@lang('actions.stores')</span>
                </a>
            </li>

            <li class="{{ Request::is('admin/products*') || Request::is('admin/product-variants*') ? 'active' : '' }}">
                <a href="{{ url('/admin/products') }}">
                    <i class="fa fa-shopping-bag"></i>
                    <span>@lang('actions.products')</span>
                </a>
            </li>

            <li class="{{ Request::is('admin/product?moderation_status=on_moderation*') ? 'active' : '' }}">
                <a class="d-ib ml-10" href="{{ url('/admin/products?moderation_status=on_moderation&search=1') }}">
                    <i class="fa fa-check"></i>
                    <span>@lang('actions.on_moderation')</span>
                    <span class="label label-success pull-right">50</span>
                </a>
            </li>
           
            <li class="{{ Request::is('admin/product?moderation_status=auto_approved*') ? 'active' : '' }}">
                <a class="d-ib ml-10" href="{{ url('/admin/products?moderation_status=auto_approved&search=1') }}">
                    <i class="fa fa-check"></i>
                    <span>@lang('actions.auto_approved')</span>
                    <span class="label label-success pull-right">80</span>
                </a>
            </li>
            
        </ul>
    </li>

    <li class="treeview {{
        Request::is('admin/catalog-categories*')
        || Request::is('admin/catalog-attributes*')
        || Request::is('admin/garment-groups*')
        || Request::is('admin/garments*')
            ? 'active'
            : ''
        }}">
        <a href="{{ url('/admin/catalog-categories') }}">
            <i class="fa fa-sitemap"></i>
            <span>@lang('labels.categories')</span>
        </a>

        <ul class="treeview-menu">
            <li class="{{ Request::is('admin/catalog-categories*') ? 'active' : '' }}">
                <a href="{{ url('/admin/catalog-categories') }}">
                    <i class="fa fa-sitemap"></i>
                    <span>@lang('labels.categories')</span>
                </a>
            </li>
            <li class="{{ Request::is('admin/catalog-attributes*') ? 'active' : '' }}">
                <a href="{{ url('/admin/catalog-attributes') }}">
                    <i class="fa fa-tags"></i>
                    <span>@lang('actions.catalog_attributes')</span>
                </a>
            </li>
            <li class="{{ Request::is('admin/garment-groups*') ? 'active' : '' }}">
                <a href="{{ url('/admin/garment-groups') }}">
                    <i class="fa fa-tags"></i>
                    <span>@lang('actions.garment_groups')</span>
                </a>
            </li>
            <li class="{{ Request::is('admin/garments*') ? 'active' : '' }}">
                <a href="{{ url('/admin/garments') }}">
                    <i class="fa fa-tags"></i>
                    <span>@lang('actions.garments')</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="treeview {{ Request::is('admin/product-model*') ? 'active' : '' }}">
        <a href="{{ url('/admin/product-models') }}">
            <i class="fa fa-cubes"></i>
            <span>@lang('labels.product_models')</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>

        <ul class="treeview-menu">
            <li class="{{ Request::is('admin/product-models') ? 'active' : '' }}">
                <a href="{{ url('/admin/product-models') }}">
                    <i class="fa fa-list"></i>
                    <span>@lang('actions.list')</span>
                </a>
            </li>
            
            <li class="{{ Request::is('admin/product-models/available') ? 'active' : '' }}">
                <a class="d-ib ml-10" href="{{ url('/admin/product-models/available') }}">
                    <i class="fa fa-check"></i>
                    <span>@lang('actions.available')</span>
                    <span class="label label-success pull-right">500</span>
                </a>
            </li>
            
            <li class="{{ Request::is('admin/product-models/complete') ? 'active' : '' }}">
                <a class="d-ib ml-10" href="{{ url('/admin/product-models/complete') }}">
                    <i class="fa fa-check"></i>
                    <span>@lang('actions.complete')</span>
                    <span class="label label-success pull-right">100</span>
                </a>
            </li>
            
            <li class="{{ Request::is('admin/product-models/incomplete/source-templates') ? 'active' : '' }}">
                <a class="d-ib ml-10" href="{{ url('/admin/product-models/incomplete/source-templates') }}">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>@lang('actions.incomplete_source_templates')</span>
                    <span class="label label-warning pull-right">890</span>
                </a>
            </li>
            
            <li class="{{ Request::is('admin/product-models/incomplete/overlays') ? 'active' : '' }}">
                <a class="d-ib ml-10" href="{{ url('/admin/product-models/incomplete/overlays') }}">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>@lang('actions.incomplete_overlays')</span>
                    <span class="label label-warning pull-right">86</span>
                </a>
            </li>
            
            <li class="{{ Request::is('admin/product-models/incomplete/prices') ? 'active' : '' }}">
                <a class="d-ib ml-10" href="{{ url('/admin/product-models/incomplete/prices') }}">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>@lang('actions.incomplete_prices')</span>
                    <span class="label label-warning pull-right">25</span>
                </a>
            </li>
            
            <li class="{{ Request::is('admin/product-models/pull-product-model-templates') ? 'active' : '' }}">
                <a href="{{ url('/admin/product-models/pull-product-model-templates') }}">
                    <i class="fa fa-cloud-download"></i>
                    <span>@lang('actions.import')</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="{{ Request::is('admin/variant-options*') ? 'active' : '' }}">

        <a href="{{ url('/admin/variant-options') }}">
            <i class="fa fa-tags"></i>
            <span>@lang('labels.variants')</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>

    </li>

    <li class="treeview {{ Request::is('admin/orders*') ? 'active' : '' }}">
        <a href="{{ url('/admin/orders') }}">
            <i class="fa fa-shopping-bag"></i>
            <span>@lang('labels.orders')</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>

        <ul class="treeview-menu">
            <li class="{{ Request::is('admin/orders') ? 'active' : '' }}">
                <a href="{{ url('/admin/orders') }}">
                    <i class="fa fa-list"></i>
                    <span>@lang('actions.all')</span>
                    <span class="label label-default pull-right">500</span>
                </a>
            </li>
            
            <li class="{{ Request::is('admin/orders/refunds') ? 'active' : '' }}">
                <a href="{{ url('/admin/orders/refunds') }}">
                    <i class="fa fa-list"></i>
                    <span>@lang('actions.refunds')</span>
                    <span class="label label-default pull-right">100</span>
                </a>
            </li>
            
            <li class="{{ Request::is('admin/orders/without-shipping-groups') ? 'active' : '' }}">
                <a href="{{ url('/admin/orders/without-shipping-groups') }}">
                    <i class="fa fa-list"></i>
                    <span>@lang('actions.without_shipping_groups')</span>
                    <span class="label label-danger pull-right">325</span>
                </a>
            </li>
           
            <li class="{{ Request::is('admin/orders/not-sent-to-kz-api') ? 'active' : '' }}">
                <a href="{{ url('/admin/orders/not-sent-to-kz-api') }}">
                    <i class="fa fa-list"></i>
                    <span>@lang('labels.not_sent_to_kz')</span>
                    <span class="label label-danger pull-right">101</span>
                </a>
            </li>
            
            <li class="{{ Request::is('admin/orders/shipping') ? 'active' : '' }}">
                <a href="{{ url('/admin/orders/shipping') }}">
                    <i class="fa fa-truck"></i>
                    <span>@lang('actions.shipping_groups')</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="treeview {{ Request::is('admin/support*') ? 'active' : '' }}">
        <a href="{{ url('/admin/support/tickets') }}">
            <i class="fa fa-envelope"></i>
            <span>@lang('labels.support')</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>

        <ul class="treeview-menu">
            <li class="{{ Request::is('admin/support/tickets') ? 'active' : '' }}">
                <a href="{{ url('/admin/support/tickets') }}">
                    <i class="fa fa-list"></i>
                    <span>@lang('actions.tickets')</span>
                </a>
            </li>
            <li class="{{ Request::is('admin/support/tickets/new') ? 'active' : '' }}">
                <a href="{{ url('/admin/support/tickets/new') }}">
                    <i class="fa fa-fire"></i>
                    <span>@lang('actions.new_tickets')</span>
                 
                    <span class="pull-right-container">
                        <small class="label bg-green pull-right">
                            256
                        </small>
                    </span>
                     
                </a>
            </li>

            <li class="{{ Request::is('admin/support/refunds') ? 'active' : '' }}">
                <a href="{{ url('/admin/support/refunds') }}">
                    <i class="fa fa-dollar"></i>
                    <span>@lang('actions.refund_requests')</span>
                </a>
            </li>
            <li class="{{ Request::is('admin/support/refunds/new') ? 'active' : '' }}">
                <a href="{{ url('/admin/support/refunds/new') }}">
                    <i class="fa fa-fire"></i>
                    <span>@lang('actions.new_refund_requests')</span>
                
                    <span class="pull-right-container">
                        <small class="label bg-green pull-right">
                           523
                        </small>
                    </span>
                     
                </a>
            </li>

            {{-- <li class="{{ Request::is('admin/support/tickets/pending') ? 'active' : '' }}">
                <a href="{{ url('/admin/support/tickets/pending') }}">
                    <i class="fa fa-clock-o"></i>
                    <span>@lang('actions.pending_tickets')</span>

                    @if ($pendingTicketsCount)
                        <span class="pull-right-container">
                            <small class="label bg-green">
                                {{ $pendingTicketsCount }}
                            </small>
                        </span>
                    @endif
                </a>
            </li> --}}
        </ul>
    </li>

    <li class="treeview {{ Request::is('admin/dev*') || Request::is('spark/kiosk') ? 'active' : '' }}">
        <a href="{{ url('/admin/dev') }}">
            <i class="fa fa-cogs"></i>
            <span>@lang('labels.developers')</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>

        <ul class="treeview-menu">
            <li class="{{ Request::is('admin/dev/beanstalkd*') ? 'active' : '' }}">
                <a href="{{ url('/admin/dev/beanstalkd') }}">
                    <i class="fa fa-list"></i>
                    <span>Beanstalkd</span>
                </a>
            </li>
            <li class="{{ Request::is('spark/kiosk') ? 'active' : '' }}">
                <a  href="{{ url('/spark/kiosk') }}">
                    <i class="fa fa-fort-awesome"></i>
                    <span>@lang('labels.kiosk')</span>
                </a>
            </li>
            <li class="{{ Request::is('admin/dev/hashids*') ? 'active' : '' }}">
                <a href="{{ url('/admin/dev/hashids') }}">
                    <i class="fa fa-i-cursor"></i>
                    <span>@lang('labels.hashids_encoder')</span>
                </a>
            </li>
        </ul>
    </li>

</ul>
