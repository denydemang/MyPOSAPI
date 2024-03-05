<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $table ="sales";
    protected $primaryKey ="id";
    protected $keyType ="int";
    public $timestamps =true;
    public $incrementing =true;

    protected $fillable = [
        "branchcode",
        "trans_no",
        "trans_date",
        "id_cust",
        "id_user",
        "total",
        "other_fee",
        "ppn",
        "percent_ppn",
        "notes",
        "grand_total",
        "paid",
        "change_amount",
        "is_approve",
        "is_credit",
    ];
}
