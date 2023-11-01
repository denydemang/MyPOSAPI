<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table ="stocks";
    protected $primaryKey ="id";
    protected $keyType ="int";
    public $timestamps =true;
    public $incrementing =true;

    protected $fillable = [
        "branchcode",
        "id",
        "ref",
        "date",
        "id_product",
        "actual_stock",
        "used_stock",
        "cogs",
        "id_unit",
    ];
}
