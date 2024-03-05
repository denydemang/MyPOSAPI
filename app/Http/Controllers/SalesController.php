<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesCreateRequest;
use App\Http\Requests\SalesUpdateRequest;
use App\Http\Resources\SalesResourceCollection;
use App\Models\COGS;
use App\Models\DetailSales;
use App\Models\LOGINVOUT;
use App\Models\ProductView;
use App\Models\Sales;
use App\Models\SalesView;
use App\Models\Stock;
use Carbon\Carbon;
use Exception;
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
        $isapprove = $request->get('isapprove'); 
        $orderBy = (null != $request->get('orderby') && in_array(strtolower($request->get('orderby')),$columlist)) ? $request->get('orderby') : "trans_date";
        $ascdesc = (null != $request->get('ascdesc') && (strtolower($request->get('ascdesc'))== "asc"|| strtolower($request->get('ascdesc'))== "desc"))? $request->get('ascdesc'): "asc";
        $perpage = (null != $request->get('perpage'))? $request->get('perpage') : 10;
        $page = (null != $request->get('page'))? $request->get('page') : 1;
        $startdate = (null != $request->get("startdate"))? date("Y-m-d", strtotime($request->get("startdate"))): $firstdate;
        $enddate = (null != $request->get("enddate")) ? date("Y-m-d", strtotime($request->get("enddate"))): $lastdate;
        try {
            $sales =SalesView::select( DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rownumber'),
                'branchcode', 'id' , 'trans_no', 
                'trans_date' ,'id_cust','cust_no','cust_name','id_user', 'username' ,'pic_name' , 'role_name',
                'total_sales', 'sales_ppn','percent_ppn','other_fee', 'sales_notes',
                'is_sales_credit',DB::raw('count(trans_no) as total_product'), 'grand_total','paid', 'change_amount' ,'is_approve'
                )->where("branchcode", $branchcode)
                ->whereBetween("trans_date", [$startdate,$enddate])
                ->when($iscredit !== null, function($query) use($iscredit){
                    $query->where('is_sales_credit', filter_var($iscredit,FILTER_VALIDATE_BOOLEAN));
                })
                ->when($isapprove !== null, function($query) use($isapprove){
                    $query->where('is_approve', filter_var($isapprove, FILTER_VALIDATE_BOOLEAN));
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
                'total_sales' , 'sales_ppn','percent_ppn', 'other_fee','sales_notes',
                'is_sales_credit','grand_total', 'paid', 'change_amount', 'is_approve'
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
                "sales" => $sales,
                "item" =>$item,
                'total_product' =>$item->count('trans_no'),
            ],
            'success' =>'Successfully Get Specific Sales Transaction'
        ])->setStatusCode(200);
    }
    public function search($branchcode,Request $request) : SalesResourceCollection {
    
        $key = $request->get('key');
        $tableName = (new SalesView())->getTable();
        $columlist = Schema::getColumnListing($tableName);
        $listFilter = ["trans_no", "pic_name", "cust_no", "cust_name"];
        $filterby =  $request->get('filterby') && in_array(strtolower($request->get('filterby')),$listFilter) ? strtolower($request->get('filterby') ): 'all';
        $iscredit = $request->get('iscredit'); 
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
            $sales =SalesView::select(DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rownumber'),
                'branchcode', 'id' , 'trans_no', 
                'trans_date' ,'id_cust','cust_no','cust_name','id_user', 'username' ,'pic_name' , 'role_name',
                'total_sales', 'sales_ppn', 'percent_ppn','other_fee','sales_notes',
                'is_sales_credit',DB::raw('count(trans_no) as total_product'), 'grand_total','paid', 'change_amount','is_approve'
                )->where("branchcode", $branchcode)
                ->whereBetween("trans_date", [$startdate,$enddate])
                ->when($iscredit !== null, function($query) use($iscredit){
                    $query->where('is_sales_credit', filter_var($iscredit,FILTER_VALIDATE_BOOLEAN));
                })
                ->when($isapprove !== null, function($query) use($isapprove){
                    $query->where('is_approve', filter_var($isapprove, FILTER_VALIDATE_BOOLEAN));
                })
                ->where(function($query) use ($key, $filterby, $listFilter){
                    
                    if($filterby == "all"){
                        $query->Where('trans_no', 'like', "%{$key}%");
                        $query->orWhere('pic_name', 'like', "%{$key}%");
                        $query->orWhere('cust_no', 'like', "%{$key}%");
                        $query->orWhere('cust_name', 'like', "%{$key}%");
                        $query->orWhere('pic_name', 'like', "%{$key}%");
                    } else {                        
                        foreach($listFilter as $filter){
                            if ($filter == $filterby){
                                $query->orWhere($filter, 'like', "%{$key}%");
                            }
                        }
                    }                  
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
        return new SalesResourceCollection($sales, "Successfully Get Sales Transaction By Search");
    }
    public function create(SalesCreateRequest $request) :JsonResponse
    {
        $dataValidated = $request->validated();
        $dateNow = date("Y-m-d", strtotime(Carbon::now()));
        try {
            DB::beginTransaction();

                $getTransNo = Sales::select('trans_no')->where("branchcode", $dataValidated["branchcode"])->where("trans_no","like", "SLS-{$dateNow}-%")->orderBy("trans_no", "desc")->first();
                if ($getTransNo){

                    $getLockMax = Sales::select('trans_no')->where("branchcode", $dataValidated["branchcode"])->where("trans_no", ">=", $getTransNo->trans_no)->lockForUpdate()->orderBy("trans_no", "desc")->first();
                    $getLastDigit = intval(str_replace("SLS-{$dateNow}-","",$getLockMax->trans_no));
                    $newCode = "SLS-{$dateNow}-". sprintf("%03d",$getLastDigit + 1);
                } else {
                    $newCode = "SLS-{$dateNow}-001";
                }
                $sales = new Sales();
                $sales->branchcode = $dataValidated['branchcode'];
                $sales->trans_no = $newCode;
                $sales->trans_date = $dataValidated['trans_date'];
                $sales->id_cust = $dataValidated['id_cust'];
                $sales->id_user = $dataValidated['id_user'];
                $sales->total = $dataValidated['total'];
                $sales->ppn = !empty($dataValidated['ppn']) ? $dataValidated['ppn'] : 0;
                $sales->percent_ppn = !empty($dataValidated['percent_ppn']) ? $dataValidated['percent_ppn'] : 0;
                $sales->notes = !empty($dataValidated['notes']) ? $dataValidated['notes'] : null;
                $sales->grand_total = $dataValidated['grand_total'];
                $sales->paid = $dataValidated['paid'];
                $sales->change_amount = $dataValidated['is_credit'];
                $sales->is_credit = $dataValidated['is_credit'];
                $sales->save();

                $items= collect($dataValidated["items"])->transform(function($item) use($sales){
                    return ['id_sales' => intval($sales->id)]+ $item;
                });
                DetailSales::insert($items->toArray());  

                foreach ($dataValidated['items'] as $item) {
                    $checkstock = new StockController();
                    $checkstock->stockout(intval($item['id_product']),intval($item['qty']), $sales->trans_no,$sales->branchcode, $sales->trans_date);   
                };
                $Cogs = LOGINVOUT::selectRaw("qty * price as cogs")->where("branchcode", $sales->branchcode)->where('ref_no', $sales->trans_no)->get();
                $totalCogs = $Cogs->sum('cogs');
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
                    "sales" => $sales,
                    "items" => $items,
                    "cogs" => $totalCogs
                ],
                "success" => "Successfully Saved Sales Transaction"
            ]
        )->setStatusCode(201);
    }

    public function update($branchcode,$key,SalesUpdateRequest $request) :JsonResponse
    {
        $dataValidated = $request->validated();

        try {
            $sales = Sales::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", strval($key));
                $query->orWhere("id", $key);
            })->first();

            if($sales){
                DB::beginTransaction();
                $sales = Sales::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", $key);
                $query->orWhere("id", $key);})->lockForUpdate()->first();
                
                $sales->trans_date = $dataValidated['trans_date'];
                $sales->id_cust = $dataValidated['id_cust'];
                $sales->id_user = $dataValidated['id_user'];
                $sales->ppn = !empty($dataValidated['ppn']) ? $dataValidated['ppn'] : 0;
                $sales->percent_ppn = !empty($dataValidated['percent_ppn']) ? $dataValidated['percent_ppn'] : 0;
                $sales->notes = !empty($dataValidated['notes']) ? $dataValidated['notes'] : null;
                $sales->grand_total = $dataValidated['grand_total'];
                $sales->paid = $dataValidated['paid'];
                $sales->change_amount = $dataValidated['change_amount'];
                $sales->is_credit = $dataValidated['is_credit'];
                $sales->update();
                DetailSales::where("id_sales" ,$sales->id)->delete();
                $items= collect($dataValidated["items"])->transform(function($item) use($sales){
                    return ['id_sales' => intval($sales->id)]+ $item;
                });
                DetailSales::insert($items->toArray());
                foreach ($dataValidated['items'] as $item) {
                    $checkstock = new StockController();
                    $checkstock->updatestockout(intval($item['id_product']),intval($item['qty']), $sales->trans_no,$sales->branchcode, $sales->trans_date);   
                };
                $Cogs = LOGINVOUT::selectRaw("qty * price as cogs")->where("branchcode", $sales->branchcode)->where('ref_no', $sales->trans_no)->get();
                $totalCogs = $Cogs->sum('cogs');

                DB::commit();
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        if (!$sales){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "ID or No_Trans Is Not Found"
                    ]
                ]
            ],500));
        }

        return response()->json([
            "data" =>[
                "sales" => $sales,
                "items" => $items,
                "cogs" => $totalCogs,
            ],
            "success" => "Successfully Updated Sales Transaction"
        ])->setStatusCode(200);
        
    }

    public function delete($branchcode, $key): JsonResponse
    {
        try {
            $sales = Sales::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", $key);
                $query->orWhere("id", $key);
            })->first();

            if($sales){
                DB::beginTransaction();
                Sales::where("branchcode", $branchcode)->where(function($query) use($key){
                    $query->where("trans_no", $key);
                    $query->orWhere("id", $key);
                })->delete();
                $stock = new StockController();
                $stock->revertstockout($sales->trans_no, $branchcode);

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
        if(!$sales){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "ID or No_Trans Is Not Found"
                    ]
                ]
            ],404));
        }
        return response()->json([
            "data" => $sales,
            "success" => "Successfully Deleted Sales Transaction"
        ])->setStatusCode(200);
    }
    public function approve($branchcode, $key){

        try {
            $sales = Sales::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", $key);
                $query->orWhere("id", $key);
            })->first();
            if($sales){
                $sales->is_approve =1;
                $sales->update();
                $Cogs = LOGINVOUT::selectRaw("qty * price as cogs")->where("branchcode", $sales->branchcode)->where('ref_no', $sales->trans_no)->get();
                $totalCogs = $Cogs->sum('cogs');
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
        if (!$sales){
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
                "sales" =>$sales,
                "cogs" => $totalCogs,
            ],
            "success" => "Successfully Approved Transaction"
            ])->setStatusCode(200);
    }
}
