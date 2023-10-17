<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierCreateRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Http\Resources\SupplierDetailResource;
use App\Http\Resources\SupplierResourceCollection;
use App\Models\Supplier;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SupplierController extends Controller
{
    public function getall($perpage, $branchcode) : SupplierResourceCollection
    {
        try {
            $data = Supplier::where("branchcode" , $branchcode)->where("active", 1)->Paginate($perpage);
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        return new SupplierResourceCollection($data);
    }
    public function get($id) : SupplierDetailResource
    {
        try {
            $data = Supplier::where("id" , $id)->first();
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
                        "Supplier Not Found"
                    ]
                ]
                ],404));
        }

        return new SupplierDetailResource($data, "Successfully Get Sepicific Supplier");
    }
    public function search($branchcode, Request $request):SupplierResourceCollection
    {
        
        $key = $request->get("key");
        $perpage =$request->get("perpage");
        try {
            $data = Supplier::where('branchcode', $branchcode)->where("active", 1)->where(function($query) use($key){
                $query->where('name', 'like', '%'. $key .'%');
                $query->orwhere('address', 'like', '%'. $key .'%');
                $query->orwhere('contact', 'like', '%'. $key .'%');
            })->Paginate($perpage);
            $data->withPath(URL::to('/').'/api/supplier/'.$branchcode.'/search?key='.$key.'&perpage='.$perpage);

        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        return new SupplierResourceCollection($data);
    }

    public function update($id ,SupplierUpdateRequest $request) :JsonResponse
    {

        $dataValidated = $request->validated();

        try {
            $data = Supplier::where("id", $id)->first();
            if ($data) {
                $data->name =$dataValidated["name"];
                $data->address =$dataValidated["address"];
                $data->contact =$dataValidated["contact"];
                $data->active =$dataValidated["active"];

                $data->update();
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
                        "Supplier Not Found"
                    ]
                ]
                ],404));
        }

        return response()->json([
            "data" => [
                "id" => $data->id,
                "name" => $data->name,
            ],
            "success" => "Successfully Updated Supplier"
        ]);


    }

    public function create(SupplierCreateRequest $request) :SupplierDetailResource
    {
        $dataValidated = $request->validated();
        try {
            $name = Supplier::where("name", $dataValidated['name'])->first();
            if(!$name){
                $data  = new Supplier();
                $data->branchcode = $dataValidated['branchcode'];
                $data->name =$dataValidated['name'];
                $data->address =$dataValidated['address'];
                $data->contact =$dataValidated['contact'];
                $data->active = 1;
    
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
        if ($name){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "Name Already Exists"
                    ]
                ]
                ],400));
        }
        return new SupplierDetailResource($data,"Successfully Create New Supplier");
    }
    public function delete($id) :JsonResponse
    {
        
        try {
            $data = Supplier::where('id',$id)->first();
            if($data){
                Supplier::where("id",$id)->delete();
            }
            //code...
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
                    "Supplier Not Found"
                    ]
                ]
                ],404));
        }

        return response()->json([
            "data" => [
                "id" => $data->id,
                "name" => $data->name,
            ],
            "success" => "Sucessfully Deleted Supplier"
        ])->setStatusCode(200);
    }



}
