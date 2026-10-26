<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::all()->sortBy(function (Stock $stock) {
            if (preg_match('/^SP[-_]?(\d+)$/i', $stock->code, $matches)) {
                return (int) $matches[1];
            }

            return PHP_INT_MAX;
        })->values();

        return view('stock.index', compact('stocks'));
    }

    public function create()
    {
        $code = $this->nextStockCode();

        return view('stock.create', compact('code'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'category' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->processPhoto($request->file('photo'));
        }

        $data['quantity'] = $data['quantity'] ?? 0;
        $data['price'] = $data['price'] ?? 0;
        $data['code'] = $this->nextStockCode();

        Stock::create($data);

        return redirect()->route('stock.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    private function nextStockCode(): string
    {
        $highestNumber = Stock::query()->pluck('code')->reduce(function (int $highest, string $code) {
            if (preg_match('/^SP[-_]?(\d+)$/i', $code, $matches)) {
                return max($highest, (int) $matches[1]);
            }

            return $highest;
        }, 0);

        return 'SP-' . str_pad((string) ($highestNumber + 1), 3, '0', STR_PAD_LEFT);
    }

    public function edit(Stock $stock)
    {
        $userRole = Auth::check() ? Auth::user()->role : null;
        if ($userRole && in_array(strtolower($userRole), ['mekanik', 'kasir'])) {
            return redirect()->route('stock.index')->with('error', 'Akses ditolak: Akun dengan role Mekanik dan Kasir tidak diizinkan untuk mengedit stok barang.');
        }

        return view('stock.edit', compact('stock'));
    }

    public function update(Request $request, Stock $stock)
    {
        $userRole = Auth::check() ? Auth::user()->role : null;
        if ($userRole && in_array(strtolower($userRole), ['mekanik', 'kasir'])) {
            return redirect()->route('stock.index')->with('error', 'Akses ditolak: Akun dengan role Mekanik dan Kasir tidak diizinkan untuk mengedit stok barang.');
        }

        $data = $request->validate([
            'code' => 'required|string|unique:stocks,code,' . $stock->id,
            'name' => 'required|string',
            'category' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if it was stored locally
            if ($stock->photo && ! str_starts_with($stock->photo, 'data:')) {
                try {
                    Storage::disk('public')->delete($stock->photo);
                } catch (\Throwable $e) {
                    // Ignore deletion failure
                }
            }
            $data['photo'] = $this->processPhoto($request->file('photo'));
        }

        $stock->update($data);

        return redirect()->route('stock.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function increase(Request $request, Stock $stock)
    {
        $userRole = Auth::check() ? Auth::user()->role : null;
        if ($userRole && in_array(strtolower($userRole), ['mekanik', 'kasir'])) {
            return redirect()->route('stock.index')->with('error', 'Akses ditolak: Akun dengan role Mekanik dan Kasir tidak diizinkan untuk menambah stok barang.');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $stock->increment('quantity', $validated['quantity']);

        return redirect()->route('stock.index')->with('success', 'Stok ' . $stock->name . ' berhasil ditambahkan sebanyak ' . $validated['quantity'] . ' pcs.');
    }

    public function destroy(Stock $stock)
    {
        $userRole = Auth::check() ? Auth::user()->role : null;
        if ($userRole && in_array(strtolower($userRole), ['mekanik', 'kasir'])) {
            return redirect()->route('stock.index')->with('error', 'Akses ditolak: Akun dengan role Mekanik dan Kasir tidak diizinkan untuk menghapus stok barang.');
        }

        if ($stock->photo && ! str_starts_with($stock->photo, 'data:')) {
            try {
                Storage::disk('public')->delete($stock->photo);
            } catch (\Throwable $e) {
                // Ignore deletion failure
            }
        }
        $stock->delete();
        return redirect()->route('stock.index')->with('success', 'Barang dihapus.');
    }

    /**
     * Process uploaded photo into an optimized Base64 data URI.
     * Works 100% on serverless (Vercel) without writing to read-only disk.
     */
    private function processPhoto($file): string
    {
        if (extension_loaded('gd') && function_exists('imagecreatefromstring')) {
            try {
                $imageContent = file_get_contents($file->getRealPath());
                $src = @imagecreatefromstring($imageContent);
                if ($src) {
                    $width = imagesx($src);
                    $height = imagesy($src);
                    $maxDim = 800;

                    if ($width > $maxDim || $height > $maxDim) {
                        $ratio = min($maxDim / $width, $maxDim / $height);
                        $newWidth = (int) ($width * $ratio);
                        $newHeight = (int) ($height * $ratio);

                        $dst = imagecreatetruecolor($newWidth, $newHeight);
                        imagealphablending($dst, false);
                        imagesavealpha($dst, true);
                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($src);
                        $src = $dst;
                    }

                    ob_start();
                    imagejpeg($src, null, 80);
                    $compressed = ob_get_clean();
                    imagedestroy($src);

                    if ($compressed) {
                        return 'data:image/jpeg;base64,' . base64_encode($compressed);
                    }
                }
            } catch (\Throwable $e) {
                // Fallback to direct raw base64
            }
        }

        return 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
    }
}
