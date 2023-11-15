<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseReturnCreateRequest;
use App\Http\Requests\PurchaseReturnUpdateRequest;
use App\Http\Resources\PurchaseReturnResourceCollection;
use App\Models\DetailPurchase;
use App\Models\DetailPurchaseReturn;
use App\Models\GRNSView;
use App\Models\LOGINVOUT;
use App\Models\Product;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnView;
use App\Models\Stock;
use App\Models\UnitView;
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
                'id_purchase' ,'purchase_trans_no','id_supplier', 'supplier_name' ,'reason', DB::raw('count(trans_no) as total_product'),
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
                'id_purchase' ,'purchase_trans_no','id_supplier', 'supplier_name','reason','total', 'is_approve'
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
                'id_purchase' ,'purchase_trans_no','id_supplier', 'supplier_name','reason','total', 'is_approve'
                )->where("branchcode", $branchcode)
                ->whereBetween("trans_date", [$startdate,$enddate])
                ->when($isapprove, function($query, string $isapprove){
                    $query->where('is_approve', filter_var($isapprove, FILTER_VALIDATE_BOOLEAN));
                })
                ->where(function($query) use ($key){
                    $query->Where('trans_no', 'like', "%{$key}%");
                    $query->orWhere('grn_trans_no', 'like', "%{$key}%");
                    $query->orWhere('purchase_trans_no', 'like', "%{$key}%");
                    $query->orWhere('supplier_name', 'like', "%{$key}%");
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

                $grnno =  GRNSView::where('id',$dataValidated['id_grn'])->first(['trans_no'])->trans_no;

                //Take out the amount stock
                foreach($dataValidated["items"] as $item){
                    $convert_value = intval(UnitView::where('id_unit', $item['id_unit'])->first(['convert_value'])->convert_value);
                    $qtyconverted = intval($item['qty']) * intval($convert_value);
                    $StockController = new StockController;
                    $StockController->purchasereturn($item['id_product'],$qtyconverted,$grnno,$newCode,$dataValidated['branchcode'], $dataValidated['trans_date']);
                }
                // 
                $pricesubtotal = LOGINVOUT::selectRaw("id_product,qty,price,qty * price as subtotal")->where('branchcode', $dataValidated['branchcode'])->where('ref_no',$newCode)->get();
                $datadetail = [];
                foreach($pricesubtotal as $i){
                    $id_unit = Product::where('id', $i['id_product'])->first(['id_unit'])->id_unit;
                    $datadetail[] = [
                        'id_product' => $i['id_product'],
                        'id_unit' =>$id_unit,
                        'qty' => $i['qty'],
                        'cogs' => $i['price'],
                        'sub_total' => $i['subtotal']
                    ];
                
                }
                $purchasereturn = new PurchaseReturn([
                    'branchcode' => $dataValidated['branchcode'],
                    'trans_no' => $newCode,
                    'id_grn' => $dataValidated['id_grn'],
                    'trans_date' => $dataValidated['trans_date'],
                    'reason' => $dataValidated['reason'],
                    'total' => round($pricesubtotal->sum('subtotal'), 0),
                ]);
                $purchasereturn->save();
                
                $datadetail= collect($datadetail)->transform(function($item) use($purchasereturn){
                    return ['id_purchase_return' => intval($purchasereturn->id)]+ $item;
                });

                DetailPurchaseReturn::insert($datadetail->toArray());  
                $datapurchasereturn = PurchaseReturnView::select(
                    'branchcode', 'id' , 'trans_no', 
                    'trans_date' ,'id_grn', 'grn_trans_no' , 
                    'id_purchase' ,'purchase_trans_no','id_supplier', 'supplier_name','reason','total', 'is_approve'
                    )
                    ->where("branchcode", $purchasereturn->branchcode)->where('trans_no',$purchasereturn->trans_no)->first();

                $dataitems = PurchaseReturnView::select(
                    'branchcode' , 'id',
                    'trans_no', 'id_detail_purchase_return',
                    'id_product', 'barcode',
                    'product_name', 'id_unit',
                    'qty', 'cogs', 'sub_total'
    
                )->where("branchcode", $purchasereturn->branchcode)->where('trans_no',$purchasereturn->trans_no)->get();
                $Cogs = LOGINVOUT::selectRaw("qty * price as cogs")->where('branchcode', $purchasereturn->branchcode)->where('ref_no',$purchasereturn->trans_no)->get();
                $totalCogs = round($Cogs->sum('cogs'), 0);
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
                    "purchase_return" => $datapurchasereturn,
                    "items" => $dataitems,
                    "cogs" => $totalCogs
                ],
                "success" => "Successfully Saved Purchase Return Transaction"
            ]
        )->setStatusCode(200);

    }
    public function update($branchcode,$key,PurchaseReturnUpdateRequest $request) :JsonResponse
    {
        $dataValidated = $request->validated();
        $dateNow = date("Y-m-d", strtotime(Carbon::now()));
        try {
            $purchasereturn = PurchaseReturn::where('branchcode', $branchcode)->where(function($query) use($key){
                $query->where("trans_no", $key);
                $query->orWhere("id", $key);
            })->lockForUpdate()->first();
            if ($purchasereturn){   
                DB::beginTransaction();
                $grnno =  GRNSView::where('id',$purchasereturn->id_grn)->first(['trans_no'])->trans_no;
                $StockController = new StockController;

                //Take out the amount stock
                $StockController->revertstockout($purchasereturn->trans_no,$purchasereturn->branchcode);
                // ADD NEW PURCHASE RETURN ITEM
                foreach($dataValidated["items"] as $item){
                    $convert_value = intval(UnitView::where('id_unit', $item['id_unit'])->first(['convert_value'])->convert_value);
                    $qtyconverted = intval($item['qty']) * intval($convert_value);
                    
                    $StockController->purchasereturn($item['id_product'],$qtyconverted,$grnno,$purchasereturn->trans_no,$purchasereturn->branchcode, $dataValidated['trans_date']);
                }
                
                $pricesubtotal = LOGINVOUT::selectRaw("id_product,qty,price,qty * price as subtotal")->where('branchcode', $purchasereturn->branchcode)->where('ref_no',$purchasereturn->trans_no)->get();
                $datadetail = [];
                foreach($pricesubtotal as $i){
                    $id_unit = Product::where('id', $i['id_product'])->first(['id_unit'])->id_unit;
                    $datadetail[] = [
                        'id_product' => $i['id_product'],
                        'id_unit' =>$id_unit,
                        'qty' => $i['qty'],
                        'cogs' => $i['price'],
                        'sub_total' => $i['subtotal']
                    ];
                
                }
                $purchasereturn->trans_date = $dataValidated['trans_date'];
                $purchasereturn->reason = $dataValidated['reason'];
                $purchasereturn->total = round($pricesubtotal->sum('subtotal'), 0);
                $purchasereturn->update();
                
                $datadetail= collect($datadetail)->transform(function($item) use($purchasereturn){
                    return ['id_purchase_return' => intval($purchasereturn->id)]+ $item;
                });
                DetailPurchaseReturn::where('id_purchase_return',$purchasereturn->id )->delete();
                DetailPurchaseReturn::insert($datadetail->toArray());  
                $datapurchasereturn = PurchaseReturnView::select(
                    'branchcode', 'id' , 'trans_no', 
                    'trans_date' ,'id_grn', 'grn_trans_no' , 
                    'id_purchase' ,'purchase_trans_no','id_supplier', 'supplier_name','reason','total', 'is_approve'
                    )
                    ->where("branchcode", $purchasereturn->branchcode)->where('trans_no',$purchasereturn->trans_no)->first();

                $dataitems = PurchaseReturnView::select(
                    'branchcode' , 'id',
                    'trans_no', 'id_detail_purchase_return',
                    'id_product', 'barcode',
                    'product_name', 'id_unit',
                    'qty', 'cogs', 'sub_total'
    
                )->where("branchcode", $purchasereturn->branchcode)->where('trans_no',$purchasereturn->trans_no)->get();
                $Cogs = LOGINVOUT::selectRaw("qty * price as cogs")->where('branchcode', $purchasereturn->branchcode)->where('ref_no',$purchasereturn->trans_no)->get();
                $totalCogs = round($Cogs->sum('cogs'), 0);
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

        if(!$purchasereturn){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "ID or No_Trans Is Not Found"
                    ]
                ]
            ],404));
        }
        return response()->json(
            [
                "data" => [
                    "purchase_return" => $datapurchasereturn,
                    "items" => $dataitems,
                    "cogs" => $totalCogs
                ],
                "success" => "Successfully Updated Purchase Return Transaction"
            ]
        )->setStatusCode(201);
    }

    public function delete($branchcode, $key): JsonResponse
    {
        try {
            $purchasereturn = PurchaseReturn::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", $key);
                $query->orWhere("id", $key);
            })->first();

            if($purchasereturn){  
                PurchaseReturn::where("branchcode", $branchcode)->where(function($query) use($key){
                    $query->where("trans_no", $key);
                    $query->orWhere("id", $key);
                })->delete();

                $stock =  new StockController();
                $stock->revertstockout($purchasereturn->trans_no, $purchasereturn->branchcode);
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
        if(!$purchasereturn){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "ID or No_Trans Is Not Found"
                    ]
                ]
            ],404));
        }
        return response()->json([
            "data" => $purchasereturn,
            "success" => "Successfully Deleted Purchase Transaction"
        ])->setStatusCode(200);
    }
    public function approve($branchcode, $key){

        try {
            $purchasereturn = PurchaseReturn::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", $key);
                $query->orWhere("id", $key);
            })->first();
            if($purchasereturn){
                $purchasereturn->is_approve = 1;
                $purchasereturn->update();
            }
            $Cogs = LOGINVOUT::selectRaw("qty * price as cogs")->where('branchcode', $purchasereturn->branchcode)->where('ref_no',$purchasereturn->trans_no)->get();
            $totalCogs = round($Cogs->sum('cogs'), 0);

        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        if (!$purchasereturn){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "ID or No_Trans Is Not Found"
                    ]
                ]
            ],404));
        }

        return response()->json([
            'data' => $purchasereturn,
            "success" => "successfully Approved Transaction"
            ])->setStatusCode(200);
    }
}
