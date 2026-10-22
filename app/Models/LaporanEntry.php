<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanEntry extends Model
{
    use HasFactory;

    protected $table = 'laporan_entries';

    protected $fillable = [
        'tipe',
        'kategori',
        'keterangan',
        'nominal',
        'tanggal',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal' => 'date',
    ];
}
