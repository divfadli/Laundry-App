<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class TransOrderDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_order',
        'id_service',
        'qty',
        'subtotal',
        'notes'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function transOrder()
    {
        return $this->belongsTo(TransOrders::class, 'id_order');
    }

    public function typeOfService()
    {
        return $this->belongsTo(TypeOfServices::class, 'id_service');
    }
}