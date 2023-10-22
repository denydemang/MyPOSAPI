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
use Illuminate\Support\Facades\URL;

class ProductController extends Controller
{
    public function getall($perpage,$keybranch) : ProductResourceCollection
    {

        try {
            $value = ProductView::where("branchcode", $keybranch)->Paginate($perpage);
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
                $query->orWhere("name",$dataValidated['name']);
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
                    "general" => [
                        "Product With That Barcode or Name Already Exists"
                    ]
                ]
                ],400));
        }

        return response()->json([
            "data" => [
                "barcode" => $data->barcode,
                "name" => $data->name,
            ],
            "success" => "Sucesccfully Created Product"
        ])->setStatusCode(201);

    }
    public function search($branchcode,Request $request) :ProductResourceCollection
    {

        $key = $request->get("key");
        $perpage = $request->get("perpage");

        $data = ProductView::where("branchcode", $branchcode)->where(function($query) use ($key){
            $query->where('barcode', 'like', '%'. $key .'%');
            $query->orwhere('name', 'like', '%'. $key .'%');
            $query->orwhere('brands', 'like', '%'. $key .'%');
            $query->orwhere('price', 'like', '%'. $key .'%');
            $query->orwhere('category', 'like', '%'. $key .'%');
            $query->orwhere('unit', 'like', '%'. $key .'%');
        })->Paginate($perpage);
        $data->withPath(URL::to('/').'/api/products/'.$branchcode.'/search?key='.$key.'&perpage='.$perpage);

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
