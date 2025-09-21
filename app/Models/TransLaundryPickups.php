<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransLaundryPickups extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_order',
        'id_customer',
        'pickup_date',
        'notes'
    ];

    protected $casts = [
        'pickup_date' => 'date'
    ];

    public function transOrder()
    {
        return $this->belongsTo(TransOrders::class, 'id_order');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'id_customer');
    }
}