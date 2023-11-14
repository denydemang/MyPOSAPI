<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPurchaseReturn extends Model
{
    protected $table ="detail_purchase_return";
    protected $primaryKey ="id";
    protected $keyType ="int";
    public $timestamps =true;
    public $incrementing =true;

    protected $fillable = [
        "id_purchase_return",
        "id_product",
        "id_unit",
        "qty",
        "cogs",
        "sub_total",
    ];
}
