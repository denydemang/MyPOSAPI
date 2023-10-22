<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryCreateRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryDetailResource;
use App\Http\Resources\CategoryResourceCollection;
use App\Models\Category;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class CategoryController extends Controller
{
    public function getall($numberperpage,$branchcode) : CategoryResourceCollection
    {
        
        try {
            $category = Category::where("branchcode",$branchcode)->where("status", 1)->paginate($numberperpage);
            
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        if(!$category){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "Categories Not Found"
                    ]
                ]
                ],404));
        }
        return new CategoryResourceCollection($category,"Successfully Get All Categories");
    }
    public function get($key) :CategoryDetailResource
    {
        try {
            $data = Category::where("id",$key)->first();
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
                        "Category Not Found"
                    ]
                ]
                ],404));
        }
    return new CategoryDetailResource($data, "Successfuly Get Specific Category");
    }
    public function search($branchcode,Request $request) : CategoryResourceCollection
    {

        $key = $request->get("key");
        $perpage = $request->get("perpage");

        $data = Category::where("branchcode", $branchcode)->where('status',1)->where(function($query) use ($key){
            $query->where('name', 'like', '%'. $key .'%');
        })->Paginate($perpage);
        $data->withPath(URL::to('/').'/api/categories/'.$branchcode.'/search?key='.$key.'&perpage='.$perpage);

        return new CategoryResourceCollection($data, "Successfully Get Category In Search");
    }
    public function update($id , CategoryUpdateRequest $request) : CategoryDetailResource
    {
        $dataValidated = $request->validated();
        try {
            $data = Category::where("id",  $id)->first();
            if ($data){
                $data->name = $dataValidated["name"];
                $data->status = $dataValidated["status"];
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
                        "Category Not Found"
                    ]
                ]
                ],404));
        }
        return new CategoryDetailResource($data, 'Successfully Updated Category');
    }
    public function create(CategoryCreateRequest $request) :CategoryDetailResource
    {
        $dataValidated = $request->validated();
        try {
            $category = Category::where("branchcode",$dataValidated['branchcode'])->where("status", 1)->where("name",$dataValidated['name'])->first();

            if(!$category){
                $data = new Category();
                $data->branchcode = $dataValidated['branchcode'];
                $data->name = $dataValidated['name'];
                $data->status = $dataValidated["status"];
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
        if ($category) {
            throw new HttpResponseException(response([
                "errors" => [
                    "cust_no" => [
                        "Name Already Exists"
                    ]
                ]
                ],400));
        }

        return new CategoryDetailResource($data,'Successfully Created New Categories');

    }

    public function delete($id) : CategoryDetailResource
    {
        
        try {
            $data = Category::where("id", $id)->first();
            if ($data){
                Category::where("id", $id)->delete();
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
                        "Category Not Found"
                    ]
                ]
                ],404));  
        }

        return new CategoryDetailResource($data, "Successfully Deleted Category");
    }

}
