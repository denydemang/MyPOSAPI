<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleCreateRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RoleDetailResource;
use App\Http\Resources\RoleResourceCollection;
use App\Models\Access;
use App\Models\AccessView;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function get($branchcode,$id_role) : RoleDetailResource{
        try {
            $role =  Role::where("branchcode", $branchcode)->where(function($query) use($id_role){
                $query->where("id", $id_role);
                $query->orWhere("name", $id_role);
            })->first();
            $accessview = AccessView::where('branchcode',$branchcode)->where(function($query) use($id_role){
                $query->where("id_role", $id_role);
                $query->orWhere("role_name", $id_role);
            })->groupBy('id_module')->get()->setVisible(['id_module', 'module_name', 'module_sub_name', 'xView', 'xApprove', 'xCreate', 'xDelete', 'xUpdate']);
            $data = [
                'branchcode' => $role['branchcode'],
                'id' => $role['id'],
                'name' => $role['name'],
                'access' => $accessview
            ];
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        return new RoleDetailResource($data, "Successfully Get Data Role");
    }
    public function create(RoleCreateRequest $request) {

        $dataValidated = $request->validated();

        try {
            DB::beginTransaction();
            $dataRole = [
                'name' => $dataValidated['name'],
                'branchcode' => $dataValidated['branchcode'],
            ];
            $role = new Role($dataRole);
            $role->save();

            $access= collect($dataValidated["access"])->transform(function($item) use($role, $dataValidated){
                return [
                    
                    'id_role' => intval($role->id),
                    'branchcode' => $dataValidated['branchcode']
                
                ]+ $item;
            });

            Access::insert($access->toArray());
            DB::commit();
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
        return response()->json(
            [
                "data" => [
                    "role" => $role,
                    "access" => $access
                ],
                "success" => "Successfully Saved Role"
            ]
        )->setStatusCode(201);

    } 
    public function update($branchcode, $id, RoleUpdateRequest $request) : JsonResponse{
        try {
            $dataValidated =$request->validated();
            DB::beginTransaction();
            $role = Role::where("branchcode", $branchcode)->where(function($query) use($id){
                $query->where("id", $id);
                $query->orWhere("name", $id);
            })->first();
            if ($role){
                $role->name = $dataValidated["name"];
                $role->update();
                Access::where("id_role", $role->id)->delete();
                $access= collect($dataValidated["access"])->transform(function($item) use($role ,$branchcode){

                    return [
                        'id_role' => intval($role->id),
                        'branchcode' => $branchcode,
                        
                        ]+ $item;
                });
    
                Access::insert($access->toArray());
            }
            DB::commit();
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
        if(!$role){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "ID or Name Role Is Not Found"
                    ]
                ]
                ],404));
        }

        return response()->json(
            [
                "data" => [
                    "role" => $role,
                    "access" => $access
                ],
                "success" => "Successfully Updated Role"
            ]
        )->setStatusCode(200);

    }
    public function delete($branchcode, $id) : JsonResponse{
        try {
            DB::beginTransaction();
            $role = Role::where("branchcode", $branchcode)->where(function($query) use($id){
                $query->where("id", $id);
                $query->orWhere("name", $id);
            })->first();
            if ($role){
                $role->delete();
            }
            DB::commit();
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
        if(!$role){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "ID or Name Role Is Not Found"
                    ]
                ]
                ],404));
        }

        return response()->json(
            [
                "data" => $role,
                "success" => "Successfully Deleted Role"
            ]
        )->setStatusCode(200);

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
