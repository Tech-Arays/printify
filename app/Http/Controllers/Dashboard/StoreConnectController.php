<?php

namespace App\Http\Controllers\Dashboard;
use Illuminate\Http\Request;
use Auth;
use Gate;
use Input;
use Session;
use Exception;
use Log;
use Bugsnag;

use App\Http\Controllers\Controller;
use App\Components\Shopify;
use App\Http\Requests\Dashboard\Store\StoreConnectInitiateFormRequest;
use App\Http\Requests\Dashboard\Store\StoreConnectConfirmFormRequest; 
use App\Models\Store;
use App\Jobs\Store\StoreConnectJob;

class StoreConnectController extends Controller
{
    
    const ACCOUNT_TYPE_NEW = 'new';
    const ACCOUNT_TYPE_EXISTING = 'existing';
    const ACCOUNT_TYPE_CURRENT = 'current';

    public function initiate(StoreConnectInitiateFormRequest $request){
       
        $shopDomain = $request->get('shop');

        $token = $request->get('hmac');

        $installURL = Shopify::i($shopDomain, $token)->api
            ->installURL([
                'permissions' => config('services.shopify.permissions'),
                'redirect' => url('/dashboard/store/connect/shopify/confirm')
            ]);

        return redirect($installURL);
    }

    public function confirm(StoreConnectConfirmFormRequest $request){
        
        $shopDomain = $request->get('shop');
        $shopDomain = Shopify::getMyshopifyDomain($shopDomain);

        try {
            $verify = Shopify::i($shopDomain)->api->verifyRequest(Input::all());
            $code = $request->get('code');
            
            if ($verify && $code) {
                
                $accessToken = Shopify::i($shopDomain)->api->getAccessToken($code);
                
                $call = Shopify::i($shopDomain, $accessToken)->getShop();
               
                Store::saveTemporaryShopifyStore($call->shop, $accessToken);
                
                if (Auth::guest()) {

                    if (!empty($call->shop->email)) {
                        session()->put('shop_email', $call->shop->email);
                    }

                    return redirect('/dashboard/store/connect/connect-to-account/new');
                } else {
                    return redirect('/dashboard/store/connect/connect-to-account/existing');
                }
            }
            else {
                return abort(400, trans('messages.cannot_verify_oauth'));
            }

        }
        catch (Exception $e) {
    
            Log::error($e);
            
            Bugsnag::notifyException($e);

            return abort(500, trans('messages.unexpected_server_error').': '.$e->getMessage());
        }
    }

    public function connectToAccount(Request $request, $account_type = null)
    {

        if (!Session::has('preparedStore')) {
            return redirect('/dashboard/store/connect');
        }
        
        $preparedStore = Session::get('preparedStore');
        $storeExistsForCurrentUser = auth()->check()
            ? (
                $preparedStore
                && Store::shopExistsForCurrentUser($preparedStore->domain)
            )
            : false;

        $otherUsersStores = null;
        $storeExistsForOtherUsers = false;
        if (auth()->check()) {
            $otherUsersStores = Store::getStoresByDomainExceptCurrentUser(
                $preparedStore->domain
            );
            $storeExistsForOtherUsers = (
                $preparedStore
                && !$otherUsersStores->isEmpty()
            );
        }

        if (Auth::check()) {
            $account_type = static::ACCOUNT_TYPE_CURRENT;
        }

        if ($account_type) {
            switch ($account_type) {
                case static::ACCOUNT_TYPE_NEW:
                    $redirectUrl = url('/dashboard/store/connect/connect-to-account');
                    Session::put('redirectToAfterAuth', $redirectUrl);
                    Session::put('url.intended', $redirectUrl);
                    Auth::logout();
                    return redirect('/register');

                case static::ACCOUNT_TYPE_EXISTING:
                    $redirectUrl = url('/dashboard/store/connect/connect-to-account');
                    Session::put('redirectToAfterAuth', $redirectUrl);
                    Session::put('url.intended', $redirectUrl);
                    Auth::logout();
                    return redirect('/login');

                case static::ACCOUNT_TYPE_CURRENT:

                    if (!auth()->check()) {
                        return redirect()->back();
                    }

                    if (
                        $storeExistsForOtherUsers
                        && config('settings.store.connect_mode') == Store::CONNECT_MODE__UNIQUE_REPLACE
                    ) {
                        $otherUsersStores->each(function($store) {
                            $store->delete();
                        });
                    }

                    if ($storeExistsForCurrentUser) {
                        
                        $store = Store::findByDomainForCurrentUser($preparedStore->domain);
                        $store->access_token = $preparedStore->access_token;
                        $store->save();
                    }
                    else {
                        $preparedStore->createStore();
                        $store = $preparedStore;
                    }

                    if(auth()->user()->stores()->count() == 1) {
                        session([
                            'firstConnect' => true,
                            'tour' => true
                        ]);
                    }

                    // create webhooks
                    $this->dispatch(new StoreConnectJob($store));

                    return redirect('/dashboard/store?first=1');
            }
        }

        return view('dashboard.store.connect-to-account', [
            'preparedStore' => $preparedStore,
            'storeExistsForOtherUsers' => $storeExistsForOtherUsers
        ]);
    }

}
