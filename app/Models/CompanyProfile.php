<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $table ="company_profiles";
    protected $primaryKey ="branchcode";
    protected $keyType ="string";
    public $timestamps =true;
    public $incrementing =true;

    protected $fillable = [
        'branchcode' ,
        'profile_name',
        'app_name',
        'address',
        'phone' ,
        'email',
        'npwp' ,
        'moto',
    ];
}
