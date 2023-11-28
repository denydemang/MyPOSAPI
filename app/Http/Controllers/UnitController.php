<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UnitView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function getall() :JsonResponse{
        $dataunit =UnitView::all();

        return response()->json(
            [
                'data' =>  $dataunit,
                'success' => 'Successfully Get All Unit'

            ])->setStatusCode(200);
    }
    public function getgroup($id_unit) :JsonResponse{

        $groupname = UnitView::where('id_unit', $id_unit)->first(['group_name'])->group_name;

        $dataunit = UnitView::where('group_name', $groupname)->get();
        return response()->json([
            [
                'data' =>  $dataunit,
                'success' => 'Successfully Get Group Unit'

            ]
        ])->setStatusCode(200);
    }
    public function getdefault() : JsonResponse{
        $dataunit = UnitView::where('convert_value', 1)->get();
        return response()->json(
            [
                'data' => $dataunit,
                'success' => 'Successfully Get Unit Default'
            ])->setStatusCode(200);
    }
}
