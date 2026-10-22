<?php

namespace App\Http\Controllers;

use App\Models\KasirTransaction;
use App\Models\Pembelian;
use App\Models\LaporanEntry;
use App\Models\Stock;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function summary()
    {
        $totalPendapatan = KasirTransaction::where('status', '!=', 'Dibatalkan')->sum('total')
            + LaporanEntry::where('tipe', 'Pemasukan')->sum('nominal');
        $totalBiayaOperasional = Pembelian::where('status', '!=', 'Dibatalkan')->sum('total_harga')
            + LaporanEntry::where('tipe', 'Pengeluaran')->sum('nominal');

        return response()->json([
            'pendapatan' => $totalPendapatan,
            'pengeluaran' => $totalBiayaOperasional,
            'laba_bersih' => $totalPendapatan - $totalBiayaOperasional,
        ]);
    }

    public function index()
    {
        // 1. Data Pemasukan dari Transaksi Kasir & Service
        $kasirTransactions = KasirTransaction::where('status', '!=', 'Dibatalkan')->latest()->get();
        $totalPendapatanKasir = $kasirTransactions->sum('total');
        $totalServis = $kasirTransactions->count();

        // 2. Data Pengeluaran dari Pembelian Supplier
        $pembelians = Pembelian::where('status', '!=', 'Dibatalkan')->latest()->get();
        $totalPembelian = $pembelians->sum('total_harga');

        // 3. Data Catatan Keuangan Manual
        $manualEntries = LaporanEntry::latest('tanggal')->latest('id')->get();
        $totalPemasukanManual = $manualEntries->where('tipe', 'Pemasukan')->sum('nominal');
        $totalPengeluaranManual = $manualEntries->where('tipe', 'Pengeluaran')->sum('nominal');

        // Total gabungan
        $totalPendapatan = $totalPendapatanKasir + $totalPemasukanManual;
        $totalBiayaOperasional = $totalPembelian + $totalPengeluaranManual;
        $labaBersih = $totalPendapatan - $totalBiayaOperasional;
        $stokHampirHabis = Stock::where('quantity', '<=', 5)->count();

        // Gabungkan semua item menjadi 1 daftar rincian laporan (Unified Financial Stream)
        $reportItems = collect();

        foreach ($kasirTransactions as $tx) {
            $reportItems->push((object)[
                'id' => $tx->id,
                'source' => 'kasir',
                'kode' => 'KSR-' . sprintf('%04d', $tx->id),
                'tanggal' => $tx->created_at,
                'kategori' => $tx->transaction_type ?: 'Servis & Sparepart',
                'keterangan' => ($tx->customer_name ? $tx->customer_name . ' - ' : '') . ($tx->vehicle_model ?: 'Pelanggan') . ' (' . ($tx->plate_number ?: 'Tanpa Plat') . ')',
                'tipe' => 'Pemasukan',
                'nominal' => $tx->total,
                'status' => $tx->status,
                'is_deletable' => true,
            ]);
        }

        foreach ($pembelians as $pem) {
            $tanggal = $pem->tanggal_pesan ?? $pem->created_at;
            $reportItems->push((object)[
                'id' => $pem->id,
                'source' => 'pembelian',
                'kode' => 'PEM-' . sprintf('%04d', $pem->id),
                'tanggal' => $tanggal,
                'kategori' => 'Pembelian Sparepart',
                'keterangan' => $pem->supplier_name . ' - ' . $pem->barang . ' (' . $pem->jumlah . ' Pcs)',
                'tipe' => 'Pengeluaran',
                'nominal' => $pem->total_harga,
                'status' => $pem->status,
                'is_deletable' => true,
            ]);
        }

        foreach ($manualEntries as $entry) {
            $reportItems->push((object)[
                'id' => $entry->id,
                'source' => 'manual',
                'kode' => 'CAT-' . sprintf('%04d', $entry->id),
                'tanggal' => $entry->tanggal,
                'kategori' => $entry->kategori ?: 'Operasional',
                'keterangan' => $entry->keterangan,
                'tipe' => $entry->tipe,
                'nominal' => $entry->nominal,
                'status' => 'Catatan Manual',
                'is_deletable' => true,
            ]);
        }

        // Urutkan berdasarkan tanggal terbaru
        $reportItems = $reportItems->sortByDesc('tanggal')->values();

        // Hitung data grafik mingguan (7 hari terakhir)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $dateStr = $dateObj->format('Y-m-d');
            $dayLabel = $dateObj->translatedFormat('D') ?: $dateObj->format('D');

            $incomeKasir = $kasirTransactions->filter(function($t) use ($dateStr) {
                return $t->created_at && $t->created_at->format('Y-m-d') === $dateStr;
            })->sum('total');

            $incomeManual = $manualEntries->filter(function($e) use ($dateStr) {
                return $e->tanggal && $e->tanggal->format('Y-m-d') === $dateStr && $e->tipe === 'Pemasukan';
            })->sum('nominal');

            $weeklyData[] = [
                'date' => $dateObj->format('d M'),
                'day' => $dayLabel,
                'amount' => $incomeKasir + $incomeManual,
            ];
        }

        $maxWeeklyAmount = max(array_column($weeklyData, 'amount')) ?: 1;
        foreach ($weeklyData as &$wd) {
            $wd['height'] = $wd['amount'] > 0 ? max(15, round(($wd['amount'] / $maxWeeklyAmount) * 100)) : 10;
        }

        return view('laporan', compact(
            'totalPendapatan',
            'totalBiayaOperasional',
            'labaBersih',
            'totalServis',
            'stokHampirHabis',
            'reportItems',
            'weeklyData'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipe' => 'required|in:Pemasukan,Pengeluaran',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
        ]);

        LaporanEntry::create($data);

        return redirect()->route('laporan')->with('success', 'Catatan laporan keuangan berhasil ditambahkan.');
    }

    public function destroy(Request $request, $id)
    {
        $source = $request->query('source', 'manual');

        if ($source === 'kasir') {
            $kasir = KasirTransaction::find($id);
            if ($kasir) {
                $kasir->delete();
            }
        } elseif ($source === 'pembelian') {
            $pembelian = Pembelian::find($id);
            if ($pembelian) {
                $pembelian->delete();
            }
        } else {
            $entry = LaporanEntry::find($id);
            if ($entry) {
                $entry->delete();
            }
        }

        return redirect()->route('laporan')->with('success', 'Data laporan berhasil dihapus.');
    }
}
