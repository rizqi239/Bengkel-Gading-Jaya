<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $fillable = [
        'supplier_name',
        'supplier_address',
        'barang',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'status',
        'tanggal_pesan',
        'tanggal_terima',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_pesan' => 'date',
        'tanggal_terima' => 'date',
    ];
}
