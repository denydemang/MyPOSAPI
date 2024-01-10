<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerCreateRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerDetailResource;
use App\Http\Resources\CustomerResoureCollection;
use App\Models\Customer;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class CustomerController extends Controller
{
    public function getall($branchcode, Request $request ) : CustomerResoureCollection
    {
        $perpage = $request->get("perpage") ? $request->get("perpage")  : 10;
        try {
            $customer = Customer::where("branchcode",$branchcode)->where('active', 1)->paginate($perpage);
            
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        if(!$customer){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "Customer Not Found"
                    ]
                ]
                ],404));
        }
        return new CustomerResoureCollection($customer,"Successfully Get All Customers");
    }
    public function get($branchcode,$key) :CustomerDetailResource
    {
        try {
            $data = Customer::where("branchcode",$branchcode)->where(function($query) use ($key){

            $query->where("id", $key);
            $query->orWhere("cust_no", $key);
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
                        "Customer Not Found"
                    ]
                ]
                ],404));
        }
    return new CustomerDetailResource($data, "Successfuly Get Specific Customer");
    }
    public function search($branchcode,Request $request) : CustomerResoureCollection
    {

        $key = $request->get("key");
        $perpage = $request->get("perpage");

        $data = Customer::where("branchcode", $branchcode)->where("active", 1)->where(function($query) use ($key){
            $query->where('cust_no', 'like', '%'. $key .'%');
            $query->orwhere('name', 'like', '%'. $key .'%');
            $query->orwhere('address', 'like', '%'. $key .'%');
            $query->orwhere('phone', 'like', '%'. $key .'%');
        })->Paginate($perpage);
        $data->withPath(URL::to('/').'/api/customers/'.$branchcode.'/search?key='.$key.'&perpage='.$perpage);

        return new CustomerResoureCollection($data, "Successfully Get Customer In Search");
    }
    public function update($branchcode, $id , CustomerUpdateRequest $request) : CustomerDetailResource
    {
        $dataValidated = $request->validated();
        try {
            $data = Customer::where("branchcode",  $branchcode)->where('active', 1)->where(function($query) use($id) {
                $query->where("id" ,$id);
                $query->orWhere("cust_no",$id);
            })->first();
            if ($data){
                $data->name = $dataValidated["name"];
                $data->address = isset($dataValidated["address"]) ? $dataValidated["address"]  : $data->address  ;
                $data->phone = isset($dataValidated["phone"]) ? $dataValidated["phone"] :$data->phone;
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
        if (!$data) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "Customer Not Found"
                    ]
                ]
                ],404));
        }
        return new CustomerDetailResource($data, 'Successfully Updated Customer');
    }

    public function create(CustomerCreateRequest $request) :CustomerDetailResource
    {
        $dataValidated = $request->validated();
        try {
            $name = Customer::where("branchcode", $dataValidated['branchcode'])->where("active", 1)->where("name",  $dataValidated["name"])->first();
            $cust_no  = null;

            if(!$name){
                DB::beginTransaction();
                $data  = new Customer();
                if ($dataValidated["cust_no"]){
                    $cust_no = Customer::where("branchcode", $dataValidated['branchcode'])->where("active", 1)->where("cust_no",  $dataValidated["cust_no"])->first();
                    if (!$cust_no){
                        $data->cust_no = $dataValidated["cust_no"] ? $dataValidated["cust_no"] : null ;
                        $data->branchcode = $dataValidated["branchcode"];
                        $data->name = $dataValidated["name"];
                        $data->address = $dataValidated["address"]  ? $dataValidated["address"] : null ;
                        $data->phone = $dataValidated["phone"] ? $dataValidated['phone'] : null;
                        $data->active = 1;

                        $data->save();
                    }
                } else {
                    $newCodeCust="";
                    $getCustID = Customer::select('cust_no')->where("branchcode", $dataValidated["branchcode"])->where("active", 1)->where("cust_no","like", "Cust_%")->orderBy("cust_no", "desc")->first();
                    if ($getCustID){
                        $getLockMax = Customer::select('cust_no')->where("branchcode", $dataValidated["branchcode"])->where("active", 1)->where("cust_no", ">=", $getCustID->cust_no)->lockForUpdate()->orderBy("cust_no", "desc")->first();
                        $getLastDigit = intval(str_replace("Cust_","",$getLockMax->cust_no));
                        $newCodeCust  = "Cust_". sprintf("%03d",$getLastDigit + 1);
                    } else {
                        $newCodeCust  = "Cust_001";
                    }
                    $data->cust_no =$newCodeCust;
                    $data->branchcode = $dataValidated["branchcode"];
                    $data->name = $dataValidated["name"];
                    $data->address = $dataValidated["address"]  ? $dataValidated["address"] : null ;
                    $data->phone = $dataValidated["phone"] ? $dataValidated['phone'] : null;
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
        if ($cust_no){
            throw new HttpResponseException(response([
                "errors" => [
                    "cust_no" => [
                        "Cust ID Already Exists"
                    ]
                ]
                ],400));
        }

        return new CustomerDetailResource($data,'Successfully Created New Customer');

    }
    public function delete($branchcode, $key) : CustomerDetailResource
    {
        
        try {
            $data = Customer::where("branchcode", $branchcode)->where(function($query) use ($key){
                $query->where("id", $key);
                $query->orWhere("cust_no", $key);
            })->first();
            if ($data){
                Customer::where("branchcode", $branchcode)->where(function($query) use ($key){
                    $query->where("id", $key);
                    $query->orWhere("cust_no", $key);
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
                        "Customer Not Found"
                    ]
                ]
                ],404));
        }

        return new CustomerDetailResource($data, "Successfully Deleted Customer");
    }
}
