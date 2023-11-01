<?php

namespace App\Http\Controllers;

use App\Models\ProductView;
use App\Models\Stock;
use Exception;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function stockout(int $idproduct, int $qty):int{

        $Totalcogs = 0; //hpp atau cost of good sold
        $remaining_stock = ProductView::where('id', $idproduct)->first();

        if (!$remaining_stock){
            throw new Exception('Product Is Not Found');
        }
        
        if(intval($remaining_stock->remaining_stock) < intval($qty)){
            throw new Exception('Insufficient Stock Of Supplies');
        }
        $stocks = Stock::where("id_product",$idproduct)
        ->whereColumn("actual_stock", ">" ,"used_stock")->lockForUpdate()->orderBy('date')->orderBy('id')->get();

        // return response()->json($stocks);
        foreach ($stocks as $stock){

            if ($qty != 0) {

                $stock_available= intval($stock->actual_stock - $stock->used_stock);
    
                    if($qty >= $stock_available){
    
                        $cogs = intval((intval($stock_available) * intval($stock->cogs)));
                        $Totalcogs+= $cogs;
                        $stock->used_stock += $stock_available;
                        $stock->update();
                        $qty-=$stock_available;
                    } else {
                        $cogs = intval((intval($qty) * intval($stock->cogs)));
                        $Totalcogs+= $cogs;
                        $stock->used_stock += $qty;
                        $stock->update();
                        $qty-=$qty;
                    }
            }
        //     }
        //     return $Totalcogs;
        }
        return $Totalcogs;
    }

    public function stockin(int $idproduct, int $qty):int{

        $Totalcogs = 0; //hpp atau cost of good sold
        $remaining_stock = ProductView::where('id', $idproduct)->first();

        if (!$remaining_stock){
            throw new Exception('Product Is Not Found');
        }
        
        if(intval($remaining_stock->remaining_stock) < intval($qty)){
            throw new Exception('Insufficient Stock Of Supplies');
        }
        $stocks = Stock::where("id_product",$idproduct)
        ->whereColumn("actual_stock", ">" ,"used_stock")->lockForUpdate()->orderBy('date')->orderBy('id')->get();

        // return response()->json($stocks);
        foreach ($stocks as $stock){

            if ($qty != 0) {

                $stock_available= intval($stock->actual_stock - $stock->used_stock);
    
                    if($qty >= $stock_available){
    
                        $cogs = intval((intval($stock_available) * intval($stock->cogs)));
                        $Totalcogs+= $cogs;
                        $stock->used_stock += $stock_available;
                        $stock->update();
                        $qty-=$stock_available;
                    } else {
                        $cogs = intval((intval($qty) * intval($stock->cogs)));
                        $Totalcogs+= $cogs;
                        $stock->used_stock += $qty;
                        $stock->update();
                        $qty-=$qty;
                    }
            }
        //     }
        //     return $Totalcogs;
        }
        return $Totalcogs;
    }
}
