<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleCreateRequest;
use App\Http\Resources\RoleResourceCollection;
use App\Models\AccessView;
use App\Models\Role;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function getall($branchcode, Request $request) : RoleResourceCollection{
        $perpage = $request->get('perpage') ? $request->get('perpage') : 10 ;
        $page =$request->get("page") ? $request->get("page") : 1;

        try {
            $data = Role::where("branchcode", $branchcode)->where("active", 1)
            ->paginate(perPage: $perpage , page: $page);
            $data->withPath($request->fullUrl());
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        return new RoleResourceCollection($data);
    }
    public function get($branchcode,$id_role) : RoleResourceCollection{
        try {
            $data = AccessView::where('branchcode',$branchcode)->where(function($query) use($id_role){
                $query->where("id_role", $id_role);
                $query->orWhere("role_name", $id_role);
            })->get();
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        return new RoleResourceCollection($data);
    }
    public function getaccessview($token) {
        try {
            $response = AccessView::where('token', $token)->get();
            $d = collect($response)->map(function($item){
                $data["id_module"] = $item['id_module'];
                $data["sub_menu"] = $item['module_sub_name'];
                $data["xView"] = $item['xView'];
                return $data;
            });
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        if (count($response) == 0){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "Token Not Found"
                    ]
                ]
                ],404));
        }
    
        return new RoleResourceCollection($d);
    }
}
