<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailGRNS extends Model
{
    protected $table ="detail_grns";
    protected $primaryKey ="id";
    protected $keyType ="int";
    public $timestamps =true;
    public $incrementing =true;

    protected $fillable = [
        "id_grns",
        "id_product",
        "id_unit",
        "qty",
        "bonusqty",
        "price",
        "sub_total",
    ];
}
