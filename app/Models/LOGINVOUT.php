<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LOGINVOUT extends Model
{
    protected $table ="log_inv_out";
    protected $primaryKey ="id";
    protected $keyType ="int";
    public $timestamps =true;
    public $incrementing =true;

    protected $fillable = [
        "branchcode",
        "ref_no",
        "date",
        "id_product",
        "qty",
        "price",
        "id_stock",
    ];
}
