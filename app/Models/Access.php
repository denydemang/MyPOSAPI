<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    protected $table ="access";
    protected $primaryKey ="id";
    protected $keyType ="int";
    public $timestamps =true;
    public $incrementing =true;

    protected $fillable = [
        "branchcode",
        "id_role",
        "id_module",
        "xView",
        "xUpdate",
        "xDelete",
        "xApprove",
        "xCreate",
    ];
}
