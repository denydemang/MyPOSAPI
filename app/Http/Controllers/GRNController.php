<?php

namespace App\Http\Controllers;

use App\Http\Requests\GRNCreateRequest;
use App\Http\Requests\GRNUpdateRequest;
use App\Http\Resources\GRNSResourceCollection;
use App\Models\DetailGRNS;
use App\Models\GRNS;
use App\Models\GRNSView;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\UnitView;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GRNController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }
    public function getall($branchcode, Request $request) : GRNSResourceCollection{

        $tableName = (new GRNSView())->getTable();
        $columlist = Schema::getColumnListing($tableName);

        $firstdate =  date("Y-m-d",strtotime(Carbon::now()->firstOfMonth()));
        $lastdate =  date("Y-m-d",strtotime(Carbon::now()->lastOfMonth()));
        $isapprove = $request->get('isapprove'); 
        $orderBy = (null != $request->get('orderby') && in_array(strtolower($request->get('orderby')),$columlist)) ? $request->get('orderby') : "received_date";
        $ascdesc = (null != $request->get('ascdesc') && (strtolower($request->get('ascdesc'))== "asc"|| strtolower($request->get('ascdesc'))== "desc"))? $request->get('ascdesc'): "asc";
        $perpage = (null != $request->get('perpage'))? $request->get('perpage') : 10;
        $page = (null != $request->get('page'))? $request->get('page') : 1;
        $startdate = (null != $request->get("startdate"))? date("Y-m-d", strtotime($request->get("startdate"))): $firstdate;
        $enddate = (null != $request->get("enddate")) ? date("Y-m-d", strtotime($request->get("enddate"))): $lastdate;
        try {
            $grns =GRNSView::select(    
                'branchcode', 'id' , 'trans_no', 
                'received_date' ,'id_purchase','purchase_trans_no','id_supplier','supplier_name','received_by',
                'description', 'total_purchase','other_fee','ppn','grand_total','is_approve', DB::raw('count(trans_no) as total_product')
                )->where("branchcode", $branchcode)
                ->whereBetween("received_date", [$startdate,$enddate])
                ->when($isapprove, function($query, string $isapprove){
                    $query->where('is_approve', filter_var($isapprove, FILTER_VALIDATE_BOOLEAN));
                })
                ->groupBy('trans_no')
                ->orderBy($orderBy, $ascdesc)->orderBy('id', $ascdesc)
                ->paginate(perPage:$perpage, page:$page);

                // $grns->withPath(URL::to('/').'/api/purchases/list/int?startdate='.$startdate.'&enddate='.$enddate.'&ascdesc='.$ascdesc.'&orderby='.$orderBy.'&perpage='.$perpage);
                $grns->withPath($request->fullUrl());
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

        return new GRNSResourceCollection($grns, "Successfully Get All GRN Transaction");
    
    }
    public function get($branchcode, $id) :JsonResponse{

        try {
            $grns = GRNSView::select(
                'branchcode', 'id' , 'trans_no', 
                'received_date' ,'id_purchase','purchase_trans_no','id_supplier','supplier_name','received_by',
                'description', 'total_purchase','other_fee','ppn','grand_total','is_approve'
                )
                ->where("branchcode", $branchcode)->where(function($query) use($id){
                    $query->where("trans_no", $id);
                    $query->orWhere("id", $id);
                })->first();
            $item = GRNSView::select(
                'branchcode' , 'id',
                'trans_no', 'id_detail_grns',
                'id_product', 'barcode',
                'name', 'brands', 'id_unit',
                'qty', 'bonusqty','unitbonusqty','price','total','discount', 'sub_total'

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
                "grns" => $grns,
                "item" =>$item
            ],
            'total_product' =>$item->count('trans_no'),
            'success' =>'Successfully Get Specific GRN Transaction'
        ])->setStatusCode(200);
    }
    public function search($branchcode,Request $request) : GRNSResourceCollection {
    
        $key = $request->get('key');
        $tableName = (new GRNSView())->getTable();
        $columlist = Schema::getColumnListing($tableName);
        $isapprove = $request->get('isapprove'); 
        $firstdate =  date("Y-m-d",strtotime(Carbon::now()->firstOfMonth()));
        $lastdate =  date("Y-m-d",strtotime(Carbon::now()->lastOfMonth()));
        $orderBy = (null != $request->get('orderby') && in_array(strtolower($request->get('orderby')),$columlist)) ? $request->get('orderby') : "received_date";
        $ascdesc = (null != $request->get('ascdesc') && (strtolower($request->get('ascdesc'))== "asc"|| strtolower($request->get('ascdesc'))== "desc"))? $request->get('ascdesc'): "asc";
        $perpage = (null != $request->get('perpage'))? $request->get('perpage') : 10;
        $page = (null != $request->get('page'))? $request->get('page') : 1;
        $startdate = (null != $request->get("startdate"))? date("Y-m-d", strtotime($request->get("startdate"))): $firstdate;
        $enddate = (null != $request->get("enddate")) ? date("Y-m-d", strtotime($request->get("enddate"))): $lastdate;

        try {
            $grns =GRNSView::select(
                'branchcode', 'id' , 'trans_no', 
                'received_date' ,'id_purchase','purchase_trans_no','id_supplier','supplier_name','received_by',
                'description', 'total_purchase','other_fee','ppn','grand_total','is_approve', DB::raw('count(trans_no) as total_product')
                )->where("branchcode", $branchcode)
                ->whereBetween("received_date", [$startdate,$enddate])
                ->when($isapprove, function($query, string $isapprove){
                    $query->where('is_approve', filter_var($isapprove, FILTER_VALIDATE_BOOLEAN));
                })
                ->where(function($query) use ($key){
                    $query->Where('trans_no', 'like', "%{$key}%");
                    $query->orWhere('purchase_trans_no', 'like', "%{$key}%");
                    $query->orWhere('supplier_name', 'like', "%{$key}%");
                    $query->orWhere('received_by', 'like', "%{$key}%");
                })
                ->groupBy('trans_no')->orderBy($orderBy, $ascdesc)->orderBy('id', $ascdesc)->paginate(perPage:$perpage, page:$page);
                $grns->withPath($request->fullUrl());
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        return new GRNSResourceCollection($grns, "Successfully Get GRNS Transaction By Search");
    }
    public function create(GRNCreateRequest $request) :JsonResponse
    {
        $dataValidated = $request->validated();
        $dateNow = date("Y-m-d", strtotime(Carbon::now()));
        try {
            DB::beginTransaction();

                $getTransNo = GRNS::select('trans_no')->where("branchcode", $dataValidated["branchcode"])->where("trans_no","like", "GRN-{$dateNow}-%")->orderBy("trans_no", "desc")->first();
                if ($getTransNo){

                    $getLockMax = GRNS::select('trans_no')->where("branchcode", $dataValidated["branchcode"])->where("trans_no", ">=", $getTransNo->trans_no)->lockForUpdate()->orderBy("trans_no", "desc")->first();
                    $getLastDigit = intval(str_replace("GRN-{$dateNow}-","",$getLockMax->trans_no));
                    $newCode = "GRN-{$dateNow}-". sprintf("%03d",$getLastDigit + 1);
                } else {
                    $newCode = "GRN-{$dateNow}-001";
                }
                $purchase = Purchase::where("branchcode", $dataValidated['branchcode'])->where("id",$dataValidated['id_purchase'])->first();
                $purchase->is_received = 1;
                $purchase->update();
                $grns = new GRNS();
                $grns->branchcode = $dataValidated['branchcode'];
                $grns->trans_no = $newCode;
                $grns->received_date = $dataValidated['received_date'];
                $grns->id_purchase = $dataValidated['id_purchase'];
                $grns->received_by = $dataValidated['received_by'];
                $grns->grand_total = $purchase->grand_total;
                $grns->description = $dataValidated['description'];
                $grns->save();

                $items= collect($dataValidated["items"])->transform(function($item) use($grns){
                    return ['id_grns' => intval($grns->id)]+ $item;
                });
            
                DetailGRNS::insert($items->toArray());  

                $totalQtyTransaction =0;
                $totalQtyBonusTransaction =0;
                $other_fee = intval(Purchase::where('id', $grns->id_purchase)->first(['other_fee'])->other_fee);
                
                //menghitung total qty dan bonus
                foreach($dataValidated['items'] as $item){
                    $bonusqty = !empty($item['bonusqty']) ? intval($item['bonusqty']) : 0;
                    $unitbonus = $item['unitbonusqty'];
                    $convert_value = intval(UnitView::where('id', $item['id_unit'])->first(['convert_value'])->convert_value);
                    $convert_value_bonus = intval(UnitView::where('id', $unitbonus)->first(['convert_value'])->convert_value);
                    $totalQtyTransaction += (intval($item['qty']) * $convert_value);
                    $totalQtyBonusTransaction += (intval($bonusqty) * $convert_value_bonus);
                }
                $other_fee_per_item =round($other_fee /($totalQtyTransaction+$totalQtyBonusTransaction),6);
                foreach ($dataValidated['items'] as $item) {
                    $bonusqty = !empty($item['bonusqty']) ? intval($item['bonusqty']) : 0;
                    $id_unit = Product::where('id', $item['id_product'])->first(['id_unit'])->id_unit;
                    $unitbonus = $item['unitbonusqty'];
                    $qty = intval($item['qty']);
                    $total = intval($item['sub_total']);
                    $convert_value = intval(UnitView::where('id', $item['id_unit'])->first(['convert_value'])->convert_value);
                    $convert_value_bonus = intval(UnitView::where('id', $unitbonus)->first(['convert_value'])->convert_value);
                    $qtyandbonus = ($qty * $convert_value) + ($bonusqty * $convert_value_bonus);
                    $hpp = round((($total / $qtyandbonus ) + $other_fee_per_item), 6);
                    $checkstock = new StockController();
                    $checkstock->stockin($item['id_product'],$qtyandbonus, $grns->trans_no, $grns->received_date,$grns->branchcode,$id_unit,$hpp);   
                };

                
                $datagrns = GRNSView::select(
                    'branchcode', 'id' , 'trans_no', 
                    'received_date' ,'id_purchase','purchase_trans_no','id_supplier','supplier_name','received_by',
                    'description', 'total_purchase','other_fee','ppn','grand_total','is_approve'
                    )->where('branchcode',$grns->branchcode)->where('trans_no', $grns->trans_no)->first();

                $dataitem = GRNSView::select(
                    'branchcode' , 'id',
                    'trans_no', 'id_detail_grns',
                    'id_product', 'barcode',
                    'name', 'brands', 'id_unit',
                    'qty', 'bonusqty','unitbonusqty','price','total','discount', 'sub_total'
    
                )->where('branchcode',$grns->branchcode)->where('trans_no', $grns->trans_no)->get();
                $Cogs = Stock::selectRaw("actual_stock * cogs as cogs")->where("branchcode", $grns->branchcode)->where('ref', $grns->trans_no)->get();
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
                    "grns" => $datagrns,
                    "items" => $dataitem,
                ],
                "cogs" => $totalCogs,
                "success" => "Successfully Saved GRNS Transaction"
            ]
        )->setStatusCode(201);
        // return response()->json([
        //     "totalQtyTransaction" => $totalQtyTransaction,
        //     "totalQtyBonusTransaction" => $totalQtyBonusTransaction,
        //     "other_fee" => $other_fee,
        //     "other_fee_per_item" => $other_fee_per_item,
        //     "hpp" => $hpp,
        // ]);
    }
    public function update($branchcode,$key,GRNUpdateRequest $request) :JsonResponse
    {
        $dataValidated = $request->validated();

        try {
            $grns = GRNS::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", strval($key));
                $query->orWhere("id", $key);
            })->first();

            if($grns){
                DB::beginTransaction();
          
                $grns = GRNS::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", $key);
                $query->orWhere("id", $key);})->lockForUpdate()->first();
                $grns->received_date = $dataValidated['received_date'];
                $grns->received_by = !empty($dataValidated['received_by']) ? $dataValidated['received_by'] :null;
                $grns->grand_total =$dataValidated['grand_total'] ;
                $grns->description =!empty($dataValidated['description']) ? $dataValidated['description'] : null ;
                $grns->update();

                DetailGRNS::where("id_grns" ,$grns->id)->delete();
                $items= collect($dataValidated["items"])->transform(function($item) use($grns){
                    return ['id_grns' => intval($grns->id)]+ $item;
                });
                DetailGRNS::insert($items->toArray());


                //Kalkulasi HPP Baru
                $totalQtyTransaction =0;
                $totalQtyBonusTransaction =0;
                $other_fee = intval(Purchase::where('id', $grns->id_purchase)->first(['other_fee'])->other_fee);
                foreach($dataValidated['items'] as $item){
                    $bonusqty = !empty($item['bonusqty']) ? intval($item['bonusqty']) : 0;
                    $unitbonus = $item['unitbonusqty'];
                    $convert_value = intval(UnitView::where('id', $item['id_unit'])->first(['convert_value'])->convert_value);
                    $convert_value_bonus = intval(UnitView::where('id', $unitbonus)->first(['convert_value'])->convert_value);
                    $totalQtyTransaction += (intval($item['qty']) * $convert_value);
                    $totalQtyBonusTransaction += (intval($bonusqty) * $convert_value_bonus);
                }
                $other_fee_per_item =round($other_fee /($totalQtyTransaction+$totalQtyBonusTransaction),6);
                $totalitemandphp =[];
                foreach ($dataValidated['items'] as $item) {
                    $bonusqty = !empty($item['bonusqty']) ? intval($item['bonusqty']) : 0;
                    $id_unit = Product::where('id', $item['id_product'])->first(['id_unit'])->id_unit;
                    $qty = intval($item['qty']);
                    $total = intval($item['sub_total']);
                    $unitbonus = $item['unitbonusqty'];
                    $convert_value = intval(UnitView::where('id', $item['id_unit'])->first(['convert_value'])->convert_value);
                    $convert_value_bonus = intval(UnitView::where('id', $unitbonus)->first(['convert_value'])->convert_value);

                    $qtyandbonus = ($qty * $convert_value) + ($bonusqty * $convert_value_bonus);
                    $hpp = round((($total / $qtyandbonus ) + $other_fee_per_item), 6);
                    $totalitemandphp[]=[
                        'id_product' => $item['id_product'],
                        'qty' => $qtyandbonus,
                        'price' => $hpp,
                        'id_unit'=> $id_unit,
                    ];
                };

                $datagrns = GRNSView::select(
                    'branchcode', 'id' , 'trans_no', 
                    'received_date' ,'id_purchase','purchase_trans_no','id_supplier','supplier_name','received_by',
                    'description', 'total_purchase','other_fee','ppn','grand_total','is_approve'
                    )->where('branchcode',$grns->branchcode)->where('trans_no', $grns->trans_no)->first();

                $dataitem = GRNSView::select(
                    'branchcode' , 'id',
                    'trans_no', 'id_detail_grns',
                    'id_product', 'barcode',
                    'name', 'brands', 'id_unit',
                    'qty', 'bonusqty','unitbonusqty','price','total','discount', 'sub_total'
    
                )->where('branchcode',$grns->branchcode)->where('trans_no', $grns->trans_no)->get();
                

                $checkstock = new StockController();
                $checkstock->updatestockin($totalitemandphp,$grns->trans_no,$grns->received_date,$grns->branchcode);   
                $Cogs = Stock::selectRaw("actual_stock * cogs as cogs")->where("branchcode", $grns->branchcode)->where('ref', $grns->trans_no)->get();
                $totalCogs = round($Cogs->sum('cogs'),0);
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
        if (!$grns){
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
                "grns" => $datagrns,
                "items" => $dataitem,
            ],
            "cogs" => $totalCogs,
            "success" => "Successfully Updated GRNS Transaction"
        ])->setStatusCode(200);
        
        // return response()->json($totalitemandphp)->setStatusCode(200);
        
    }
    public function delete($branchcode, $key): JsonResponse
    {
        try {
            $grns = GRNS::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", $key);
                $query->orWhere("id", $key);
            })->first();

            if($grns){
                DB::beginTransaction();
                GRNS::where("branchcode", $branchcode)->where(function($query) use($key){
                    $query->where("trans_no", $key);
                    $query->orWhere("id", $key);
                })->delete();
                Stock::where('branchcode', $branchcode)->where('ref',$grns->trans_no)->delete();
                Purchase::where('branchcode', $branchcode)->where('id',$grns->id_purchase)->update([
                    "is_received" => 0
                ]);
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
        if(!$grns){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "ID or No_Trans Is Not Found"
                    ]
                ]
            ],404));
        }
        return response()->json([
            "data" => $grns,
            "success" => "Successfully Deleted GRN Transaction"
        ])->setStatusCode(200);
    }
    public function approve($branchcode, $key){

        try {
            $grns = GRNS::where("branchcode", $branchcode)->where(function($query) use($key){
                $query->where("trans_no", $key);
                $query->orWhere("id", $key);
            })->first();
            if($grns){
                $grns->is_approve =1;
                $grns->update();

                Stock::where("branchcode", $branchcode)->where("ref", $grns->trans_no)->update([
                    "is_approve"  => 1
                ]);
                $Cogs = Stock::selectRaw("actual_stock * cogs as cogs")->where("branchcode", $grns->branchcode)->where('ref', $grns->trans_no)->get();
                $totalCogs = round($Cogs->sum('cogs'), 0);
                $datagrns = GRNSView::select(
                    'branchcode', 'id' , 'trans_no', 
                    'received_date' ,'id_purchase','purchase_trans_no','id_supplier','supplier_name','received_by',
                    'description', 'total_purchase','other_fee','ppn','grand_total','is_approve'
                    )->where('branchcode',$grns->branchcode)->where('trans_no', $grns->trans_no)->first();

                $dataitem = GRNSView::select(
                    'branchcode' , 'id',
                    'trans_no', 'id_detail_grns',
                    'id_product', 'barcode',
                    'name', 'brands', 'id_unit',
                    'qty', 'bonusqty','unitbonusqty','price','total','discount', 'sub_total'
    
                )->where('branchcode',$grns->branchcode)->where('trans_no', $grns->trans_no)->get();
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
        if (!$grns){
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
                "grns" => $datagrns,
                "items" => $dataitem,
            ],
            "cogs" => $totalCogs,
            "success" => "Successfully Approved GRN Transaction"
            ])->setStatusCode(200);
    }
}
