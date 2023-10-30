<?php

namespace App\Http\Controllers;

use App\Http\Resources\SalesResourceCollection;
use App\Models\SalesView;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SalesController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }
    public function getall($branchcode, Request $request) {

        $tableName = (new SalesView())->getTable();
        $columlist = Schema::getColumnListing($tableName);

        $firstdate =  date("Y-m-d",strtotime(Carbon::now()->firstOfMonth()));
        $lastdate =  date("Y-m-d",strtotime(Carbon::now()->lastOfMonth()));
        $iscredit = $request->get('iscredit'); 
        $orderBy = (null != $request->get('orderby') && in_array(strtolower($request->get('orderby')),$columlist)) ? $request->get('orderby') : "trans_date";
        $ascdesc = (null != $request->get('ascdesc') && (strtolower($request->get('ascdesc'))== "asc"|| strtolower($request->get('ascdesc'))== "desc"))? $request->get('ascdesc'): "asc";
        $perpage = (null != $request->get('perpage'))? $request->get('perpage') : 10;
        $page = (null != $request->get('page'))? $request->get('page') : 1;
        $startdate = (null != $request->get("startdate"))? date("Y-m-d", strtotime($request->get("startdate"))): $firstdate;
        $enddate = (null != $request->get("enddate")) ? date("Y-m-d", strtotime($request->get("enddate"))): $lastdate;
        try {
            $sales =SalesView::select(
                'branchcode', 'id' , 'trans_no', 
                'trans_date' ,'id_cust','cust_no','cust_name','id_user', 'username' ,'pic_name' , 'role_name',
                'total','sales_discount', 'sales_ppn', 'sales_notes',
                'is_sales_credit',DB::raw('count(trans_no) as total_product'), 'grand_total','paid', 'change_amount'
                )->where("branchcode", $branchcode)
                ->whereBetween("trans_date", [$startdate,$enddate])
                ->when($iscredit, function($query, string $iscredit){
                    $query->where('is_sales_credit', filter_var($iscredit,FILTER_VALIDATE_BOOLEAN));
                })
                ->groupBy('trans_no')
                ->orderBy($orderBy, $ascdesc)->orderBy('id', $ascdesc)
                ->paginate(perPage:$perpage, page:$page);

                // $sales->withPath(URL::to('/').'/api/purchases/list/int?startdate='.$startdate.'&enddate='.$enddate.'&ascdesc='.$ascdesc.'&orderby='.$orderBy.'&perpage='.$perpage);
                $sales->withPath($request->fullUrl());
                // return $sales;
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        return new SalesResourceCollection($sales, "Successfully Get All Sales");
    
    }
    public function get($branchcode, $id) :JsonResponse{

        try {
            $sales = SalesView::select(
                'branchcode', 'id' , 'trans_no', 
                'trans_date' ,'id_cust','cust_no','cust_name','id_user', 'username' ,'pic_name' , 'role_name',
                'total' , 'sales_discount', 'sales_ppn', 'sales_notes',
                'is_sales_credit','grand_total', 'paid', 'change_amount'
                )
                ->where("branchcode", $branchcode)->where(function($query) use($id){
                    $query->where("trans_no", $id);
                    $query->orWhere("id", $id);
                })->first();
            $item = SalesView::select(
                'branchcode' , 'id',
                'trans_no', 'id_detail_sales',
                'id_product', 'barcode',
                'product_name', 'unit',
                'qty', 'price', 'item_discount','sub_total'

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
                "sales" => $sales,
                "item" =>$item
            ],
            'total_product' =>$item->count('trans_no'),
            'success' =>'Successfully Get Specific Sales Transaction'
        ])->setStatusCode(200);
    }
    public function search($branchcode,Request $request) : SalesResourceCollection {
    
        $key = $request->get('key');
        $tableName = (new SalesView())->getTable();
        $columlist = Schema::getColumnListing($tableName);

        $iscredit = $request->get('iscredit'); 
        $firstdate =  date("Y-m-d",strtotime(Carbon::now()->firstOfMonth()));
        $lastdate =  date("Y-m-d",strtotime(Carbon::now()->lastOfMonth()));
        $orderBy = (null != $request->get('orderby') && in_array(strtolower($request->get('orderby')),$columlist)) ? $request->get('orderby') : "trans_date";
        $ascdesc = (null != $request->get('ascdesc') && (strtolower($request->get('ascdesc'))== "asc"|| strtolower($request->get('ascdesc'))== "desc"))? $request->get('ascdesc'): "asc";
        $perpage = (null != $request->get('perpage'))? $request->get('perpage') : 10;
        $page = (null != $request->get('page'))? $request->get('page') : 1;
        $startdate = (null != $request->get("startdate"))? date("Y-m-d", strtotime($request->get("startdate"))): $firstdate;
        $enddate = (null != $request->get("enddate")) ? date("Y-m-d", strtotime($request->get("enddate"))): $lastdate;

        try {
            $sales =SalesView::select(
                'branchcode', 'id' , 'trans_no', 
                'trans_date' ,'id_cust','cust_no','cust_name','id_user', 'username' ,'pic_name' , 'role_name',
                'total','sales_discount', 'sales_ppn', 'sales_notes',
                'is_sales_credit',DB::raw('count(trans_no) as total_product'), 'grand_total','paid', 'change_amount'
                )->where("branchcode", $branchcode)
                ->whereBetween("trans_date", [$startdate,$enddate])
                ->when($iscredit, function($query, string $iscredit){
                    $query->where('is_sales_credit', filter_var($iscredit,FILTER_VALIDATE_BOOLEAN));
                })
                ->where(function($query) use ($key){
                    $query->Where('trans_no', 'like', "%{$key}%");
                    $query->orWhere('cust_name', 'like', "%{$key}%");
                    $query->orWhere('username', 'like', "%{$key}%");
                    $query->orWhere('pic_name', 'like', "%{$key}%");
                    $query->orWhere('role_name', 'like', "%{$key}%");
                })
                ->groupBy('trans_no')->orderBy($orderBy, $ascdesc)->orderBy('id', $ascdesc)->paginate(perPage:$perpage, page:$page);
                $sales->withPath($request->fullUrl());
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        return new SalesResourceCollection($sales, "Successfully Get Sales By Search");
    }

    // public function create(PurchaseCreateRequest $request) : JsonResponse{
    //     $dataValidated = $request->validated();
    //     $dateNow = date("Y-m-d", strtotime(Carbon::now()));
    //     try {
    //         DB::beginTransaction();
    //             $getTransNo = Purchase::select('trans_no')->where("branchcode", $dataValidated["branchcode"])->where("trans_no","like", "PRC-{$dateNow}-%")->orderBy("trans_no", "desc")->first();
    //             if ($getTransNo){

    //                 $getLockMax = Purchase::select('trans_no')->where("branchcode", $dataValidated["branchcode"])->where("trans_no", ">=", $getTransNo->trans_no)->lockForUpdate()->orderBy("trans_no", "desc")->first();
    //                 $getLastDigit = intval(str_replace("PRC-{$dateNow}-","",$getLockMax->trans_no));
    //                 $newCode = "PRC-{$dateNow}-". sprintf("%03d",$getLastDigit + 1);
    //             } else {
    //                 $newCode = "PRC-{$dateNow}-001";
    //             }

    //             $purchase = new Purchase();
    //             $purchase->branchcode = $dataValidated['branchcode'];
    //             $purchase->trans_no = $newCode;
    //             $purchase->trans_date = $dataValidated['trans_date'];
    //             $purchase->id_user = $dataValidated['id_user'];
    //             $purchase->id_supplier = $dataValidated['id_supplier'];
    //             $purchase->discount = !empty($dataValidated['discount'])? $dataValidated['discount'] : 0;
    //             $purchase->other_fee = !empty($dataValidated['other_fee']) ? $dataValidated['other_fee'] : 0;
    //             $purchase->ppn = !empty($dataValidated['ppn']) ? $dataValidated['ppn'] : 0;
    //             $purchase->payment_term = !empty($dataValidated['payment_term']) ? $dataValidated['payment_term'] : null;
    //             $purchase->total = $dataValidated['total'];
    //             $purchase->is_credit = $dataValidated['is_credit'];
    //             $purchase->save();

    //             $items= collect($dataValidated["items"])->transform(function($item) use($purchase){
    //                 return ['id_purchases' => intval($purchase->id)]+ $item;
    //             });
    //             DetailPurchase::insert($items->toArray());
    //         DB::commit();
            
    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         throw new HttpResponseException(response([
    //             "errors" => [
    //                 "general" => [
    //                     $th->getMessage()
    //                 ]
    //             ]
    //             ],500));
    //     }


    //     return response()->json(
    //         [
    //             "data" => [
    //                 "purchase" => $purchase,
    //                 "items" => $items
    //             ],
    //             "success" => "Successfully Saved Purchase Transaction"
    //         ]
    //     )->setStatusCode(200);

    // }
    // public function update($branchcode,$key,PurchaseUpdateRequest $request) :JsonResponse
    // {
    //     $dataValidated = $request->validated();
    //     try {
    //         $purchase = Purchase::where("branchcode", $branchcode)->where(function($query) use($key){
    //             $query->where("trans_no", strval($key));
    //             $query->orWhere("id", $key);
    //         })->first();
    //         if($purchase){
    //             DB::beginTransaction();
                    
    //                 $purchase = Purchase::where("branchcode", $branchcode)->where(function($query) use($key){
    //                     $query->where("trans_no", $key);
    //                     $query->orWhere("id", $key);
    //                 })->lockForUpdate()->first();
    //                 $purchase->trans_date = $dataValidated['trans_date'];
    //                 $purchase->id_user = $dataValidated['id_user'];
    //                 $purchase->id_supplier = $dataValidated['id_supplier'];
    //                 $purchase->discount = $dataValidated['discount'];
    //                 $purchase->other_fee = $dataValidated['other_fee'];
    //                 $purchase->ppn = $dataValidated['ppn'];
    //                 $purchase->total = $dataValidated['total'];
    //                 $purchase->payment_term = $dataValidated['payment_term'];
    //                 $purchase->is_credit = $dataValidated['is_credit'];
    //                 $purchase->update();
    //                 DetailPurchase::where("id_purchases" ,$purchase->id)
    //                 ->lockForUpdate()->delete();
    //                 $items= collect($dataValidated["items"])->transform(function($item) use($purchase){
    //                     return ['id_purchases' => intval($purchase->id)]+ $item;
    //                 });
    //                 DetailPurchase::insert($items->toArray());
    //             DB::commit();
    //         }
    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         throw new HttpResponseException(response([
    //             "errors" => [
    //                 "general" => [
    //                     $th->getMessage()
    //                 ]
    //             ]
    //             ],500));

    //     }
    //     if (!$purchase){
    //         throw new HttpResponseException(response([
    //             "errors" => [
    //                 "general" => [
    //                     "ID or No_Trans Is Not Found"
    //                 ]
    //             ]
    //         ],500));
    //     }

    //     return response()->json([
    //         "data" =>[
    //             "purchase" => $purchase,
    //             "items" => $items
    //         ],
    //         "success" => "Successfully Updated Purchase Transaction"
    //     ])->setStatusCode(200);
    // }

    // public function delete($branchcode, $key): JsonResponse
    // {
    //     try {
    //         $purchase = Purchase::where("branchcode", $branchcode)->where(function($query) use($key){
    //             $query->where("trans_no", $key);
    //             $query->orWhere("id", $key);
    //         })->first();

    //         if($purchase){
    //             Purchase::where("branchcode", $branchcode)->where(function($query) use($key){
    //                 $query->where("trans_no", $key);
    //                 $query->orWhere("id", $key);
    //             })->delete();
    //         }
    //     } catch (\Throwable $th) {
    //         throw new HttpResponseException(response([
    //             "errors" => [
    //                 "general" => [
    //                     $th->getMessage()
    //                 ]
    //             ]
    //             ],500));
    //     }
    //     if(!$purchase){
    //         throw new HttpResponseException(response([
    //             "errors" => [
    //                 "general" => [
    //                     "ID or No_Trans Is Not Found"
    //                 ]
    //             ]
    //         ],500));
    //     }
    //     return response()->json([
    //         "data" => $purchase,
    //         "success" => "Successfully Deleted Purchase Transaction"
    //     ]);
    // }
    // public function approve($branchcode, $key){

    //     try {
    //         $purchase = Purchase::where("branchcode", $branchcode)->where(function($query) use($key){
    //             $query->where("trans_no", $key);
    //             $query->orWhere("id", $key);
    //         })->first();
    //         if($purchase){
    //             Purchase::where("branchcode", $branchcode)->where(function($query) use($key){
    //                 $query->where("trans_no", $key);
    //                 $query->orWhere("id", $key);
    //             })->update([
    //                 "is_approve" => 1
    //             ]);
    //         }

    //     } catch (\Throwable $th) {
    //         throw new HttpResponseException(response([
    //             "errors" => [
    //                 "general" => [
    //                     $th->getMessage()
    //                 ]
    //             ]
    //             ],500));
    //     }
    //     if (!$purchase){
    //         throw new HttpResponseException(response([
    //             "errors" => [
    //                 "general" => [
    //                     "ID or No_Trans Is Not Found"
    //                 ]
    //             ]
    //         ],500));
    //     }

    //     return response()->json([
    //         'data' => [
    //             'trans_no' => $purchase->trans_no,
    //             'is_approve' => $purchase->is_approve
    //         ],
    //         "success" => "successfully Approved Transaction"
    //         ]);
    // }
}
