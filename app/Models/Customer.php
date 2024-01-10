<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table ='customers';
    protected $primaryKey ="id";
    protected $keyType ="int";
    public $timestamps =true;
    public $incrementing =true;

    protected $fillable =[
        "branchcode",
        "cust_no",
        "name",
        "address",
        "phone",
        "active"
    ];
}
