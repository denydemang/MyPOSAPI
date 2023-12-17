<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function getall() :JsonResponse{
        try {
            $module = Module::all();
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        return response()->json([
            "data" => $module,
            "success" => "Successfully Get All Modules"
        ])->setStatusCode(200);
    }
}
