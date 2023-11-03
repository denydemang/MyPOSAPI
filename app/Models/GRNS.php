<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GRNS extends Model
{
    protected $table ="grns";
    protected $primaryKey ="id";
    protected $keyType ="int";
    public $timestamps =true;
    public $incrementing =true;

    protected $fillable = [
        "branchcode",
        "trans_no",
        "received_date",
        "id_purchase",
        "received_by",
        "description",
        "grand_total",
        "is_approve",
    ];
}
