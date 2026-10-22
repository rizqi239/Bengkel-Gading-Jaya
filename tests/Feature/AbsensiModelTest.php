<?php

use App\Models\Absensi;

it('can create an absensi record', function () {
    $absensi = Absensi::create([
        'employee_name' => 'Bambang Subianto',
        'clock_in' => '07:45:00',
        'clock_out' => '16:30:00',
    ]);

    expect($absensi->employee_name)->toBe('Bambang Subianto')
        ->and($absensi->clock_in)->toBe('07:45:00')
        ->and($absensi->clock_out)->toBe('16:30:00');
});
