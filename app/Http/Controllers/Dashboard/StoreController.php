<?php

namespace App\Http\Controllers\Dashboard;

use App;
use Gate;
use App\Components\Logger;
use App\Components\Shopify;    
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\Store\CreateStoreFormRequest;
use App\Http\Requests\Dashboard\Store\UpdateStoreFormRequest;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreSettings;
use App\Models\Product;
use App\Jobs\Store\StoreUnconnectJob;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $stores = auth()->user()->stores;

        if ($request->is('*.json')) {
            return response()->api([
                'stores' => $this->serializeCollection(
                    $stores,
                    new StoreBriefTransformer
                )
            ]);
        }

        else {

            if (!$request->has('first') && session()->has('tour')) {
                session([
                    'firstConnect' => false
                ]);
            }

            return view('dashboard.store.index', [
                'stores' => $stores,
                'firstConnect' => session('firstConnect'),
                'tour' => session('tour')
            ]);
        }
        
    }

    public function syncView(Request $request,$id){

        $store = Store::findStoreWithRelations($id);

        if (Gate::denies('edit', $store)) {
            return abort(403, trans('messages.not_authorized_to_access_store'));
        }

        return view('dashboard.store.sync', [
            'store' => $store,
            'tour' => session('tour'),
            'tourLastStep' => session('tourLastStep')
        ]);

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateStoreFormRequest $request)
    {
        $store = new Store();
        if (Gate::denies('create', $store)) {
            return abort(403, trans('messages.not_authorized_to_create_store'));
        }

        $name = filter_var($request->get('name'), FILTER_SANITIZE_STRING);
        $store->createStore($name);

        return $this->returnSuccess(trans('messages.store_created'));
    }

    public function updateView(Request $request, $store_id)
    {
        $store = Store::find($store_id);
        if (Gate::denies('edit', $store)) {
            return abort(403, trans('messages.not_authorized_to_access_store'));
        }

        return view('pages.dashboard.store.update', [
            'store' => $store
        ]);
    }
    

    /**
     * Shopify webhooks endpoint
    */

    public function webhook(Request $request)
    {
        $json = $request->json();
        $isValid = Shopify::verifyWebhook($request->getContent(), $request->server('HTTP_X_SHOPIFY_HMAC_SHA256'));

        // we will log all webhooks
        Logger::i(Logger::WEBHOOK_APP)->notice($request->getContent());

        if ($isValid) {
            $topic = $request->server('HTTP_X_SHOPIFY_TOPIC');
            $domain = filter_var($request->server('HTTP_X_SHOPIFY_SHOP_DOMAIN'), FILTER_SANITIZE_STRING);
            $stores = Store::findByDomain($domain);

            switch($topic) {
                case Shopify::WEBHOOK_TOPIC_APP_UNINSTALLED:

                    foreach ($stores as $store) {
                        Shopify::i($store->shopifyDomain(), $store->access_token)->removeAllWebhooks();
                    }

                    break;
            }


        }
    }

}
