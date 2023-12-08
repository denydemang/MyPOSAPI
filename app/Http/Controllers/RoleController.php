<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleCreateRequest;
use App\Http\Resources\RoleResourceCollection;
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
}
