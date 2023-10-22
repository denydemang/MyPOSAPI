<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerCreateRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerDetailResource;
use App\Http\Resources\CustomerResoureCollection;
use App\Models\Customer;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class CustomerController extends Controller
{
    public function getall($numberperpage,$branchcode) : CustomerResoureCollection
    {
        
        try {
            $customer = Customer::where("branchcode",$branchcode)->paginate($numberperpage);
            
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

        $data = Customer::where("branchcode", $branchcode)->where(function($query) use ($key){
            $query->where('cust_no', 'like', '%'. $key .'%');
            $query->orwhere('name', 'like', '%'. $key .'%');
            $query->orwhere('address', 'like', '%'. $key .'%');
            $query->orwhere('phone', 'like', '%'. $key .'%');
            $query->orwhere('active', 'like', '%'. $key .'%');
        })->Paginate($perpage);
        $data->withPath(URL::to('/').'/api/customers/'.$branchcode.'/search?key='.$key.'&perpage='.$perpage);

        return new CustomerResoureCollection($data, "Successfully Get Customer In Search");
    }
    public function update($branchcode, $id , CustomerUpdateRequest $request) : CustomerDetailResource
    {
        $dataValidated = $request->validated();
        try {
            $data = Customer::where("branchcode",  $branchcode)->where(function($query) use($id) {
                $query->where("id" ,$id);
                $query->orWhere("cust_no",$id);
            })->first();
            if ($data){
                $data->name = $dataValidated["name"];
                $data->address = isset($dataValidated["address"]) ? $dataValidated["address"]  : $data->address  ;
                $data->phone = isset($dataValidated["phone"]) ? $dataValidated["phone"] :$data->phone;
                $data->active = $dataValidated["active"];
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
            $barcode = Customer::where("branchcode",$dataValidated['branchcode'])->where("cust_no",$dataValidated['cust_no'])->first();

            if(!$barcode){
                $data = new Customer();
                $data->branchcode = $dataValidated['branchcode'];
                $data->cust_no = $dataValidated['cust_no'];
                $data->name = $dataValidated["name"];
                $data->address = isset($dataValidated["address"]) ? $dataValidated["address"] : null;
                $data->phone = isset($dataValidated["phone"]) ? $dataValidated["phone"] : null;
                $data->active = $dataValidated["active"];
    
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
                    "cust_no" => [
                        "No Cust Already Exist"
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
