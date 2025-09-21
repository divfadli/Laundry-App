<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customers extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'customer_name',
        'phone',
        'address'
    ];

    public function transOrders(){
        return $this->hasMany(TransOrders::class,'id_customer');
    }

    public function transLaundryPickups(){
        return $this->hasMany(TransLaundryPickups::class,'id_customer');
    }
}
