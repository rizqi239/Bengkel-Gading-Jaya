<?php

namespace App\Http\Controllers;

use App\Models\KasirTransaction;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KasirController extends Controller
{
    public function index(Request $request)
    {
        $transactionType = $request->query('type', 'all');
        $serviceTypes = ['Servis + Sparepart', 'Servis Mesin', 'Tune Up', 'Service CVT', 'BoreUp', 'Servis'];

        $query = KasirTransaction::orderBy('created_at', 'desc');
        if ($transactionType === 'servis') {
            $query->whereIn('transaction_type', $serviceTypes);
        } elseif ($transactionType === 'produk') {
            $query->where(function ($query) {
                $query->where('transaction_type', 'Penjualan Barang')
                    ->orWhereJsonLength('items', '>', 0);
            });
        } else {
            $transactionType = 'all';
        }

        $transactions = $query->get();
        return view('kasir.index', compact('transactions', 'transactionType'));
    }

    public function export(Request $request)
    {
        $transactionType = $request->query('type', 'all');
        $serviceTypes = ['Servis + Sparepart', 'Servis Mesin', 'Tune Up', 'Service CVT', 'BoreUp', 'Servis'];

        $query = KasirTransaction::orderBy('created_at', 'desc');
        if ($transactionType === 'servis') {
            $query->whereIn('transaction_type', $serviceTypes);
        } elseif ($transactionType === 'produk') {
            $query->where(function ($query) {
                $query->where('transaction_type', 'Penjualan Barang')
                    ->orWhereJsonLength('items', '>', 0);
            });
        }

        $transactions = $query->get();
        $fileName = 'transaksi_kasir_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($transactions) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Transaksi Kasir');
            $sheet->fromArray(['No. Transaksi', 'Tanggal', 'Pelanggan', 'Tipe Transaksi', 'Nomor Plat', 'Kendaraan', 'Item', 'Total', 'Metode Pembayaran', 'Status']);
            $sheet->getStyle('A1:J1')->getFont()->setBold(true);
            $sheet->freezePane('A2');

            foreach ($transactions as $index => $transaction) {
                $row = $index + 2;
                $items = collect($transaction->items ?? [])->map(function ($item) {
                    return ($item['name'] ?? 'Item') . ' (x' . ($item['quantity'] ?? 1) . ')';
                })->implode(', ');

                $sheet->setCellValue("A{$row}", $index + 1);
                $textValues = [
                    'B' => $transaction->created_at?->format('d-m-Y H:i') ?? '-',
                    'C' => $transaction->customer_name ?? 'Pelanggan Umum',
                    'D' => $transaction->transaction_type,
                    'E' => $transaction->plate_number ?? '-',
                    'F' => $transaction->vehicle_model ?? '-',
                    'G' => $items,
                    'I' => $transaction->payment_method,
                    'J' => $transaction->status,
                ];

                foreach ($textValues as $column => $value) {
                    $sheet->setCellValueExplicit("{$column}{$row}", (string) $value, DataType::TYPE_STRING);
                }

                $sheet->setCellValue("H{$row}", (float) $transaction->total);
            }

            $sheet->setAutoFilter('A1:J' . max(1, $transactions->count() + 1));
            foreach (range('A', 'J') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $fileName, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function create()
    {
        $stocks = Stock::orderBy('name')->get();
        return view('kasir.create', compact('stocks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'nullable|string|max:150',
            'transaction_type' => 'required|in:Servis + Sparepart,Servis Mesin,Tune Up,Service CVT,BoreUp,Penjualan Barang',
            'plate_number' => 'nullable|string|max:50',
            'vehicle_model' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'items' => 'nullable|array',
            'items.*.stock_id' => 'required_with:items|exists:stocks,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.price' => 'required_with:items|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:Tunai,Dana,GoPay,Bank BCA,Bank Mandiri',
            'status' => 'required|in:Draft,Selesai,Dibatalkan',
        ]);

        $data['items'] = $this->normalizeItems($data['items'] ?? []);
        $servicePrice = $this->getServicePrice($data['transaction_type']);
        $data['subtotal'] = array_sum(array_column($data['items'], 'price')) + $servicePrice;
        $data['discount'] = $data['discount'] ?? 0;
        $data['tax'] = $data['tax'] ?? 0;
        $data['total'] = max(0, $data['subtotal'] - $data['discount'] + $data['tax']);
        $transaction = KasirTransaction::create($data);

        if ($transaction->status === 'Selesai') {
            $this->adjustStockForItems($transaction->items, -1);
        }

        return redirect()->route('kasir.index')->with('success', 'Transaksi berhasil disimpan.');
    }

    public function show(KasirTransaction $kasir)
    {
        return view('kasir.show', compact('kasir'));
    }

    public function updateStatus(Request $request, KasirTransaction $kasir)
    {
        $userRole = Auth::check() ? Auth::user()->role : null;
        if ($userRole && in_array(strtolower($userRole), ['mekanik', 'kasir'])) {
            return redirect()->route('kasir.show', $kasir)->with('error', 'Akses ditolak: Akun dengan role Mekanik dan Kasir tidak diizinkan untuk mengubah status transaksi.');
        }

        $data = $request->validate([
            'status' => 'required|in:Draft,Selesai',
        ]);

        if ($kasir->status === 'Dibatalkan') {
            return redirect()->route('kasir.show', $kasir)->with('error', 'Transaksi yang dibatalkan tidak dapat diubah statusnya.');
        }

        DB::transaction(function () use ($kasir, $data) {
            $lockedTransaction = KasirTransaction::whereKey($kasir->id)->lockForUpdate()->firstOrFail();
            $newStatus = $data['status'];

            if ($lockedTransaction->status !== $newStatus) {
                if ($lockedTransaction->status === 'Selesai') {
                    $this->adjustStockForItems($lockedTransaction->items ?? [], 1);
                }

                if ($newStatus === 'Selesai') {
                    $this->adjustStockForItems($lockedTransaction->items ?? [], -1);
                }

                $lockedTransaction->update(['status' => $newStatus]);
            }
        });

        $statusLabel = $data['status'] === 'Selesai' ? 'Selesai' : 'Belum selesai';

        return redirect()->route('kasir.show', $kasir)->with('success', 'Status transaksi berhasil diubah menjadi ' . $statusLabel . '.');
    }

    public function print(KasirTransaction $kasir)
    {
        return view('kasir.print', compact('kasir'));
    }

    public function edit(KasirTransaction $kasir)
    {
        $userRole = Auth::check() ? Auth::user()->role : null;
        if ($userRole && in_array(strtolower($userRole), ['mekanik', 'kasir'])) {
            return redirect()->route('kasir.index')->with('error', 'Akses ditolak: Akun dengan role Mekanik dan Kasir tidak diizinkan untuk mengedit transaksi.');
        }

        $stocks = Stock::orderBy('name')->get();
        return view('kasir.edit', compact('kasir', 'stocks'));
    }

    public function update(Request $request, KasirTransaction $kasir)
    {
        $userRole = Auth::check() ? Auth::user()->role : null;
        if ($userRole && in_array(strtolower($userRole), ['mekanik', 'kasir'])) {
            return redirect()->route('kasir.index')->with('error', 'Akses ditolak: Akun dengan role Mekanik dan Kasir tidak diizinkan untuk mengedit transaksi.');
        }

        $data = $request->validate([
            'customer_name' => 'nullable|string|max:150',
            'transaction_type' => 'required|in:Servis + Sparepart,Servis Mesin,Tune Up,Service CVT,BoreUp,Penjualan Barang',
            'plate_number' => 'nullable|string|max:50',
            'vehicle_model' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'items' => 'nullable|array',
            'items.*.stock_id' => 'required_with:items|exists:stocks,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.price' => 'required_with:items|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:Tunai,Dana,GoPay,Bank BCA,Bank Mandiri',
            'status' => 'required|in:Draft,Selesai,Dibatalkan',
        ]);

        $data['items'] = $this->normalizeItems($data['items'] ?? []);
        $servicePrice = $this->getServicePrice($data['transaction_type']);
        $data['subtotal'] = array_sum(array_column($data['items'], 'price')) + $servicePrice;
        $data['discount'] = $data['discount'] ?? 0;
        $data['tax'] = $data['tax'] ?? 0;
        $data['total'] = max(0, $data['subtotal'] - $data['discount'] + $data['tax']);
        $oldItems = $kasir->items ?? [];
        $oldStatus = $kasir->status;
        $newStatus = $data['status'];

        if ($oldStatus === 'Selesai') {
            $this->adjustStockForItems($oldItems, 1);
        }

        if ($newStatus === 'Selesai') {
            $this->adjustStockForItems($data['items'], -1);
        }

        $kasir->update($data);

        return redirect()->route('kasir.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(KasirTransaction $kasir)
    {
        $userRole = Auth::check() ? Auth::user()->role : null;
        if ($userRole && in_array(strtolower($userRole), ['mekanik', 'kasir'])) {
            return redirect()->route('kasir.index')->with('error', 'Akses ditolak: Akun dengan role Mekanik dan Kasir tidak diizinkan untuk menghapus transaksi.');
        }

        if ($kasir->status === 'Selesai') {
            $this->adjustStockForItems($kasir->items ?? [], 1);
        }

        $kasir->delete();
        return redirect()->route('kasir.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    protected function getServicePrice(string $transactionType): float
    {
        return match ($transactionType) {
            'Servis + Sparepart' => 10000,
            'Servis Mesin' => 1000000,
            'Tune Up' => 500000,
            'Service CVT' => 65000,
            'BoreUp' => 2000000,
            'Penjualan Barang' => 0,
            default => 0,
        };
    }

    protected function normalizeItems(array $items): array
    {
        return array_map(function ($item) {
            if (!empty($item['stock_id'])) {
                $stock = Stock::find($item['stock_id']);
                if ($stock) {
                    $item['name'] = $stock->name;
                    $item['price'] = $item['price'] ?? $stock->price;
                }
            }

            return [
                'stock_id' => isset($item['stock_id']) ? (int) $item['stock_id'] : null,
                'name' => $item['name'] ?? null,
                'quantity' => isset($item['quantity']) ? (int) $item['quantity'] : 0,
                'price' => isset($item['price']) ? (float) $item['price'] : 0,
            ];
        }, $items);
    }

    protected function adjustStockForItems(array $items, int $multiplier): void
    {
        foreach ($items as $item) {
            if (empty($item['stock_id']) || empty($item['quantity'])) {
                continue;
            }

            $stock = Stock::find($item['stock_id']);
            if (!$stock) {
                continue;
            }

            $quantity = (int) $item['quantity'] * $multiplier;
            $stock->quantity = max(0, $stock->quantity + $quantity);
            $stock->save();
        }
    }
}
