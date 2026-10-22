<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'produk' => 'nullable|in:oli,minyak_rem,kampas_rem,ban',
        ]);

        $produkFilter = $validated['produk'] ?? '';
        $kataKunci = [
            'oli' => 'oli',
            'minyak_rem' => 'minyak rem',
            'kampas_rem' => 'kampas rem',
            'ban' => 'ban',
        ];

        $query = Pembelian::latest();
        if ($produkFilter !== '') {
            $query->where('barang', 'like', '%' . $kataKunci[$produkFilter] . '%');
        }

        $pembelians = $query->paginate(10)->withQueryString();
        
        // Calculate statistics
        $totalOrders = Pembelian::count();
        $waitingOrders = Pembelian::where('status', 'Menunggu')->count();
        $totalValue = Pembelian::sum('total_harga');
        
        return view('pembelian.index', compact('pembelians', 'totalOrders', 'waitingOrders', 'totalValue', 'produkFilter'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'produk' => 'nullable|in:oli,minyak_rem,kampas_rem,ban',
        ]);

        $produkFilter = $validated['produk'] ?? '';
        $kataKunci = [
            'oli' => 'oli',
            'minyak_rem' => 'minyak rem',
            'kampas_rem' => 'kampas rem',
            'ban' => 'ban',
        ];

        $query = Pembelian::latest();
        if ($produkFilter !== '') {
            $query->where('barang', 'like', '%' . $kataKunci[$produkFilter] . '%');
        }

        $pembelians = $query->get();
        $fileName = 'pembelian_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($pembelians) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Pembelian');
            $sheet->fromArray([
                'Supplier',
                'Alamat Supplier',
                'Barang',
                'Jumlah',
                'Harga Satuan',
                'Total Harga',
                'Status',
                'Tanggal Pesan',
                'Tanggal Terima',
                'Keterangan',
            ]);
            $sheet->getStyle('A1:J1')->getFont()->setBold(true);
            $sheet->freezePane('A2');

            foreach ($pembelians as $index => $pembelian) {
                $row = $index + 2;
                $textValues = [
                    'A' => $pembelian->supplier_name,
                    'B' => $pembelian->supplier_address ?? '-',
                    'C' => $pembelian->barang,
                    'D' => $pembelian->jumlah,
                    'G' => $pembelian->status,
                    'H' => $pembelian->tanggal_pesan?->format('d-m-Y') ?? '-',
                    'I' => $pembelian->tanggal_terima?->format('d-m-Y') ?? '-',
                    'J' => $pembelian->keterangan ?? '-',
                ];

                foreach ($textValues as $column => $value) {
                    $sheet->setCellValueExplicit("{$column}{$row}", (string) $value, DataType::TYPE_STRING);
                }

                $sheet->setCellValue("E{$row}", (float) $pembelian->harga_satuan);
                $sheet->setCellValue("F{$row}", (float) $pembelian->total_harga);
            }

            $sheet->setAutoFilter('A1:J' . max(1, $pembelians->count() + 1));
            foreach (range('A', 'J') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $fileName, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pembelian.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_address' => 'nullable|string|max:1000',
            'barang' => 'required|string|max:255',
            'jumlah' => 'required|string|max:255',
            'harga_satuan' => 'required|numeric|min:0',
            'status' => 'required|in:Menunggu,Diterima,Dibatalkan',
            'tanggal_pesan' => 'required|date',
            'tanggal_terima' => 'nullable|date|after_or_equal:tanggal_pesan',
            'keterangan' => 'nullable|string|max:500'
        ]);

        // Calculate total_harga (extract number from jumlah string)
        $jumlahNumber = (int) filter_var($request->jumlah, FILTER_SANITIZE_NUMBER_INT);
        $validated['total_harga'] = $jumlahNumber * $request->harga_satuan;

        Pembelian::create($validated);

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembelian $pembelian)
    {
        return view('pembelian.show', compact('pembelian'));
    }

    public function complete(Pembelian $pembelian)
    {
        if ($pembelian->status !== 'Menunggu') {
            return redirect()->route('pembelian.show', $pembelian)
                ->with('success', 'Pembelian ini sudah tidak berstatus menunggu.');
        }

        $pembelian->update([
            'status' => 'Diterima',
            'tanggal_terima' => today(),
        ]);

        return redirect()->route('pembelian.show', $pembelian)
            ->with('success', 'Pembelian berhasil diselesaikan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembelian $pembelian)
    {
        return view('pembelian.edit', compact('pembelian'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembelian $pembelian)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_address' => 'nullable|string|max:1000',
            'barang' => 'required|string|max:255',
            'jumlah' => 'required|string|max:255',
            'harga_satuan' => 'required|numeric|min:0',
            'status' => 'required|in:Menunggu,Diterima,Dibatalkan',
            'tanggal_pesan' => 'required|date',
            'tanggal_terima' => 'nullable|date|after_or_equal:tanggal_pesan',
            'keterangan' => 'nullable|string|max:500'
        ]);

        // Calculate total_harga
        $jumlahNumber = (int) filter_var($request->jumlah, FILTER_SANITIZE_NUMBER_INT);
        $validated['total_harga'] = $jumlahNumber * $request->harga_satuan;

        $pembelian->update($validated);

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembelian $pembelian)
    {
        $pembelian->delete();
        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dihapus');
    }
}

