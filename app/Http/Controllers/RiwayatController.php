<?php

namespace App\Http\Controllers;

use App\Models\KasirTransaction;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $serviceTypes = ['Servis + Sparepart', 'Servis Mesin', 'Tune Up', 'Service CVT', 'BoreUp', 'Servis'];

        $query = KasirTransaction::where('status', 'Selesai')
            ->whereIn('transaction_type', $serviceTypes)
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('plate_number', 'like', "%{$search}%")
                  ->orWhere('transaction_type', 'like', "%{$search}%")
                  ->orWhere('vehicle_model', 'like', "%{$search}%");
            });
        }

        $transactions = $query->get();

        // Calculate statistics across all completed transactions
        $baseQuery = KasirTransaction::where('status', 'Selesai');

        $totalServis = (clone $baseQuery)->whereIn('transaction_type', $serviceTypes)->count();
        $totalCustomers = (clone $baseQuery)->whereNotNull('customer_name')->distinct('customer_name')->count('customer_name');
        $totalVehicles = (clone $baseQuery)->whereNotNull('vehicle_model')->where('vehicle_model', '!=', '-')->distinct('vehicle_model')->count('vehicle_model');

        return view('riwayat', compact(
            'transactions', 
            'totalCustomers', 
            'totalServis', 
            'totalVehicles',
            'serviceTypes'
        ));
    }

    public function export(Request $request)
    {
        $serviceTypes = ['Servis + Sparepart', 'Servis Mesin', 'Tune Up', 'Service CVT', 'BoreUp', 'Servis'];
        $query = KasirTransaction::where('status', 'Selesai')
            ->whereIn('transaction_type', $serviceTypes)
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('plate_number', 'like', "%{$search}%")
                  ->orWhere('transaction_type', 'like', "%{$search}%")
                  ->orWhere('vehicle_model', 'like', "%{$search}%");
            });
        }

        $transactions = $query->get();

        // Create CSV
        $filename = 'riwayat_transaksi_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = array(
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Kategori', 'Jenis Transaksi', 'Pelanggan', 'Plat Nomor', 'Model Kendaraan', 'Total', 'Tanggal');

        $callback = function() use ($transactions, $columns, $serviceTypes) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');

            foreach ($transactions as $transaction) {
                fputcsv($file, array(
                    'Jasa Servis',
                    $transaction->transaction_type,
                    $transaction->customer_name ?? 'Pelanggan Umum',
                    $transaction->plate_number ?? '-',
                    $transaction->vehicle_model ?? '-',
                    $transaction->total,
                    $transaction->created_at->format('d-m-Y H:i')
                ), ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
