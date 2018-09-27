@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>    

    <div class="store-tablesection store-tableactive">

        <div class="container">
           
            <h4>Stores</h4>
            @if (!$stores->isEmpty())
            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>@lang('labels.store')</th>

                        <th class="text-center">@lang('labels.status')</th>

                        <th class="text-center">@lang('labels.product_sync')</th>

                        <th class="text-center">@lang('labels.settings')</th>

                    </tr>

                </thead>

                <tbody>
                
                    @foreach ($stores as $store)

                    <tr>

                        <td>

                            <a href="{{ url('/dashboard/store/'.$store->id.'/update') }}">

                            {{ $store->name }}

                            </a>

                        </td>

                        <td class="text-center">

                            @if ($store->isInSync())
                                @if ($store->shopifyWebhooksAreSetUp())
                                    <span class="badge badge-success">
                                        @lang('labels.active')
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        @lang('labels.pending')
                                    </span>
                                @endif
                            @else
                                <span class="badge badge-success">
                                    @lang('labels.active')
                                </span>
                            @endif
                        </td>

                        <td class="text-center">

                            <a href="{{ url('/dashboard/store/'.$store->id.'/sync/') }}" class="sync-add btn btn-default">

                                @if ($store->isInSync())
                                    @lang('actions.sync_add_product')
                                @else
                                    @lang('labels.add_product')
                                @endif                                                                                       

                            </a>

                        </td>

                        <td class="text-center">

                            <a href="{{ url('/dashboard/store/'.$store->id.'/update') }}" class="sync-add btn btn-default">

                            @lang('actions.edit')                                                                                                

                            </a>

                        </td>

                    </tr>
                    @endforeach
                </tbody>

            </table>
            @else
                <div class="alert">
                    <h3 class="text-center">
                        <div>
                            @lang('labels.welcome_to_printable')
                        </div>
                        <div>
                            @lang('labels.click_here_to_install_app', [
                                'a' => '<a href="'.url('/dashboard/store/connect').'">',
                                '/a' => '</a>'
                            ])
                        </div>
                        <div>
                            @lang('labels.click_here_to_schedule_call', [
                                'a' => '<a href="https://calendly.com/monetizesocial/store-setup/08-08-2017?back=1" target="_blank">',
                                '/a' => '</a>'
                            ])
                        </div>
                    </h3>
                </div>

            @endif
        </div>

    </div>

</home>

@endsection
