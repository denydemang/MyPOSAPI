<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyProfileCreateRequest;
use App\Http\Requests\CompanyProfileUpdateRequest;
use App\Http\Resources\CompanyProfileDetailResource;
use App\Http\Resources\CompanyProfileResourceCollection;
use App\Models\CompanyProfile;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    public function getall(Request $request) :CompanyProfileResourceCollection
    {
        $perpage = $request->get("perpage");
        try {
            $CompProfile = CompanyProfile::paginate($perpage);
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        return new CompanyProfileResourceCollection($CompProfile);
    }
    public function get($branchcode) :CompanyProfileDetailResource
    {
        try {
            $CompProfile = CompanyProfile::where("branchcode", $branchcode)->first();
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        return new CompanyProfileDetailResource($CompProfile);
    }
    public function search(Request $request) : CompanyProfileResourceCollection
    {
        $key = $request->get("key");
        $perpage = $request->get("perpage");
        try {
            $CompProfile = CompanyProfile::where(function($query) use($key){
                $query->where("profile_name", "like", "%". $key . "%");
                $query->orWhere("address" ,"like", "%".$key."%");
                $query->orWhere("phone" ,"like", "%".$key."%");
                $query->orWhere("email" ,"like", "%".$key."%");
                $query->orWhere("npwp" ,"like", "%".$key."%");
                $query->orWhere("moto" ,"like", "%".$key."%");
            })->paginate($perpage);
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        return new CompanyProfileResourceCollection($CompProfile);
    }
    public function create(CompanyProfileCreateRequest $request) :JsonResponse 
    {
        $dataValidated = $request->validated();

        try {
            $branchcode = CompanyProfile::where("branchcode", $dataValidated["branchcode"])->first();
            if(!$branchcode){

                $CP = new CompanyProfile();
                $CP->branchcode = $dataValidated["branchcode"];
                $CP->profile_name = $dataValidated["profile_name"];
                $CP->address = $dataValidated["address"];
                $CP->phone = $dataValidated["phone"];
                $CP->email = $dataValidated["email"];
                $CP->npwp = $dataValidated["npwp"];
                $CP->moto = $dataValidated["moto"];
                $CP->save();
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
        if ($branchcode) {
            throw new HttpResponseException(response([
                "errors" => [
                    "branchcode" => [
                        "Branchcode Already Exists"
                    ]
                ]
                ],400));
        }
        return response()->json([
            "data" => $CP,
            "success" => "Successfully Created New Company"
        ])->setStatusCode(200);
    }
    public function update($branchcode,CompanyProfileUpdateRequest $request) : JsonResponse
    {
        $dataValidated =$request->validated();
        try {
            //code...
            $CP = CompanyProfile::where("branchcode",$branchcode)->first();
            if ($CP){
                $CP->profile_name = $dataValidated["profile_name"];
                $CP->address = $dataValidated["address"];
                $CP->phone = $dataValidated["phone"];
                $CP->email = $dataValidated["email"];
                $CP->npwp = $dataValidated["npwp"];
                $CP->moto = $dataValidated["moto"];
                $CP->update();
            }
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "generals" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        if(!$CP){
            throw new HttpResponseException(response([
                "errors" => [
                    "generals" => [
                        "Branch Code Not Found"
                    ]
                ]
                ],404));
        }
        return response()->json([
            "data"=> $CP,
            "success" => "successfully Updated Company Profile"
        ]);
    }
    public function delete($branchcode) : JsonResponse
    {
        
        try {
            $CP = CompanyProfile::where("branchcode" ,$branchcode)->first();
            if ($CP){
                CompanyProfile::where("branchcode" ,$branchcode)->delete();
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
        if(!$CP){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "BranchCode Not Found"
                    ]
                ]
                ],404));
        }

        return response()->json([
            "data" => [
                "branchcode" =>$CP->branchcode,
                "profile_name" =>$CP->profile_name,
                "email" =>$CP->email
            ],
            "success" => "Successfully Deleted Company Profile"
        ])->setStatusCode(200);
    }
}