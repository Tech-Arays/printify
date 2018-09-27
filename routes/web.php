<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@show');

//Route::get('/home', 'HomeController@show');
Auth::routes();
Route::group(['middleware' => 'members'], function () {

    Route::group(['prefix' => 'dashboard','namespace'=>'Dashboard','as' => 'dashboard.'], function () {
        Route::get('/', function() {
            
            return redirect('/dashboard/store');
        
        });
        /*Store routes*/

        Route::get('/store', 'StoreController@index');

        Route::get('/store/{store_id}/sync', 'StoreController@syncView');

        Route::get('/store/{store_id}/update', 'StoreController@updateView');

        Route::post('/store/{store_id}/update', 'StoreController@update');

    });

});  

Route::group(['namespace' => 'Dashboard', 'prefix' => 'dashboard'], function () {
    // connect store
    // shopify Application URL
    Route::get('/store/connect/{provider}/initiate', 'StoreConnectController@initiate');

    // shopify Redirection URL
    Route::get('/store/connect/{provider}/confirm', 'StoreConnectController@confirm');

    // redirected to this after confirm
    Route::get('/store/connect/connect-to-account', 'StoreConnectController@connectToAccount');

    // account to connect shop is selected, will connect
    Route::get('/store/connect/connect-to-account/{account_type}', 'StoreConnectController@connectToAccount');
});

/*** Admin ***/
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['admin']], function () {
   
    Route::match(['get'], '/', ["as" => "admin", "uses" => "DashboardController@index"]);
   
    Route::match(['get'], 'users', ["as" => "users", "uses" => "UsersController@all"]);

    /*Category Routes*/
    Route::match(['get'], 'catalog-categories', ["as" => "get_categories", "uses" => "CatalogCategoriesController@all"]);
   
    Route::match(['post'], 'catalog-categories', ["as" => "post_categories", "uses" => "CatalogCategoriesController@saveOrder"]);
    
    Route::match(['get'], 'catalog-categories/add', ["as" => "get_add_categories", "uses" => "CatalogCategoriesController@add"]);
    
    Route::match(['post'], 'catalog-categories/add', ["as" => "post_add_categories", "uses" => "CatalogCategoriesController@add"]);
    
    Route::match(['get'], 'catalog-categories/edit', ["as" => "get_edit_category", "uses" => "CatalogCategoriesController@edit"]);
    
    Route::match(['post'], 'catalog-categories/edit', ["as" => "post_edit_category", "uses" => "CatalogCategoriesController@edit"]);
    
    Route::match(['get','post'], 'catalog-categories/delete', ["as" => "get_delete_category", "uses" => "CatalogCategoriesController@delete"]);

    /*Catalog Attributes*/
    
    Route::match(['get'], 'catalog-attributes', ["as" => "get_categories", "uses" => "CatalogAttributesController@all"]);
    
    Route::match(['get'], 'catalog-attributes/add', ["as" => "get_add_categories", "uses" => "CatalogAttributesController@add"]);
    
    Route::match(['post'], 'catalog-attributes/add', ["as" => "post_add_categories", "uses" => "CatalogAttributesController@add"]);
    
    Route::match(['get'], 'catalog-attributes/{id}/edit', ["as" => "get_edit_category", "uses" => "CatalogAttributesController@edit"]);
    
    Route::match(['post'], 'catalog-attributes/{id}/edit', ["as" => "post_edit_category", "uses" => "CatalogAttributesController@edit"]);
    
    Route::match(['get','post'], 'catalog-attributes/{id}/delete', ["as" => "get_delete_category", "uses" => "CatalogAttributesController@delete"]);

    // catalog attribute options

    Route::match(['get'], 'catalog-attributes/{attribute_id}/options', ["as" => "get_categories", "uses" => "CatalogAttributeOptionsController@getByAttribute"]);
    
    Route::match(['get'], 'catalog-attributes/{attribute_id}/options/add', ["as" => "get_add_categories", "uses" => "CatalogAttributeOptionsController@add"]);
    
    Route::match(['post'], 'catalog-attributes/{attribute_id}/options/add', ["as" => "post_add_categories", "uses" => "CatalogAttributeOptionsController@add"]);
    
    Route::match(['get'], 'catalog-attributes/{attribute_id}/options/{id}/edit', ["as" => "get_edit_category", "uses" => "CatalogAttributeOptionsController@edit"]);
    
    Route::match(['post'], 'catalog-attributes/{attribute_id}/options/{id}/edit', ["as" => "post_edit_category", "uses" => "CatalogAttributeOptionsController@edit"]);

    Route::match(['get'], 'catalog-attributes/{attribute_id}/options/{id}/delete', ["as" => "delete_category", "uses" => "CatalogAttributeOptionsController@delete"]);
    
    // garment group
    Route::match(['get'], 'garment-groups', ["as" => "garment_groups", "uses" => "GarmentGroupsController@all"]);
    Route::match(['get'], 'garment-groups/{id}/edit', ["as" => "get_garment_groups", "uses" => "GarmentGroupsController@edit"]);
    Route::match(['post'], 'garment-groups/{id}/edit', ["as" => "post_garment_groups", "uses" => "GarmentGroupsController@edit"]);
    
    Route::match(['get'], 'garments', ["as" => "garment_groups", "uses" => "GarmentsController@all"]);
    Route::match(['get'], 'garments/{id}/edit', ["as" => "get_garment_edit", "uses" => "GarmentsController@edit"]);
    Route::match(['post'], 'garments/{id}/edit', ["as" => "post_garment_edit", "uses" => "GarmentsController@edit"]);
    
    // product models
    Route::get('product-models', 'ProductModelTemplatesController@all');
    Route::get('product-models/add', 'ProductModelTemplatesController@add');
    Route::post('product-models/add', 'ProductModelTemplatesController@add');
    Route::get('product-models/{id}/edit', 'ProductModelTemplatesController@edit');
    Route::post('product-models/{id}/edit', 'ProductModelTemplatesController@edit');
    Route::get('product-models/{id}/delete', 'ProductModelTemplatesController@delete');

    //Add Product variants
    Route::get('product-variants/{id}/add','ProductModelVariations@add');

    //product-model-options
    Route::match(['get'], 'variant-options', ["as" => "variants-options", "uses" => "VariantAttributesController@all"]);
    Route::match(['get'], 'variant-options/{id}/edit', ["as" => "variants-options", "uses" => "VariantAttributesController@edit"]);
    Route::match(['post'], 'variant-options/{id}/edit', ["as" => "variants-options", "uses" => "VariantAttributesController@edit"]);
   
    // products-model variations
    Route::get('variation-options/{id}/add', 'ProductVariantOptionsController@add');
    Route::get('variation-options/{id}/show', 'ProductVariantOptionsController@edit');
    Route::post('variation-options/{id}/show', 'ProductVariantOptionsController@edit');
    Route::post('variation-options/{id}/approve', 'ProductVariantOptionsController@approve');
    Route::post('variation-options/{id}/decline', 'ProductVariantOptionsController@decline');
    Route::post('variation-options/{id}/save-meta', 'ProductVariantOptionsController@saveMeta');
});