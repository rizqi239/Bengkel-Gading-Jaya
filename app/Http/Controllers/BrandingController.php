<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    public function updateLogo(Request $request)
    {
        $validated = $request->validate([
            'logo' => ['required', 'image', 'max:2048'],
        ], [
            'logo.required' => 'Pilih gambar logo terlebih dahulu.',
            'logo.image' => 'File logo harus berupa gambar.',
            'logo.max' => 'Ukuran logo maksimal 2 MB.',
        ]);

        $logoPath = $validated['logo']->store('branding', 'public');
        if (!$logoPath) {
            throw new \RuntimeException('Logo bengkel gagal disimpan.');
        }

        $oldLogoPath = DB::table('site_settings')->where('name', 'logo_path')->value('value');
        DB::table('site_settings')->updateOrInsert(
            ['name' => 'logo_path'],
            ['value' => $logoPath, 'updated_at' => now(), 'created_at' => now()],
        );

        if ($oldLogoPath) {
            Storage::disk('public')->delete($oldLogoPath);
        }

        return redirect()->back()->with('status', 'Logo bengkel berhasil diperbarui.');
    }
}
