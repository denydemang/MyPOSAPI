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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class SupplierController extends Controller
{
    public function getall($branchcode, Request $request) : SupplierResourceCollection
    {
        $perpage = $request->get("perpage") ? $request->get("perpage")  : 10;
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
                $query->orWhere('address', 'like', '%'. $key .'%');
                $query->orWhere('number_id', 'like', '%'. $key .'%');
                $query->orWhere('contact', 'like', '%'. $key .'%');
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

    public function update($branchcode,$id ,SupplierUpdateRequest $request) :JsonResponse
    {

        $dataValidated = $request->validated();
        
        try {
            $data = Supplier::where("id", $id)->first();
            $number_id = null;
            $name = null;
            if ($data) {
                if ($data->number_id != $dataValidated["number_id"]){
                    $number_id = Supplier::where("branchcode", $branchcode)->where("number_id", $dataValidated["number_id"])->first();
                    if(!$number_id){
                        $data->number_id = $dataValidated["number_id"];
                    } 
                } 
                if ($data->name != $dataValidated["name"]){
                    $name = Supplier::where("branchcode", $branchcode)->where("name", $dataValidated["name"])->first();
                    if(!$name){
                        $data->name = $dataValidated["name"];
                    } 
                } 
                if(!$name && !$number_id){
                    $data->address = $dataValidated["address"];
                    $data->contact = $dataValidated["contact"];
                    $data->update();
                }

            
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
        if($number_id ){
            throw new HttpResponseException(response([
                "errors" => [
                    "number_id" => [
                        "Number Id Already Exists"
                    ]
                ]
                ],400));
        }
        if($name ){
            throw new HttpResponseException(response([
                "errors" => [
                    "name" => [
                        "Name Already Exists"
                    ]
                ]
                ],400));
        }

        return response()->json([
            "data" => [
                "id" => $data->id,
                "number_id" => $data->number_id,
                "name" => $data->name,
            ],
            "success" => "Successfully Updated Supplier"
        ])->setStatusCode(200);


    }

    public function create(SupplierCreateRequest $request) :SupplierDetailResource
    {
        $dataValidated = $request->validated();
        try {
            $name = Supplier::where("branchcode", $dataValidated['branchcode'])->where("name",  $dataValidated["name"])->first();
            $number_id  = null;
            if(!$name){
                DB::beginTransaction();
                $data  = new Supplier();
                if ($dataValidated["number_id"]){
                    $number_id = Supplier::where("branchcode", $dataValidated['branchcode'])->where("number_id",  $dataValidated["number_id"])->first();
                    if(!$number_id){
                        $data->number_id = $dataValidated["number_id"];
                        $data->branchcode = $dataValidated['branchcode'];
                        $data->name =$dataValidated['name'];
                        $data->address =$dataValidated['address'];
                        $data->contact =$dataValidated['contact'];
                        $data->active = 1;
            
                        $data->save();
                    }
                    
                } else {
                    $newCodeSupp="";
                    $getNumberID = Supplier::select('number_id')->where("branchcode", $dataValidated["branchcode"])->where("number_id","like", "Supp_%")->orderBy("number_id", "desc")->first();
                    if ($getNumberID){
                        $getLockMax = Supplier::select('number_id')->where("branchcode", $dataValidated["branchcode"])->where("number_id", ">=", $getNumberID->number_id)->lockForUpdate()->orderBy("number_id", "desc")->first();
                        $getLastDigit = intval(str_replace("supp_","",$getLockMax->number_id));
                        $newCodeSupp  = "supp_". sprintf("%03d",$getLastDigit + 1);
                    } else {
                        $newCodeSupp  = "supp_001";
                    }
                    $data->number_id = $newCodeSupp;
                    $data->branchcode = $dataValidated['branchcode'];
                    $data->name =$dataValidated['name'];
                    $data->address =$dataValidated['address'];
                    $data->contact =$dataValidated['contact'];
                    $data->active = 1;
        
                    $data->save();

                }
                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
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
                    "name" => [
                        "Name Already Exists"
                    ]
                ]
                ],400));
        }
        if ($number_id){
            throw new HttpResponseException(response([
                "errors" => [
                    "number_id" => [
                        "Number Id Already Exists"
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
