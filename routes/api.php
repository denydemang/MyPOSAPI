<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GRNController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
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
        Route::get("/users/password/{id}", "getPasswordUser");
        Route::get('/users/{id}', "get");
        Route::delete('/users/delete/{id}', 'delete');
        Route::get('/users' ,'getall');    
        Route::post('/users' ,'register');
        Route::put("/users/{id}", "update");
        Route::patch("/users/deactivate/{id}", "deactivate");
        Route::patch("/users/activate/{id}", "activate");
        Route::get('/users/checkcompany/{id}',"checkcompany");
    });
    
    
    Route::controller(ProductController::class)->group(function(){
        Route::post('/products' ,'create');
        Route::get('/products/{branchcode}/search' ,'search');
        Route::put('/products/{branchcode}/{idorbarcode}' ,'update');
        Route::delete('/products/{branchcode}/{idorbarcode}' ,'delete');
        Route::get('/products/list/{numberperpage}/{branchcode}' ,'getall');
        Route::get('/products/detail/{branchcode}/{idorbarcode}' ,'get');
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

    Route::controller(CustomerController::class)->group(function(){
        Route::get('/customers/list/{numberperpage}/{branchcode}' ,'getall');
        Route::get('/customers/detail/{branchcode}/{idorcustno}' ,'get');
        Route::get('/customers/{branchcode}/search' ,'search');
        Route::put('/customers/{branchcode}/{idorcustno}' ,'update');
        Route::post('/customers' ,'create');
        Route::delete('/customers/{branchcode}/{idorcustno}' ,'delete');
    }); 
    Route::controller(CategoryController::class)->group(function(){
        Route::get('/categories/list/{numberperpage}/{branchcode}' ,'getall');
        Route::get('/categories/detail/{id}' ,'get');
        Route::get('/categories/{branchcode}/search' ,'search');
        Route::put('/categories/{id}' ,'update');
        Route::post('/categories' ,'create');
        Route::delete('/categories/{id}' ,'delete');
    }); 

    Route::controller(SalesController::class)->group(function(){
        Route::get('/sales/list/{branchcode}' ,'getall');
        Route::get('/sales/detail/{branchcode}/{idsaleseortrans_no}' ,'get');
        Route::get('/sales/{branchcode}/search' ,'search');
        Route::post('/sales' ,'create');
        Route::put('/sales/{branchcode}/{idsalesortrans_no}' ,'update');
        Route::delete('/sales/{branchcode}/{idsalesortrans_no}' ,'delete');
        Route::patch('/sales/{branchcode}/{idsalesortrans_no}' ,'approve');
    }); 

    Route::controller(PurchaseController::class)->group(function(){
        Route::get('/purchases/list/{branchcode}' ,'getall');
        Route::get('/purchases/detail/{branchcode}/{idpurchaseortrans_no}' ,'get');
        Route::get('/purchases/{branchcode}/search' ,'search');
        Route::post('/purchases' ,'create');
        Route::put('/purchases/{branchcode}/{idpurchaseortrans_no}' ,'update');
        Route::delete('/purchases/{branchcode}/{id}' ,'delete');
        Route::patch('/purchases/{branchcode}/{id}' ,'approve');
    }); 

    Route::controller(PurchaseReturnController::class)->group(function(){
        Route::get('/grns/list/{branchcode}' ,'getall');
        Route::get('/grns/detail/{branchcode}/{idgrnseortrans_no}' ,'get');
        Route::get('/grns/{branchcode}/search' ,'search');
        Route::post('/grns' ,'create');
        Route::put('/grns/{branchcode}/{idgrnsortrans_no}' ,'update');
        Route::delete('/grns/{branchcode}/{idgrnsortrans_no}' ,'delete');
        Route::patch('/grns/{branchcode}/{idgrnsortrans_no}' ,'approve');
    }); 

    
}); 

Route::controller(GRNController::class)->group(function(){
    Route::get('/grns/list/{branchcode}' ,'getall');
    Route::get('/grns/detail/{branchcode}/{idgrnseortrans_no}' ,'get');
    Route::get('/grns/{branchcode}/search' ,'search');
    Route::post('/grns' ,'create');
    Route::put('/grns/{branchcode}/{idgrnsortrans_no}' ,'update');
    Route::delete('/grns/{branchcode}/{idgrnsortrans_no}' ,'delete');
    Route::patch('/grns/{branchcode}/{idgrnsortrans_no}' ,'approve');
}); 
Route::controller(UnitController::class)->group(function(){
    Route::get('/units/list' ,'getall');
    Route::get('/units/group/{idunit}' ,'getgroup');
    Route::get('/units/default' ,'getdefault');
    // Route::get('/units/detail/{branchcode}/{idunitseortrans_no}' ,'get');
    // Route::get('/units/{branchcode}/search' ,'search');
    // Route::post('/units' ,'create');
    // Route::put('/units/{branchcode}/{idunitsortrans_no}' ,'update');
    // Route::delete('/units/{branchcode}/{idunitsortrans_no}' ,'delete');
    // Route::patch('/units/{branchcode}/{idunitsortrans_no}' ,'approve');
}); 
Route::controller(UserController::class)->group(function(){
    Route::get('/users/checklogin/{id}/{token}',"checkuserlogin");
});

Route::controller(StockController::class)->group(function(){
    Route::get('/stocks/{idproduct}/{qty}', 'stockout');
    // Route::get('/sales/list/{branchcode}' ,'getall');
    // Route::get('/sales/detail/{branchcode}/{idpurchaseortrans_no}' ,'get');
    // Route::get('/sales/{branchcode}/search' ,'search');
    // Route::post('/sales' ,'create');
    // Route::put('/sales/{branchcode}/{idpurchaseortrans_no}' ,'update');
    // Route::delete('/sales/{branchcode}/{id}' ,'delete');
    // Route::patch('/sales/{branchcode}/{id}' ,'approve');
}); 



    
Route::delete('/users/logout', [UserController::class, 'logout']);
Route::post('/users/login/{branchcode}' ,[UserController::class, 'login']);