<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransOrders extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_customer',
        'order_code',
        'order_date',
        'order_end_date',
        'order_status',
        'total',
        'order_pay',
        'order_change'
    ];

    protected $casts = [
        'order_date' => 'date',
        'order_end_date' => 'date',
        'total' => 'decimal:2',
        'order_pay' => 'decimal:2',
        'order_change' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'id_customer');
    }

    public function transOrderDetails()
    {
        return $this->hasMany(TransOrderDetails::class, 'id_order');
    }

    public function transLaundryPickups()
    {
        return $this->hasOne(TransLaundryPickups::class, 'id_order');
    }

    public function getStatusTextAttribute()
    {
        return $this->order_status == 0 ? 'Baru' : 'Sudah diambil';
    }

    public function getStatusClassAttribute()
    {
        return $this->order_status == 0 ? 'bg-warning' : 'bg-success';
    }
}