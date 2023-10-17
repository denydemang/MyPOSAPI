<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table ="products";
    protected $primaryKey ="id";
    protected $keyType ="int";
    public $timestamps =true;
    public $incrementing =true;
    protected $fillable = [
        "branchcode",
        "barcode",
        "name",
        "brands",
        "id_category",
        "price",
        "status",
        "maxstock",
        "minstock",
    ];

}
