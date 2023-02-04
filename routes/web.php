<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/token', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'token' => csrf_token()
        ]
    ]);
});

Route::group(['namespace' => 'App\Http\Controllers'], function() {

    Route::group(['middleware' => ['guest']], function() {

        /**
         * Register Routes (point 1)
         */
        Route::group(['prefix' => 'register'], function() {
            /**
             * Register Admin Routes
             */
            Route::post('admin', 'AuthController@register_admin')
            ->name('register.admin');
        
            /**
             * Register User Routes
             */
            Route::post('', 'AuthController@register_user')
            ->name('register.user');
        });
    
    
        /**
         * Login Routes
         */
        Route::post('login', 'AuthController@login')
        ->name('login');
        
    });
    
    Route::group(['middleware' => ['auth']], function() {
    
        /**
         * Me Routes
         */
        Route::get('me', 'AuthController@me')
        ->name('me');
    
        /**
         * Logout Routes
         */
        Route::get('logout', 'AuthController@logout')
        ->name('logout');

        /**
         * Product
         */
        Route::group(['prefix' => 'product', 'as' => 'product.'], function() {
            
            # list all products (point 4)
            Route::get('', 'ProductController@show')->name('show');
            
            # get a product
            Route::get('{id}', 'ProductController@show')->name('show.id');
            
            # add a new product (point 2)
            Route::post('', 'ProductController@store')->name('store')->middleware('can:isAdmin');
            
            # updating a product
            Route::put('{id}', 'ProductController@update')->name('update')->middleware('can:isAdmin');
            
            # delete a product
            Route::delete('{id}', 'ProductController@destroy')->name('destroy')->middleware('can:isAdmin');
        });

        /**
         * Purchase Order
         */
        Route::group(['prefix' => 'po', 'as' => 'po.', 'middleware' => ['can:isAdmin']], function() {
            
            # list all po
            Route::get('', 'PurchaseOrderController@show')->name('show');
            
            # add a new po (point 3)
            Route::post('', 'PurchaseOrderController@store')->name('store');
            
        });

        /**
         * Order
         */
        Route::group(['prefix' => 'order', 'as' => 'order.'], function() {
            
            # list all orders
            Route::get('', 'OrderController@show')->name('show');
            
            # get a order
            Route::get('{id}', 'OrderController@show')->name('show.id');
            
            # add a new order (point 5 & 7)
            Route::post('', 'OrderController@store')->name('store');
            
            # updating a order (point 6)
            Route::put('{id}', 'OrderController@update')->name('update')->middleware('can:isAdmin');
        });
        
    });

});