<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasirTransaction extends Model
{
    use HasFactory;

    protected $table = 'kasir_transactions';

    protected $fillable = [
        'customer_name',
        'transaction_type',
        'plate_number',
        'vehicle_model',
        'description',
        'items',
        'subtotal',
        'discount',
        'tax',
        'total',
        'payment_method',
        'status',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];
}
