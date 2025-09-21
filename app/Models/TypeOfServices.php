<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeOfServices extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_name',
        'price',
        'description'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function transOrderDetails()
    {
        return $this->hasMany(TransOrderDetails::class, 'id_service');
    }
}