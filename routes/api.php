<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GRNController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthCategoryMiddleware;
use App\Http\Middleware\ApiAuthCompanyProfileMiddleware;
use App\Http\Middleware\ApiAuthCustomerMiddleware;
use App\Http\Middleware\ApiAuthGRNMiddleware;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Middleware\ApiAuthProductMiddleware;
use App\Http\Middleware\ApiAuthPurchaseMiddleware;
use App\Http\Middleware\ApiAuthPurchaseReturnMiddleware;
use App\Http\Middleware\ApiAuthRoleMiddleware;
use App\Http\Middleware\ApiAuthSalesMiddleware;
use App\Http\Middleware\ApiAuthStockController;
use App\Http\Middleware\ApiAuthSupplierMiddleware;
use App\Http\Middleware\ApiAuthSupplierMiddlewaremaster_supplier;
use App\Http\Middleware\ApiAuthUserMiddleware;
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
// */
Route::controller(UserController::class)->group(function(){
    Route::get('/users/checklogin/{id}/{token}',"checkuserlogin");
    Route::delete('/users/logout', 'logout');
    Route::post('/users/login/{branchcode}' ,'login');
});
Route::controller(RoleController::class)->group(function(){
    Route::get("/roles/access/{token}", "getaccessview");
});
    
Route::middleware(ApiAuthMiddleware::class)->group(function(){
    Route::middleware(ApiAuthUserMiddleware::class)->group(function(){
        Route::controller(UserController::class)->group(function(){
            Route::get('/users' ,'getall');    
            Route::post('/users' ,'register');
            Route::get("/users/password/{id}", "getPasswordUser");
            Route::get('/users/search', "search");
            Route::patch("/users/deactivate/{id}", "deactivate");
            Route::patch("/users/activate/{id}", "activate");
            Route::get('/users/checkcompany/{id}',"checkcompany");
            Route::get('/users/{id}', "get");
            Route::delete('/users/{id}', 'delete');
            Route::put("/users/{id}", "update");
        });   
    });
    Route::middleware(ApiAuthRoleMiddleware::class)->group(function(){
        Route::controller(RoleController::class)->group(function(){
            Route::get("/roles/list/{branchcode}", "getall");
            Route::get("/roles/detail/{branchcode}/{id_role}", "get");
            Route::post("/roles", "create");
            Route::put("/roles/{branchcode}/{id_role}", "update");
            Route::delete("/roles/{branchcode}/{id_role}", "delete");
        });
    });
    Route:: middleware(ApiAuthProductMiddleware::class)->group(function(){
        Route::controller(ProductController::class)->group(function(){
            Route::post('/products' ,'create');
            Route::get('/products/{branchcode}/search' ,'search');
            Route::put('/products/{branchcode}/{idorbarcode}' ,'update');
            Route::delete('/products/{branchcode}/{idorbarcode}' ,'delete');
            Route::get('/products/list/{numberperpage}/{branchcode}' ,'getall');
            Route::get('/products/detail/{branchcode}/{idorbarcode}' ,'get');
        });
    });
    Route::middleware(ApiAuthSupplierMiddleware::class)->group(function(){
        Route::controller(SupplierController::class)->group(function(){
            Route::get('/suppliers/list/{branchcode}' ,'getall');
            Route::get('/suppliers/detail/{id}' ,'get');
            Route::get('/suppliers/{branchcode}/search' ,'search');
            Route::put('/suppliers/{branchcode}/{id}' ,'update');
            Route::post('/suppliers' ,'create'); 
            Route::delete('/suppliers/{id}' ,'delete');
        }); 
    });
    Route::middleware(ApiAuthCompanyProfileMiddleware::class)->group(function(){
        Route::controller(CompanyProfileController::class)->group(function(){
            Route::get('/companyprofiles/list' ,'getall');
            Route::get('/companyprofiles/detail/{branchcode}' ,'get');
            Route::get('/companyprofiles/search' ,'search');
            Route::put('/companyprofiles/{branchcode}' ,'update');
            Route::post('/companyprofiles' ,'create');
            Route::delete('/companyprofiles/{id}' ,'delete');
        }); 
    });
    Route::middleware(ApiAuthCustomerMiddleware::class)->group(function(){
        Route::controller(CustomerController::class)->group(function(){
            Route::get('/customers/list/{numberperpage}/{branchcode}' ,'getall');
            Route::get('/customers/detail/{branchcode}/{idorcustno}' ,'get');
            Route::get('/customers/{branchcode}/search' ,'search');
            Route::put('/customers/{branchcode}/{idorcustno}' ,'update');
            Route::post('/customers' ,'create');
            Route::delete('/customers/{branchcode}/{idorcustno}' ,'delete');
        }); 
    });

    Route::middleware(ApiAuthCategoryMiddleware::class)->group(function(){
        Route::controller(CategoryController::class)->group(function(){
            Route::get('/categories/list/{numberperpage}/{branchcode}' ,'getall');
            Route::get('/categories/detail/{id}' ,'get');
            Route::get('/categories/{branchcode}/search' ,'search');
            Route::put('/categories/{id}' ,'update');
            Route::post('/categories' ,'create');
            Route::delete('/categories/{id}' ,'delete');
        }); 
    });
    Route::controller(ModuleController::class)->group(function(){
        Route::get('/modules/list', 'getall');
    });
    Route::middleware(ApiAuthSalesMiddleware::class)->group(function(){
        Route::controller(SalesController::class)->group(function(){
            Route::get('/sales/list/{branchcode}' ,'getall');
            Route::get('/sales/detail/{branchcode}/{idsaleseortrans_no}' ,'get');
            Route::get('/sales/{branchcode}/search' ,'search');
            Route::post('/sales' ,'create');
            Route::put('/sales/{branchcode}/{idsalesortrans_no}' ,'update');
            Route::delete('/sales/{branchcode}/{idsalesortrans_no}' ,'delete');
            Route::patch('/sales/{branchcode}/{idsalesortrans_no}' ,'approve');
        }); 
    });
    Route::middleware(ApiAuthPurchaseMiddleware::class)->group(function(){
        Route::controller(PurchaseController::class)->group(function(){
            Route::get('/purchases/list/{branchcode}' ,'getall');
            Route::get('/purchases/detail/{branchcode}/{idpurchaseortrans_no}' ,'get');
            Route::get('/purchases/{branchcode}/search' ,'search');
            Route::post('/purchases' ,'create');
            Route::put('/purchases/{branchcode}/{idpurchaseortrans_no}' ,'update');
            Route::delete('/purchases/{branchcode}/{id}' ,'delete');
            Route::patch('/purchases/{branchcode}/{id}' ,'approve');
        }); 
    });
    Route::middleware(ApiAuthPurchaseReturnMiddleware::class)->group(function(){
        Route::controller(PurchaseReturnController::class)->group(function(){
            Route::get('/purchasereturn/list/{branchcode}' ,'getall');
            Route::get('/purchasereturn/detail/{branchcode}/{idpurchasereturneortrans_no}' ,'get');
            Route::get('/purchasereturn/{branchcode}/search' ,'search');
            Route::post('/purchasereturn' ,'create');
            Route::put('/purchasereturn/{branchcode}/{idpurchasereturnortrans_no}' ,'update');
            Route::delete('/purchasereturn/{branchcode}/{idpurchasereturnortrans_no}' ,'delete');
            Route::patch('/purchasereturn/{branchcode}/{idpurchasereturnortrans_no}' ,'approve');
        }); 
    });
    Route::middleware(ApiAuthGRNMiddleware::class)->group(function(){
        Route::controller(GRNController::class)->group(function(){
            Route::get('/grns/list/{branchcode}' ,'getall');
            Route::get('/grns/detail/{branchcode}/{idgrnseortrans_no}' ,'get');
            Route::get('/grns/{branchcode}/search' ,'search');
            Route::post('/grns' ,'create');
            Route::put('/grns/{branchcode}/{idgrnsortrans_no}' ,'update');
            Route::delete('/grns/{branchcode}/{idgrnsortrans_no}' ,'delete');
            Route::patch('/grns/{branchcode}/{idgrnsortrans_no}' ,'approve');
        }); 
    });

    Route::controller(UnitController::class)->group(function(){
        Route::get('/units/list' ,'getall');
        Route::get('/units/group/{idunit}' ,'getgroup');
        Route::get('/units/default' ,'getdefault');
    }); 
    Route::middleware(ApiAuthStockController::class)->group(function(){
        Route::controller(StockController::class)->group(function(){
            Route::get('/stocks/{idproduct}/{qty}', 'stockout');
            Route::get('/stocks/initial/{branchcode}/{idproduct}', 'getinitialstock');
            Route::post('/stocks/initial/create', 'createinitialstock');
            Route::put('/stocks/initial/{branchcode}/{idproduct}', 'updateinitialstock');
        });
    });
}); 

