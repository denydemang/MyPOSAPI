<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseCreateRequest;
use App\Http\Requests\PurchaseUpdateRequest;
use App\Http\Resources\PurchaseResourceCollection;
use App\Models\DetailPurchase;
use App\Models\Purchase;
use App\Models\PurchaseView;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class PurchaseController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }
    public function getall($branchcode, Request $request) {

        $tableName = (new PurchaseView())->getTable();
        $columlist = Schema::getColumnListing($tableName);

        $firstdate =  date("Y-m-d",strtotime(Carbon::now()->firstOfMonth()));
        $lastdate =  date("Y-m-d",strtotime(Carbon::now()->lastOfMonth()));
        $iscredit = $request->get('iscredit'); 
        $isreceived = $request->get('isreceived'); 
        $isapprove = $request->get('isapprove'); 
        $orderBy = (null != $request->get('orderby') && in_array(strtolower($request->get('orderby')),$columlist)) ? $request->get('orderby') : "trans_date";
        $ascdesc = (null != $request->get('ascdesc') && (strtolower($request->get('ascdesc'))== "asc"|| strtolower($request->get('ascdesc'))== "desc"))? $request->get('ascdesc'): "asc";
        $perpage = (null != $request->get('perpage'))? $request->get('perpage') : 10;
        $page = (null != $request->get('page'))? $request->get('page') : 1;
        $startdate = (null != $request->get("startdate"))? date("Y-m-d", strtotime($request->get("startdate"))): $firstdate;
        $enddate = (null != $request->get("enddate")) ? date("Y-m-d", strtotime($request->get("enddate"))): $lastdate;
        try {
            $purchase =PurchaseView::select(
                'branchcode', 'id' , 'trans_no', 
                'trans_date' ,'id_user', 'pic_name' , 
                'id_supplier' ,'number_id_supplier','supplier_name','total_purchase', DB::raw('count(trans_no) as total_product'), 'other_fee', 'ppn',
                'payment_term', 'is_approve','is_credit', 'grand_total'
                )->where("branchcode", $branchcode)
                ->whereBetween("trans_date", [$startdate,$enddate])
                ->when($iscredit !== null, function($query) use($iscredit){
                    $query->where('is_credit', filter_var($iscredit,FILTER_VALIDATE_BOOLEAN));
                })
                ->when($isapprove !== null, function($query) use($isapprove){
                    $query->where('is_approve', filter_var($isapprove, FILTER_VALIDATE_BOOLEAN));
                })
                ->when($isreceived !== null, function($query) use($isreceived){
                    $query->where('is_received', filter_var($isreceived, FILTER_VALIDATE_BOOLEAN));
                })
                ->groupBy('trans_no')
                ->orderBy($orderBy, $ascdesc)->orderBy('id', $ascdesc)
                ->paginate(perPage:$perpage, page:$page);

                // $purchase->withPath(URL::to('/').'/api/purchases/list/int?startdate='.$startdate.'&enddate='.$enddate.'&ascdesc='.$ascdesc.'&orderby='.$orderBy.'&perpage='.$perpage);
                $purchase->withPath($request->fullUrl());
                // return $purchase;
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        return new PurchaseResourceCollection($purchase, "Successfully Get All Purchases");
    
    }
    public function get($branchcode, $id) :JsonResponse {

        try {
            $purchase = PurchaseView::select(
                'branchcode', 'id' , 'trans_no', 
                'trans_date' ,'id_user', 'pic_name' , 
                'id_supplier' ,'number_id_supplier','supplier_name','total_purchase', 'other_fee', 'ppn',
                'payment_term', 'is_approve','is_credit', 'grand_total'
                )
                ->where("branchcode", $branchcode)->where(function($query) use($id){
                    $query->where("trans_no", $id);
                    $query->orWhere("id", $id);
                })->first();
            $item = PurchaseView::select(
                'branchcode' , 'id',
                'trans_no', 'id_detail_purchases',
                'id_product', 'barcode',
                'product_name', 'unit',
                'qty', 'price','total', 'discount','sub_total'

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
            'success' =>'Successfully Get Specific Purchase'
        ])->setStatusCode(200);
    }
    public function search($branchcode,Request $request) : PurchaseResourceCollection {
    
        $key = $request->get('key');
        $tableName = (new PurchaseView())->getTable();
        $columlist = Schema::getColumnListing($tableName);
        $listFilter = ["trans_no", "pic_name", "number_id_supplier", "supplier_name"];
        $filterby =  $request->get('filterby') && in_array(strtolower($request->get('filterby')),$listFilter) ? strtolower($request->get('filterby') ): 'all';
        $iscredit = $request->get('iscredit'); 
        $isapprove = $request->get('isapprove'); 
        $isreceived = $request->get('isreceived');
        $firstdate =  date("Y-m-d",strtotime(Carbon::now()->firstOfMonth()));
        $lastdate =  date("Y-m-d",strtotime(Carbon::now()->lastOfMonth()));
        $orderBy = (null != $request->get('orderby') && in_array(strtolower($request->get('orderby')),$columlist)) ? $request->get('orderby') : "trans_date";
        $ascdesc = (null != $request->get('ascdesc') && (strtolower($request->get('ascdesc'))== "asc"|| strtolower($request->get('ascdesc'))== "desc"))? $request->get('ascdesc'): "asc";
        $perpage = (null != $request->get('perpage'))? $request->get('perpage') : 10;
        $page = (null != $request->get('page'))? $request->get('page') : 1;
        $startdate = (null != $request->get("startdate"))? date("Y-m-d", strtotime($request->get("startdate"))): $firstdate;
        $enddate = (null != $request->get("enddate")) ? date("Y-m-d", strtotime($request->get("enddate"))): $lastdate;

        try {
            $purchase =PurchaseView::select(DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rownumber'),
                'branchcode', 'id' , 'trans_no', 
                'trans_date' ,'id_user', 'pic_name' , 
                'id_supplier' ,'number_id_supplier','supplier_name', 'total_purchase',DB::raw('count(trans_no) as total_product'), 'other_fee', 'ppn',
                'payment_term', 'is_approve','is_credit', 'grand_total'
                )->where("branchcode", $branchcode)
                ->whereBetween("trans_date", [$startdate,$enddate])
                ->when($iscredit !== null, function($query) use($iscredit){
                    $query->where('is_credit', filter_var($iscredit,FILTER_VALIDATE_BOOLEAN));
                })
                ->when($isapprove !== null, function($query) use($isapprove){
                    $query->where('is_approve', filter_var($isapprove, FILTER_VALIDATE_BOOLEAN));
                })
                ->when($isreceived !== null, function($query) use($isreceived){
                    $query->where('is_received', filter_var($isreceived, FILTER_VALIDATE_BOOLEAN));
                })
                ->where(function($query) use ($key, $filterby, $listFilter){
                    
                    if($filterby == "all"){
                        $query->Where('trans_no', 'like', "%{$key}%");
                        $query->orWhere('pic_name', 'like', "%{$key}%");
                        $query->orWhere('number_id_supplier', 'like', "%{$key}%");
                        $query->orWhere('supplier_name', 'like', "%{$key}%");
                    } else {                        
                        foreach($listFilter as $filter){
                            if ($filter == $filterby){
                                $query->orWhere($filter, 'like', "%{$key}%");
                            }
                        }
                    }                  
                })
                ->groupBy('trans_no')->orderBy($orderBy, $ascdesc)->orderBy('id', $ascdesc)->paginate(perPage:$perpage, page:$page);
                $purchase->withPath($request->fullUrl());
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        return new PurchaseResourceCollection($purchase, "Successfully Get Purchase By Search");
    }

    public function create(PurchaseCreateRequest $request) : JsonResponse{
        $dataValidated = $request->validated();
        $dateNow = date("Y-m-d", strtotime(Carbon::now()));
        try {
            DB::beginTransaction();
                $getTransNo = Purchase::select('trans_no')->where("branchcode", $dataValidated["branchcode"])->where("trans_no","like", "PRC-{$dateNow}-%")->orderBy("trans_no", "desc")->first();
                if ($getTransNo){

                    $getLockMax = Purchase::select('trans_no')->where("branchcode", $dataValidated["branchcode"])->where("trans_no", ">=", $getTransNo->trans_no)->lockForUpdate()->orderBy("trans_no", "desc")->first();
                    $getLastDigit = intval(str_replace("PRC-{$dateNow}-","",$getLockMax->trans_no));
                    $newCode = "PRC-{$dateNow}-". sprintf("%03d",$getLastDigit + 1);
                } else {
                    $newCode = "PRC-{$dateNow}-001";
                }

                $purchase = new Purchase();
                $purchase->branchcode = $dataValidated['branchcode'];
                $purchase->trans_no = $newCode;
                $purchase->trans_date = $dataValidated['trans_date'];
                $purchase->id_user = $dataValidated['id_user'];
                $purchase->id_supplier = $dataValidated['id_supplier'];
                $purchase->total = $dataValidated['total'];
                $purchase->other_fee = !empty($dataValidated['other_fee']) ? $dataValidated['other_fee'] : 0;
                $purchase->ppn = !empty($dataValidated['ppn']) ? $dataValidated['ppn'] : 0;
                $purchase->payment_term = !empty($dataValidated['payment_term']) ? $dataValidated['payment_term'] : null;
                $purchase->grand_total = $dataValidated['grand_total'];
                $purchase->is_credit = $dataValidated['is_credit'];
                $purchase->save();

                $items= collect($dataValidated["items"])->transform(function($item) use($purchase){
                    return ['id_purchases' => intval($purchase->id)]+ $item;
                });
                DetailPurchase::insert($items->toArray());
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
                    "purchase" => $purchase,
                    "items" => $items
                ],
                "success" => "Successfully Saved Purchase Transaction"
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
