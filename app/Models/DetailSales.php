<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSales extends Model
{
    protected $table ="detail_sales";
    protected $primaryKey ="id";
    protected $keyType ="int";
    public $timestamps =true;
    public $incrementing =true;

    protected $fillable = [
        "id_sales",
        "trans_date",
        "id_product",
        "id_unit",
        "qty",
        "price",
        "total",
        "discount",
        "sub_total",
    ];
}
