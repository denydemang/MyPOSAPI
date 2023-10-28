<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table ="purchases";
    protected $primaryKey ="id";
    protected $keyType ="int";
    public $timestamps =true;
    public $incrementing =true;

    protected $fillable = [
        "branchcode",
        "trans_no",
        "trans_date",
        "id_user",
        "id_supplier",
        "dicount",
        "other_fee",
        "ppn",
        "payment_term",
        "is_approve",
        "total",
        "is_credit",
    ];
}