<?php

use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
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
Route::middleware(ApiAuthMiddleware::class)->group(function(){

    Route::controller(UserController::class)->group(function(){
        Route::get('/users/{id}', "get");
        Route::delete('/users/delete/{id}', 'delete');
        Route::get('/users' ,'getall');
        Route::post('/users' ,'register');
        Route::put("/users/{id}", "update");
        Route::get('/users/checkcompany/{key}',"checkcompany");
    });

    Route::controller(ProductController::class)->group(function(){
        Route::post('/products' ,'create');
        Route::get('/products/{branchcode}/search' ,'search');
        Route::put('/products/{idorbarcode}' ,'update');
        Route::delete('/products/{idorbarcode}' ,'delete');
        Route::get('/products/list/{numberperpage}/{branchcode}' ,'getall');
        Route::get('/products/detail/{idorbarcode}' ,'get');
    });
    
    Route::controller(SupplierController::class)->group(function(){
        Route::get('/suppliers/list/{numberperpage}/{branchcode}' ,'getall');
        Route::get('/suppliers/detail/{id}' ,'get');
        Route::get('/suppliers/{branchcode}/search' ,'search');
        Route::put('/suppliers/{id}' ,'update');
        Route::post('/suppliers' ,'create');
        Route::delete('/suppliers/{id}' ,'delete');
    }); 
    Route::controller(CompanyProfileController::class)->group(function(){
        Route::get('/companyprofiles/list' ,'getall');
        Route::get('/companyprofiles/detail/{branchcode}' ,'get');
        Route::get('/companyprofiles/search' ,'search');
        Route::put('/companyprofiles/{branchcode}' ,'update');
        Route::post('/companyprofiles' ,'create');
        Route::delete('/companyprofiles/{id}' ,'delete');
    }); 
});

Route::delete('/users/logout', [UserController::class, 'logout']);
Route::post('/users/login' ,[UserController::class, 'login']);