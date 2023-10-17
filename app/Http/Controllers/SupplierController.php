<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupplierDetailResource;
use App\Http\Resources\SupplierResourceCollection;
use App\Models\Supplier;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

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

        return new SupplierDetailResource($data);
    }
    public function search($branchcode, Request $request){
        
        $key = $request->get("key");
        $perpage =$request->get("perpage");
        try {
            $data = Supplier::where('branchcode', $branchcode)->where("active", 1)->where(function($query) use($key){
                $query->where('name', 'like', '%'. $key .'%');
                $query->orwhere('address', 'like', '%'. $key .'%');
                $query->orwhere('contact', 'like', '%'. $key .'%');
            })->Paginate($perpage);
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
    }


}
