<?php

namespace App\Http\Controllers;

use App\Http\Requests\InitialStockCreateRequest;
use App\Http\Requests\InitialStockUpdateRequest;
use App\Models\LOGINVOUT;
use App\Models\ProductView;
use App\Models\Stock;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function stockout(int $idproduct, int $qty, string $transno, string $branchcode, string $date):void{

        // $Totalcogs = 0; //hpp atau cost of good sold
        $remaining_stock = ProductView::where('branchcode', $branchcode)->where('id', $idproduct)->first();

        if (!$remaining_stock){
            throw new Exception('Product Is Not Found');
        }
        
        if(intval($remaining_stock->remaining_stock) < intval($qty)){
            throw new Exception('Insufficient Stock Of Supplies');
        }
        $stocks = Stock::where('branchcode', $branchcode)->where("id_product",$idproduct)
        ->whereColumn("actual_stock", ">" ,"used_stock")->lockForUpdate()->orderBy('date')->orderBy('id')->get();

        // return response()->json($stocks);
        foreach ($stocks as $stock){

            if ($qty != 0) {

                $stock_available= intval($stock->actual_stock - $stock->used_stock);
    
                    if($qty >= $stock_available){
    
                        $cogs = intval((intval($stock_available) * intval($stock->cogs)));
                        // $Totalcogs+= $cogs;
                        $stock->used_stock += $stock_available;
                        $stock->update();
                        $loginvout = new LOGINVOUT();
                        $loginvout->branchcode = $branchcode;
                        $loginvout->id_product = $idproduct;
                        $loginvout->ref_no = $transno;
                        $loginvout->date = $date;
                        $loginvout->qty = $stock_available;
                        $loginvout->price = $stock->cogs;
                        $loginvout->id_stock = $stock->id;
                        $loginvout->save();
                        $qty-=$stock_available;
                    } else {
                        $cogs = intval((intval($qty) * intval($stock->cogs)));
                        // $Totalcogs+= $cogs;
                        $stock->used_stock += $qty;
                        $stock->update();
                        $loginvout = new LOGINVOUT();
                        $loginvout->branchcode = $branchcode;
                        $loginvout->id_product = $idproduct;
                        $loginvout->ref_no = $transno;
                        $loginvout->date = $date;
                        $loginvout->qty = $qty;
                        $loginvout->price = $stock->cogs;
                        $loginvout->id_stock = $stock->id;
                        $loginvout->save();
                        $qty-=$qty;
                    }
            }
        }
    }
    public function purchasereturn(int $idproduct, int $qty, string $grnno, string  $transno, string $branchcode, string $date)
    {
        $stock = Stock::where('branchcode',$branchcode)->where('id_product', $idproduct)->where('ref', $grnno)->lockForUpdate()->first();
        if (!$stock){
            throw new Exception('ID Product Not Found');
        }
        if (intval($qty) > (intval($stock->actual_stock) - intval($stock->used_stock))){
            throw new Exception('Exceeds the Amount of Stock Purchased');
        }
        $stock->used_stock += $qty;
        $stock->update();
        $stockout = new LOGINVOUT([
            'branchcode' => $branchcode,
            'ref_no' => $transno,
            'date' => $date,
            'id_product'=> $idproduct,
            'qty' => $qty,
            'price' => $stock->cogs,
            'id_stock' => $stock->id
        ]);
        $stockout->save();
    }
    public function updatepurchasereturn(int $idproduct, int $qty, string $grnno, string  $transno, string $branchcode, string $date)
    {
        $stockout = LOGINVOUT::where('branchcode', $branchcode)->where('ref_no', $transno)->get();
        foreach ($stockout as $item) {
            $stock = Stock::where('id_product',$stockout->id_product)->where('ref', $grnno)->lockForUpdate()->first();
            $stock->used_stock -= $item->qty;
            $stock->update();
            $item->delete();
        }
        $this->purchasereturn($idproduct,$qty, $grnno,$transno,$branchcode, $date);
    }
    // public function updatestockout(int $idproduct, int $qty, string $transno, string $branchcode, string $date):void{
    //     $logstockout = LOGINVOUT::where("branchcode", $branchcode)->where("ref_no", $transno)->where("id_product", $idproduct)->orderBy('date')->orderBy('id')->get();

    //     //decrease old qty
    //     foreach($logstockout as $log){
    //         $stock = Stock::where("id" , $log->id_stock)->lockForUpdate()->first();
    //         $stock->used_stock -= $log->qty;
    //         $stock->update();
    //     }
    //     LOGINVOUT::where("branchcode", $branchcode)->where("ref_no", $transno)->where("id_product", $idproduct)->delete();

    //     //add back new qty
    //     $this->stockout($idproduct,$qty, $transno,$branchcode, $date);
        
    // }
    public function revertstockout(string $transno, string $branchcode):void{
        $logstockout = LOGINVOUT::where("branchcode", $branchcode)->where("ref_no", $transno)->get();
        foreach($logstockout as $log){
            $stock = Stock::where("id" , $log->id_stock)->lockForUpdate()->first();
            $stock->used_stock -= $log->qty;
            $stock->update();
        }
        LOGINVOUT::where("branchcode", $branchcode)->where("ref_no", $transno)->delete();
    }

    public function stockin(int $idproduct, int $qty, string $trans_no ,string $date, string $branchcode ,string $idunit,float $price, $isapprove= 0):void{

        $stock = new Stock();
        $stock->branchcode = $branchcode;
        $stock->ref = $trans_no;
        $stock->date = $date;
        $stock->id_product = $idproduct;
        $stock->actual_stock = $qty;
        $stock->used_stock = 0;
        $stock->cogs = $price;
        $stock->id_unit =$idunit;
        $stock->is_approve = $isapprove;
        $stock->save();

    }
    public function updatestockin(array $dataitem,string $trans_no ,string $date, string $branchcode){

        $stock = Stock::where("branchcode", $branchcode)->where("ref", $trans_no)->get();
        
        $counter =0;
        foreach( $stock as $stck){
            if ($stck->is_approve ==1){
            throw new Exception('Transaction Is Already Approved Cannot Be Updated/Deleted');
        }
        else{


            $stck->date = $date;
            $stck->id_product = $dataitem[$counter]['id_product'];
            $stck->actual_stock = $dataitem[$counter]['qty'];
            $stck->used_stock = 0;
            $stck->cogs =$dataitem[$counter]['price'] ;
            $stck->id_unit =$dataitem[$counter]['id_unit'] ;
            $stck->update();
            }
            $counter++;
        }
        
    }
    public function createinitialstock(InitialStockCreateRequest $request) :JsonResponse {
        $dataValidated = $request->validated();

        try {
            $getinitialstock = Stock::where('branchcode', $dataValidated['branchcode'])->where('ref','Initial Stock')->where('id_product',$dataValidated['id_product'])->first();
            if(!$getinitialstock){
                $this->stockin($dataValidated['id_product'], $dataValidated['initialstock'], 'Initial Stock', $dataValidated['date'], $dataValidated['branchcode'],$dataValidated['id_unit'], $dataValidated['buyprice'], 1);
            };
        
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }
        if ($getinitialstock) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "Product Already Has Initial Stock, Choose Update Instead"
                    ]
                ]
                ],400));
        }
        return response()->json([
            'success' => 'Successfully Created Initial Stock'
        ])->setStatusCode(200);
    }
    public function updateinitialstock($branchcode,$idproduct,InitialStockUpdateRequest $request) :JsonResponse {


        $dataValidated = $request->validated();
        try {
            //code...
            $getinitialstock = Stock::where('branchcode', $branchcode)->where('ref','Initial Stock')->where('id_product',$idproduct)->first();
            if($getinitialstock){
                $getinitialstock->actual_stock = $dataValidated['initialstock'];
                $getinitialstock->id_unit = $dataValidated['id_unit'];
                $getinitialstock->cogs = $dataValidated['buyprice'];
                $getinitialstock->update();
            }
        } catch (\Throwable $th) {
            //throw $th;
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        if(!$getinitialstock){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "Product Not Found"
                    ]
                ]
                ],404));
        }
        return response()->json([
            "success" => "Successfully Updated Initial Stock"
        ])->setStatusCode(200);

    }
    public function getinitialstock($branchcode,$idproduct) :JsonResponse {
        try {
            //code...
            $getinitialstock = Stock::where('branchcode', $branchcode)->where('ref','Initial Stock')->where('id_product',$idproduct)->first();
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
            "data" => $getinitialstock,
            "success" => "Successfully Get Initial Stock"
        ])->setStatusCode(200);
    }
    

}
