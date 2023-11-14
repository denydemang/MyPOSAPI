<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseReturnCreateRequest;
use App\Http\Resources\PurchaseReturnResourceCollection;
use App\Models\DetailPurchaseReturn;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnView;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PurchaseReturnController extends Controller
{
    
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }
    public function getall($branchcode, Request $request) :PurchaseReturnResourceCollection {

        $tableName = (new PurchaseReturnView())->getTable();
        $columlist = Schema::getColumnListing($tableName);

        $firstdate =  date("Y-m-d",strtotime(Carbon::now()->firstOfMonth()));
        $lastdate =  date("Y-m-d",strtotime(Carbon::now()->lastOfMonth()));
        $isapprove = $request->get('isapprove'); 
        $orderBy = (null != $request->get('orderby') && in_array(strtolower($request->get('orderby')),$columlist)) ? $request->get('orderby') : "trans_date";
        $ascdesc = (null != $request->get('ascdesc') && (strtolower($request->get('ascdesc'))== "asc"|| strtolower($request->get('ascdesc'))== "desc"))? $request->get('ascdesc'): "asc";
        $perpage = (null != $request->get('perpage'))? $request->get('perpage') : 10;
        $page = (null != $request->get('page'))? $request->get('page') : 1;
        $startdate = (null != $request->get("startdate"))? date("Y-m-d", strtotime($request->get("startdate"))): $firstdate;
        $enddate = (null != $request->get("enddate")) ? date("Y-m-d", strtotime($request->get("enddate"))): $lastdate;
        try {
            $purchasereturn =PurchaseReturnView::select(
                'branchcode', 'id' , 'trans_no', 
                'trans_date' ,'id_grn', 'grn_trans_no' , 
                'id_purchase' ,'purchase_trans_no','reason', DB::raw('count(trans_no) as total_product'),
                'total', 'is_approve'
                )->where("branchcode", $branchcode)
                ->whereBetween("trans_date", [$startdate,$enddate])
                ->when($isapprove, function($query, string $isapprove){
                    $query->where('is_approve', filter_var($isapprove, FILTER_VALIDATE_BOOLEAN));
                })->groupBy('trans_no')
                ->orderBy($orderBy, $ascdesc)->orderBy('id', $ascdesc)
                ->paginate(perPage:$perpage, page:$page);
                $purchasereturn->withPath($request->fullUrl());
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        return new PurchaseReturnResourceCollection($purchasereturn, "Successfully Get All Purchases Return");
    
    }
    public function get($branchcode, $id) :JsonResponse {

        try {
            $purchase = PurchaseReturnView::select(
                'branchcode', 'id' , 'trans_no', 
                'trans_date' ,'id_grn', 'grn_trans_no' , 
                'id_purchase' ,'purchase_trans_no','reason','total', 'is_approve'
                )
                ->where("branchcode", $branchcode)->where(function($query) use($id){
                    $query->where("trans_no", $id);
                    $query->orWhere("id", $id);
                })->first();
            $item = PurchaseReturnView::select(
                'branchcode' , 'id',
                'trans_no', 'id_detail_purchase_return',
                'id_product', 'barcode',
                'product_name', 'id_unit',
                'qty', 'cogs', 'sub_total'

            )->where('branchcode', $branchcode)->where(function($query) use ($id){
                $query->where('trans_no', $id);
                $query->orWhere('id' , $id);
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
        return response()->json([
            'data' => [
                "purchase" => $purchase,
                "item" =>$item
            ],
            'total_product' =>$item->count('trans_no'),
            'success' =>'Successfully Get Specific Purchase Return'
        ])->setStatusCode(200);
    }
    public function search($branchcode,Request $request) : PurchaseReturnResourceCollection {
    
        $key = $request->get('key');
        $tableName = (new PurchaseReturnView())->getTable();
        $columlist = Schema::getColumnListing($tableName);

        $isapprove = $request->get('isapprove'); 
        $firstdate =  date("Y-m-d",strtotime(Carbon::now()->firstOfMonth()));
        $lastdate =  date("Y-m-d",strtotime(Carbon::now()->lastOfMonth()));
        $orderBy = (null != $request->get('orderby') && in_array(strtolower($request->get('orderby')),$columlist)) ? $request->get('orderby') : "trans_date";
        $ascdesc = (null != $request->get('ascdesc') && (strtolower($request->get('ascdesc'))== "asc"|| strtolower($request->get('ascdesc'))== "desc"))? $request->get('ascdesc'): "asc";
        $perpage = (null != $request->get('perpage'))? $request->get('perpage') : 10;
        $page = (null != $request->get('page'))? $request->get('page') : 1;
        $startdate = (null != $request->get("startdate"))? date("Y-m-d", strtotime($request->get("startdate"))): $firstdate;
        $enddate = (null != $request->get("enddate")) ? date("Y-m-d", strtotime($request->get("enddate"))): $lastdate;

        try {
            $purchasereturn =PurchaseReturnView::select(
                'branchcode', 'id' , 'trans_no', 
                'trans_date' ,'id_grn', 'grn_trans_no' , 
                'id_purchase' ,'purchase_trans_no','reason','total', 'is_approve'
                )->where("branchcode", $branchcode)
                ->whereBetween("trans_date", [$startdate,$enddate])
                ->when($isapprove, function($query, string $isapprove){
                    $query->where('is_approve', filter_var($isapprove, FILTER_VALIDATE_BOOLEAN));
                })
                ->where(function($query) use ($key){
                    $query->Where('trans_no', 'like', "%{$key}%");
                    $query->orWhere('grn_trans_no', 'like', "%{$key}%");
                    $query->orWhere('purchase_trans_no', 'like', "%{$key}%");
                })
                ->groupBy('trans_no')->orderBy($orderBy, $ascdesc)->orderBy('id', $ascdesc)->paginate(perPage:$perpage, page:$page);
                $purchasereturn->withPath($request->fullUrl());
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        return new PurchaseReturnResourceCollection($purchasereturn, "Successfully Get Purchase Return By Search");
    }

    public function create(PurchaseReturnCreateRequest $request) : JsonResponse{
        $dataValidated = $request->validated();
        $dateNow = date("Y-m-d", strtotime(Carbon::now()));
        try {
            DB::beginTransaction();
                $getTransNo = PurchaseReturn::select('trans_no')->where("branchcode", $dataValidated["branchcode"])->where("trans_no","like", "PRTN-{$dateNow}-%")->orderBy("trans_no", "desc")->first();
                if ($getTransNo){

                    $getLockMax = PurchaseReturn::select('trans_no')->where("branchcode", $dataValidated["branchcode"])->where("trans_no", ">=", $getTransNo->trans_no)->lockForUpdate()->orderBy("trans_no", "desc")->first();
                    $getLastDigit = intval(str_replace("PRTN-{$dateNow}-","",$getLockMax->trans_no));
                    $newCode = "PRTN-{$dateNow}-". sprintf("%03d",$getLastDigit + 1);
                } else {
                    $newCode = "PRTN-{$dateNow}-001";
                }

                $purchasereturn = new PurchaseReturn();
                $purchasereturn->branchcode = $dataValidated['branchcode'];
                $purchasereturn->trans_no = $newCode;
                $purchasereturn->trans_date = $dataValidated['trans_date'];
                $purchasereturn->id_grn = $dataValidated['id_grn'];
                $purchasereturn->reasonn = $dataValidated['reasonn'];
                $purchasereturn->total = $dataValidated['total'];
                $purchasereturn->save();

                $items= collect($dataValidated["items"])->transform(function($item) use($purchasereturn){
                    return ['id_purchase_return' => intval($purchasereturn->id)]+ $item;
                });
                DetailPurchaseReturn::insert($items->toArray());
                foreach($items as $item){
                    $StockController = new StockController;
                    $StockController->stockout($item['id_product'],$item['qty'],$purchasereturn->trans_no,$purchasereturn->branchcode, $purchasereturn->trans_date);
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


        return response()->json(
            [
                "data" => [
                    "purchase" => $purchasereturn,
                    "items" => $items
                ],
                "success" => "Successfully Saved Purchase Return Transaction"
            ]
        )->setStatusCode(201);

    }
    public function update($branchcode,$key,PurchaseUpdateRequest $request) :JsonResponse
    {
        $dataValidated = $request->validated();
        try {
            $purchase = Purchase::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", strval($key));
                $query->orWhere("id", $key);
            })->first();
            if($purchase){
                DB::beginTransaction();
                    
                    $purchase = Purchase::where("branchcode", $branchcode)->where(function($query) use($key){
                        $query->where("trans_no", $key);
                        $query->orWhere("id", $key);
                    })->lockForUpdate()->first();
                    $purchase->trans_date = $dataValidated['trans_date'];
                    $purchase->id_user = $dataValidated['id_user'];
                    $purchase->id_supplier = $dataValidated['id_supplier'];
                    $purchase->total = $dataValidated['total'];
                    $purchase->discount = $dataValidated['discount'];
                    $purchase->other_fee = $dataValidated['other_fee'];
                    $purchase->ppn = $dataValidated['ppn'];
                    $purchase->grand_total = $dataValidated['grand_total'];
                    $purchase->payment_term = $dataValidated['payment_term'];
                    $purchase->is_credit = $dataValidated['is_credit'];
                    $purchase->update();
                    DetailPurchase::where("id_purchases" ,$purchase->id)
                    ->lockForUpdate()->delete();
                    $items= collect($dataValidated["items"])->transform(function($item) use($purchase){
                        return ['id_purchases' => intval($purchase->id)]+ $item;
                    });
                    DetailPurchase::insert($items->toArray());
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
        if (!$purchase){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "ID or No_Trans Is Not Found"
                    ]
                ]
            ],404));
        }

        return response()->json([
            "data" =>[
                "purchase" => $purchase,
                "items" => $items
            ],
            "success" => "Successfully Updated Purchase Transaction"
        ])->setStatusCode(200);
    }

    public function delete($branchcode, $key): JsonResponse
    {
        try {
            $purchase = Purchase::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", $key);
                $query->orWhere("id", $key);
            })->first();

            if($purchase){  
                Purchase::where("branchcode", $branchcode)->where(function($query) use($key){
                    $query->where("trans_no", $key);
                    $query->orWhere("id", $key);
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
        if(!$purchase){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "ID or No_Trans Is Not Found"
                    ]
                ]
            ],404));
        }
        return response()->json([
            "data" => $purchase,
            "success" => "Successfully Deleted Purchase Transaction"
        ])->setStatusCode(200);
    }
    public function approve($branchcode, $key){

        try {
            $purchase = Purchase::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", $key);
                $query->orWhere("id", $key);
            })->first();
            if($purchase){
                $purchase->is_approve = 1;
                $purchase->update();
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
        if (!$purchase){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "ID or No_Trans Is Not Found"
                    ]
                ]
            ],404));
        }

        return response()->json([
            'data' => [
                'trans_no' => $purchase->trans_no,
                'is_approve' => $purchase->is_approve
            ],
            "success" => "successfully Approved Transaction"
            ])->setStatusCode(200);
    }
}
