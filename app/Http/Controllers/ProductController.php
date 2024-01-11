<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductResourceCollection;
use App\Models\Product;
use App\Models\ProductView;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class ProductController extends Controller
{
    public function getall($perpage,$keybranch, Request $request) : ProductResourceCollection
    {
        $tableName = (new ProductView())->getTable();
        $columlist = Schema::getColumnListing($tableName);
        $orderBy = (null != $request->get('orderby') && in_array(strtolower($request->get('orderby')),$columlist)) ? $request->get('orderby') : "id";
        $ascdesc = (null != $request->get('ascdesc') && (strtolower($request->get('ascdesc'))== "asc"|| strtolower($request->get('ascdesc'))== "desc"))? $request->get('ascdesc'): "asc";
        $page = (null != $request->get('page'))? $request->get('page') : 1; 
        try {
            $value = ProductView::selectRaw("ROW_NUMBER() OVER (ORDER BY products_view.id) AS rownumber,
            products_view.*")->where("branchcode", $keybranch)
            ->orderBy($orderBy, $ascdesc)->orderBy('id', $ascdesc)
            ->Paginate(perPage:$perpage, page:$page);
            $value->withPath($request->fullUrl());
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        return new ProductResourceCollection($value);
        
    }
    public function get($branchcode,$key) : ProductDetailResource
    {
    
        try {
                $data = ProductView::where("branchcode",$branchcode)->where(function($query) use ($key){

                $query->where("id", $key);
                $query->orWhere("barcode", $key);
                })->first();
            } catch (\Throwable $th) {
                throw new HttpResponseException(response([
                    "errors" => [
                        "general" => [
                            $th->getMessage()
                        ]
                    ]
                    ],500));
            }
            if (!$data) {
                throw new HttpResponseException(response([
                    "errors" => [
                        "general" => [
                            "Product Not Found"
                        ]
                    ]
                    ],404));
            }
        return new ProductDetailResource($data);
    }
    public function create(ProductCreateRequest $request) : JsonResponse
    {
        $dataValidated = $request->validated();
        try {
            $barcode = Product::where("branchcode",$dataValidated['branchcode'])->where(function($query) use($dataValidated){
                $query->where("barcode",$dataValidated['barcode']);
            })->first();

            if(!$barcode){
                $data = new Product();
                $data->branchcode = $dataValidated['branchcode'];
                $data->barcode = $dataValidated['barcode'];
                $data->name = $dataValidated["name"];
                $data->brands = $dataValidated["brands"];
                $data->maxstock = $dataValidated["maxstock"];
                $data->price = $dataValidated["price"];
                $data->minstock = $dataValidated["minstock"];
                $data->id_category = $dataValidated["id_category"];
                $data->id_unit = $dataValidated["id_unit"];
    
                $data->save();
            }
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        if ($barcode) {
            throw new HttpResponseException(response([
                "errors" => [
                    "barcode" => [
                        "Product With That Barcode Already Exists"
                    ]
                ]
                ],400));
        }

        return response()->json([
            "data" => [
                "id" => $data->id,
                "barcode" => $data->barcode,
                "name" => $data->name,
            ],
            "success" => "Sucesccfully Created Product"
        ])->setStatusCode(201);

    }
    public function search($branchcode,Request $request) :ProductResourceCollection
    {

        $key = $request->get("key");
        $perpage = (null != $request->get("perpage")) ?  $request->get("perpage") : 10;
        $tableName = (new ProductView())->getTable();
        $columlist = Schema::getColumnListing($tableName);

        
        $filterby =  $request->get('filterby') && in_array(strtolower($request->get('filterby')),$columlist) ? strtolower($request->get('filterby') ): 'all';
        $orderBy = (null != $request->get('orderby') && in_array(strtolower($request->get('orderby')),$columlist)) ? $request->get('orderby') : "id";
        $ascdesc = (null != $request->get('ascdesc') && (strtolower($request->get('ascdesc'))== "asc"|| strtolower($request->get('ascdesc'))== "desc"))? $request->get('ascdesc'): "asc";
        $page = (null != $request->get('page'))? $request->get('page') : 1; 

        $data = ProductView::selectRaw("ROW_NUMBER() OVER (ORDER BY products_view.id) AS rownumber,
        products_view.*")->where("branchcode", $branchcode)
        ->where(function($query) use ($key, $filterby,$columlist){
            $valuesToRemove = ["id", "branchcode", "id_category", "status"];
            $arrayFiltered = array_values(array_diff($columlist, $valuesToRemove));
            if($filterby == 'all'){

                $query->where('barcode', 'like', '%'. $key .'%');
                $query->orWhere('name', 'like', '%'. $key .'%');   
                $query->orWhere('brands', 'like', '%'. $key .'%');   
                $query->orWhere('category', 'like', '%'. $key .'%');   
                $query->orWhere('unit', 'like', '%'. $key .'%');   
                $query->orWhere('price', '=', $key );   
                $query->orWhere('maxstock', '=', $key );   
                $query->orWhere('minstock', '=', $key );   
                $query->orWhere('remaining_stock', 'like','%'. $key .'%');   
            }else{
                for ($i=0; $i < count($arrayFiltered); $i++) { 
                    if ($filterby == $arrayFiltered[$i]){
                        if ($filterby == 'price' ||$filterby == 'maxstock' || $filterby == 'minstock'  ||  $filterby == 'remaining_stock'){

                            $query->orwhere($filterby, '=', $key );
                        } else{
                            $query->orwhere($filterby, 'like','%'. $key .'%');
                        }
                    } 
                }
            }
        })
        ->orderBy($orderBy, $ascdesc)->orderBy('id', $ascdesc)
        ->Paginate(perPage:$perpage, page:$page);
        $data->withPath($request->fullUrl());

        return new ProductResourceCollection($data);
    }
    public function update($branchcode,$id,ProductUpdateRequest $request) : JsonResponse
    {
        $dataValidated = $request->validated();

        try {
            $data = Product::where("branchcode",  $branchcode)->where(function($query) use($id) {
                $query->where("id" ,$id);
                $query->orWhere("barcode",$id);
            })->first();
            if ($data){
                $data->barcode = $dataValidated["barcode"];
                $data->name = $dataValidated["name"];
                $data->brands = $dataValidated["brands"];
                $data->id_category = $dataValidated["id_category"];
                $data->id_unit = $dataValidated["id_unit"];
                $data->price = $dataValidated["price"];
                $data->status = $dataValidated["status"];
                $data->maxstock = $dataValidated["maxstock"];
                $data->minstock = $dataValidated["minstock"];
                $data->save();
            }
            
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        if(!$data) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "Product Is Not Found"
                    ]
                ]
                ],404));
        }
        
        return response()->json([
            "data" => [
                "id" => $data->id,
                "barcode" => $data->barcode,
                "name" => $data->name
            ],
            "success" => "Successfully Updated Products"
        ]);

    }
    public function delete($branchcode,$key) :JsonResponse
    {

        try {
            $data = Product::where("branchcode",$branchcode)->where(function($query) use ($key){
                $query->where("id", $key);
                $query->orWhere("barcode", $key);
            })->first();
            if ($data){
                Product::where("branchcode",$branchcode)->where(function($query) use ($key){
                    $query->where("id", $key);
                    $query->orWhere("barcode", $key);
                })->delete();
            }
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        if(!$data){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "Product Not Found"
                    ]
                ]
                ],404));
        }

        return response()->json([
            "data" => [
                "id" => $data->id,
                "barcode" => $data->barcode,
                "name" => $data->name
            ],
            "success" => "Successfully Deleted Product"
        ]);

    }
}
