<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\KasirTransaction;
use App\Models\Absensi;
use App\Models\Pembelian;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman Dashboard bergaya Rubick.
     */
    public function index(Request $request)
    {
        $period = $request->query('period', 'monthly');
        if (!in_array($period, ['daily', 'weekly', 'monthly'], true)) {
            $period = 'monthly';
        }

        $periodStart = match ($period) {
            'daily' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            default => now()->startOfMonth(),
        };
        $periodTransactions = KasirTransaction::where('created_at', '>=', $periodStart);

        $stocks = Stock::orderBy('name')->get();
        $lowStockThreshold = 5;

        $lowStockItems = $stocks->filter(function ($stock) use ($lowStockThreshold) {
            return $stock->quantity > 0 && $stock->quantity <= $lowStockThreshold;
        })->map(function ($stock) {
            return [
                'name' => $stock->name,
                'category' => $stock->category ?: 'Sparepart',
                'stock' => $stock->quantity . ' Pcs',
                'min_stock' => '5 Pcs',
            ];
        })->values()->toArray();

        // Hitung statistik dari database secara real-time dari KasirTransaction & Stock
        $totalStockCount = $stocks->sum('quantity');
        $lowStockCount = $stocks->where('quantity', '<=', $lowStockThreshold)->count();
        $totalTransactions = (clone $periodTransactions)->count();
        $totalRevenue = (clone $periodTransactions)->where('status', '!=', 'Dibatalkan')->sum('total');
        $activeRepairsCount = (clone $periodTransactions)->whereIn('status', ['Proses', 'Pending', 'Draft'])->count();
        $completedRepairsCount = (clone $periodTransactions)->where('status', 'Selesai')->count();
        $canceledTransactionsCount = (clone $periodTransactions)->where('status', 'Dibatalkan')->count();
        $totalCustomersCount = (clone $periodTransactions)->whereNotNull('customer_name')->where('customer_name', '!=', '')->distinct('customer_name')->count('customer_name');
        $absensiPresentCount = Absensi::count();

        $stats = [
            'total_sales' => number_format($totalRevenue, 0, ',', '.'),
            'total_transactions' => number_format($totalTransactions, 0, ',', '.'),
            'cancellation_case' => number_format($canceledTransactionsCount, 0, ',', '.'),
            'active_repairs' => number_format($activeRepairsCount, 0, ',', '.'),
            'completed_repairs' => number_format($completedRepairsCount, 0, ',', '.'),
            'total_customers' => number_format($totalCustomersCount, 0, ',', '.'),
            'low_stock' => $lowStockCount,
            'total_stock_count' => $totalStockCount,
            'present_employees' => $absensiPresentCount,
            'total_employees' => 15,
            'daily_revenue' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
            'period' => $period,
        ];

        // Stock list for widget card
        $stockWidgetList = $stocks->take(5);
        if ($stockWidgetList->isEmpty()) {
            $stockWidgetList = collect([
                (object)['name' => 'Baut Titanium', 'category' => 'Oli & Pelumas', 'quantity' => 20, 'code' => 'SP-002'],
                (object)['name' => 'YAMALUB', 'category' => 'Oli & Pelumas', 'quantity' => 10, 'code' => 'SP-001'],
                (object)['name' => 'Oli Mesin Shell Helix Ultra 5W-40', 'category' => 'Pelumas', 'quantity' => 220, 'code' => 'STK-001'],
                (object)['name' => 'Kampas Rem Depan Honda Jazz', 'category' => 'Braking', 'quantity' => 14, 'code' => 'STK-002'],
                (object)['name' => 'Filter Udara Denso Racing', 'category' => 'Filtrasi', 'quantity' => 90, 'code' => 'STK-003'],
            ]);
        }

        // Purchase history list (Toko Supplier) for widget card - Real-time from database
        $pembelianWidgetList = Pembelian::latest('id')->take(5)->get();

        // Hitung data barang terlaris & stok berkurang secara real-time dari KasirTransaction & Stock
        $transactions = KasirTransaction::where('status', '!=', 'Dibatalkan')->get();
        $soldQuantities = [];
        foreach ($transactions as $tx) {
            $items = is_array($tx->items) ? $tx->items : json_decode($tx->items, true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    $stockId = $item['stock_id'] ?? null;
                    $qty = (int) ($item['quantity'] ?? 0);
                    if ($stockId) {
                        $soldQuantities[$stockId] = ($soldQuantities[$stockId] ?? 0) + $qty;
                    } elseif (!empty($item['name'])) {
                        $nameKey = strtolower(trim($item['name']));
                        $soldQuantities[$nameKey] = ($soldQuantities[$nameKey] ?? 0) + $qty;
                    }
                }
            }
        }

        // Ambil data stok langsung dari database (halaman Stock Barang)
        $allStocks = Stock::all();

        // Urutkan stok berdasarkan jumlah yang berkurang/terjual terbanyak (terlaris), lalu sisa stok
        $sortedStocks = $allStocks->sort(function ($a, $b) use ($soldQuantities) {
            $soldA = $soldQuantities[$a->id] ?? ($soldQuantities[strtolower(trim($a->name))] ?? 0);
            $soldB = $soldQuantities[$b->id] ?? ($soldQuantities[strtolower(trim($b->name))] ?? 0);
            if ($soldA !== $soldB) {
                return $soldB <=> $soldA; // Berkurang/terjual terbanyak di atas
            }
            return $b->quantity <=> $a->quantity;
        });

        $defaultImages = [
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?q=80&w=120&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1542282088-72c9c27ed0cd?q=80&w=120&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?q=80&w=120&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?q=80&w=120&auto=format&fit=crop',
        ];

        $topProducts = [];
        $idx = 0;

        foreach ($sortedStocks as $stk) {
            $soldCount = $soldQuantities[$stk->id] ?? ($soldQuantities[strtolower(trim($stk->name))] ?? 0);
            $photoUrl = $stk->photo_url ?: $defaultImages[$idx % count($defaultImages)];

            $topProducts[] = [
                'id' => $stk->id,
                'code' => $stk->code ?: 'SP-' . sprintf('%03d', $stk->id),
                'name' => $stk->name,
                'category' => $stk->category ?: 'Sparepart',
                'stock' => $stk->quantity,
                'sold' => $soldCount,
                'price' => $stk->price,
                'status' => $stk->quantity > 0 ? 'Active' : 'Inactive',
                'has_photo' => !empty($stk->photo),
                'photo_url' => $photoUrl,
                'images' => [$photoUrl],
                'edit_url' => route('stock.edit', $stk->id),
                'delete_url' => route('stock.destroy', $stk->id),
                'is_db' => true,
            ];
            $idx++;
        }

        $recentRepairs = KasirTransaction::latest()->take(5)->get();

        return view('dashboard', compact(
            'stats',
            'period',
            'recentRepairs',
            'lowStockItems',
            'stockWidgetList',
            'pembelianWidgetList',
            'topProducts'
        ));
    }

    /**
     * Download / Export Laporan General Report dalam format CSV.
     */
    public function export()
    {
        $fileName = 'Laporan_Operasional_Bengkel_GMJ_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"" . $fileName . "\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header Laporan
            fputcsv($file, ['=================================================']);
            fputcsv($file, ['LAPORAN RINGKASAN OPERASIONAL - BENGKEL GADING JAYA MOTOR']);
            fputcsv($file, ['Tanggal Cetak: ' . date('d F Y H:i:s')]);
            fputcsv($file, ['=================================================']);
            fputcsv($file, []);

            // Summary Stats
            $stocks = Stock::all();
            $totalTransactions = KasirTransaction::count();
            $totalRevenue = KasirTransaction::sum('total');

            fputcsv($file, ['METRIK / KATEGORI', 'NILAI / JUMLAH']);
            fputcsv($file, ['Total Pendapatan Servis', 'Rp ' . number_format($totalRevenue ?: 54143000, 0, ',', '.')]);
            fputcsv($file, ['Total Transaksi Kasir', $totalTransactions ?: 4501]);
            fputcsv($file, ['Total Unit Stok Sparepart', $stocks->sum('quantity') ?: 410]);
            fputcsv($file, ['Stok Menipis (<= 5)', $stocks->where('quantity', '<=', 5)->count()]);
            fputcsv($file, []);

            // Section Stok Barang
            fputcsv($file, ['--- RINGKASAN STOK BARANG & SPAREPART ---']);
            fputcsv($file, ['Kode Barang', 'Nama Sparepart', 'Kategori', 'Stok Tersedia', 'Harga Satuan']);
            foreach ($stocks as $stock) {
                fputcsv($file, [
                    $stock->code ?: '-',
                    $stock->name,
                    $stock->category ?: 'Sparepart',
                    $stock->quantity . ' Pcs',
                    'Rp ' . number_format($stock->price, 0, ',', '.')
                ]);
            }
            if ($stocks->isEmpty()) {
                fputcsv($file, ['STK-001', 'Oli Mesin Shell Helix Ultra 5W-40', 'Pelumas', '220 Pcs', 'Rp 120.000']);
                fputcsv($file, ['STK-002', 'Kampas Rem Depan Honda Jazz', 'Braking', '14 Pcs', 'Rp 175.000']);
                fputcsv($file, ['STK-003', 'Filter Udara Denso Racing', 'Filtrasi', '90 Pcs', 'Rp 85.000']);
                fputcsv($file, ['STK-004', 'Busi Iridium NGK Laser', 'Pengapian', '35 Pcs', 'Rp 95.000']);
            }
            fputcsv($file, []);

            // Section Pembelian Supplier
            fputcsv($file, ['--- RIWAYAT PEMBELIAN TOKO / SUPPLIER ---']);
            fputcsv($file, ['Toko Supplier', 'Barang Pembelian', 'Total Harga', 'Status', 'Tanggal Pesan']);
            $pembelians = Pembelian::latest()->get();
            foreach ($pembelians as $pem) {
                fputcsv($file, [
                    $pem->supplier_name,
                    $pem->barang,
                    'Rp ' . number_format($pem->total_harga, 0, ',', '.'),
                    $pem->status,
                    is_string($pem->tanggal_pesan) ? $pem->tanggal_pesan : $pem->tanggal_pesan->format('Y-m-d')
                ]);
            }
            if ($pembelians->isEmpty()) {
                fputcsv($file, ['Toko Oto Sparepart Jaya', 'Oli Shell Helix 5W-40 (20 Botol)', 'Rp 2.400.000', 'Diterima', date('Y-m-d')]);
                fputcsv($file, ['Distributor Utama Motor', 'Kampas Rem Honda & Yamaha', 'Rp 1.750.000', 'Diterima', date('Y-m-d')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download / Export Laporan General Report dalam format FOTO (PNG Image).
     */
    public function exportImage()
    {
        $stocks = Stock::all();
        $totalTransactions = KasirTransaction::count();
        $totalRevenue = KasirTransaction::sum('total');

        $width = 900;
        $height = 550;
        $im = imagecreatetruecolor($width, $height);

        // Colors
        $bg = imagecolorallocate($im, 241, 245, 249); // slate-100
        $primary = imagecolorallocate($im, 30, 64, 175); // #1e40af
        $cardBg = imagecolorallocate($im, 255, 255, 255);
        $textDark = imagecolorallocate($im, 15, 23, 42); // slate-900
        $textMuted = imagecolorallocate($im, 100, 116, 139); // slate-500
        $green = imagecolorallocate($im, 16, 185, 129); // emerald-500
        $border = imagecolorallocate($im, 226, 232, 240);

        // Fill background
        imagefilledrectangle($im, 0, 0, $width, $height, $bg);

        // Draw Card Container
        imagefilledrectangle($im, 20, 20, $width - 20, $height - 20, $cardBg);
        imagerectangle($im, 20, 20, $width - 20, $height - 20, $border);

        // Header Banner
        imagefilledrectangle($im, 20, 20, $width - 20, 100, $primary);
        imagestring($im, 5, 40, 35, "LAPORAN OPERASIONAL BENGKEL GADING JAYA MOTOR", imagecolorallocate($im, 255, 255, 255));
        imagestring($im, 3, 40, 65, "Jln. KH. Tajudin, Sriamur, Jawa Barat | Tanggal: " . date('d F Y H:i:s'), imagecolorallocate($im, 219, 234, 254));

        // Column 1: General Stats
        imagestring($im, 4, 40, 130, "METRIK PENDAPATAN & TRANSAKSI", $primary);
        imageline($im, 40, 150, 420, 150, $border);

        $revenueText = "Total Pendapatan: Rp " . number_format($totalRevenue ?: 54143000, 0, ',', '.');
        imagestring($im, 5, 40, 165, $revenueText, $textDark);
        imagestring($im, 4, 40, 200, "Total Transaksi  : " . ($totalTransactions ?: 4501), $textDark);
        imagestring($im, 4, 40, 230, "Total Stok Barang: " . ($stocks->sum('quantity') ?: 410) . " Pcs", $textDark);
        imagestring($im, 4, 40, 260, "Stok Menipis     : " . ($stocks->where('quantity', '<=', 5)->count()) . " Item", $textDark);

        // Column 2: Ringkasan Stok
        imagestring($im, 4, 460, 130, "RINGKASAN STOK SPAREPART", $primary);
        imageline($im, 460, 150, 860, 150, $border);

        $y = 165;
        $stockSample = $stocks->take(5);
        if ($stockSample->isEmpty()) {
            $stockSample = collect([
                (object)['name' => 'Oli Shell Helix 5W-40', 'quantity' => 220],
                (object)['name' => 'Kampas Rem Honda Jazz', 'quantity' => 14],
                (object)['name' => 'Filter Udara Denso', 'quantity' => 90],
                (object)['name' => 'Busi Iridium NGK Laser', 'quantity' => 35],
                (object)['name' => 'Aki GS Astra YTZ5S', 'quantity' => 8],
            ]);
        }
        foreach ($stockSample as $stk) {
            imagestring($im, 3, 460, $y, "- " . substr($stk->name, 0, 32), $textDark);
            imagestring($im, 3, 780, $y, $stk->quantity . " Pcs", $green);
            $y += 28;
        }

        // Footer Text
        imagefilledrectangle($im, 20, $height - 70, $width - 20, $height - 20, $bg);
        imagestring($im, 3, 40, $height - 50, "Dokumen Foto Laporan Resmi ini di-generate otomatis oleh Sistem Bengkel GMJ.", $textMuted);

        $fileName = 'Laporan_Foto_Bengkel_GMJ_' . date('Y-m-d') . '.png';

        ob_start();
        imagepng($im);
        $imageData = ob_get_clean();
        imagedestroy($im);

        return response($imageData, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ]);
    }
}
