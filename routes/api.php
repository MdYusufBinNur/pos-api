<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/',function () {
    return response()->json('Api running');
});
Route::prefix('v1')->namespace('Api\v1')->group(function () {
    Route::post('login','AdminController@login');
    Route::post('register','AdminController@register');
});
Route::middleware(['auth:api'])->prefix('v1')->namespace('Api\v1')->group(function () {
    Route::middleware(['checkRole:super'])->group(function () {
        Route::get('users', 'AdminController@index');
        Route::post('assign-branch', 'AdminController@assignBranchToAnUser');
    });
    //Admin Routes
    Route::middleware(['checkRole:super,admin'])->group(function () {
        Route::get('users', 'AdminController@index');
    });
});
Route::middleware(['auth:api'])->prefix('v1')->group(function () {

    Route::middleware(['checkRole:super'])->group(function () {
        Route::apiResource('shop','ShopController');
        Route::apiResource('brand','BrandController');
        Route::apiResource('branch','BranchController');

      //  Route::apiResource('sale','ProductSaleController');
    });
    //Admin Routes
    Route::middleware(['checkRole:super,admin'])->group(function () {
        Route::apiResource('product','ProductController');
        Route::apiResource('category','CategoryController');
        Route::apiResource('sub-category','SubCategoryController');
        Route::apiResource('unit','UnitController');
        Route::apiResource('supplier','SupplierController');
        Route::apiResource('supplier_category','SupplierCategoryController');
        Route::apiResource('customer','CustomerController');
        Route::apiResource('purchase-request','PurchaseRequestController');
        Route::apiResource('product-stock','ProductStockController');
    });
    //Seller Routes
    Route::middleware(['checkRole:super,seller,admin'])->group(function () {
        //Write routes
        Route::get('online-delivery', 'ProductSaleController@onlineDelivery');
        Route::get('regular-delivery', 'ProductSaleController@regularDelivery');
        Route::apiResource('sale','ProductSaleController');
        Route::apiResource('expense','ExpenseController');
        Route::post('expense-list','ExpenseController@expenseList');
    });

    //Customers Routes
    Route::middleware(['checkRole:super,customer'])->group(function () {
        //Write routes
    });

    //Branch Manager Routes
    Route::middleware(['checkRole:super,branch_manager'])->group(function () {
        //Write routes
    });
});
