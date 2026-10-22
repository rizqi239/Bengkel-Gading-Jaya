<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'user_id',
        'employee_name',
        'role',
        'clock_in',
        'clock_out',
        'late_minutes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLateMinutesAttribute($value)
    {
        if ($value !== null && (int)$value > 0) {
            return (int) $value;
        }

        if (!$this->clock_in) {
            return 0;
        }

        $parts = explode(':', $this->clock_in);
        if (count($parts) < 2) {
            return 0;
        }

        $hours = (int) $parts[0];
        $minutes = (int) $parts[1];

        $totalMinutes = ($hours * 60) + $minutes;
        $standardMinutes = 8 * 60; // Standard 08:00 WIB

        return max(0, $totalMinutes - $standardMinutes);
    }

    public function getLateFormattedAttribute()
    {
        $late = $this->late_minutes;
        if ($late <= 0) {
            return 'Tepat Waktu';
        }

        if ($late >= 60) {
            $h = floor($late / 60);
            $m = $late % 60;
            return "Telat {$h} jam " . ($m > 0 ? "{$m} mnt" : "");
        }

        return "Telat {$late} menit";
    }
}
