<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = [
        'code',
        'name',
        'category',
        'quantity',
        'price',
        'photo',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get the public URL for the photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        if (str_starts_with($this->photo, 'data:image/')) {
            return route('stock.photo', $this);
        }

        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return $this->photo;
        }

        $disk = config('filesystems.default');

        try {
            return Storage::disk($disk)->url($this->photo);
        } catch (\Throwable $e) {
            return asset('storage/' . $this->photo);
        }
    }
}
