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
            $path = $request->file('photo')->store('stocks', 'public');
            $data['photo'] = $path;
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
            // delete old photo if exists
            if ($stock->photo) {
                Storage::disk('public')->delete($stock->photo);
            }
            $data['photo'] = $request->file('photo')->store('stocks', 'public');
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

        if ($stock->photo) {
            Storage::disk('public')->delete($stock->photo);
        }
        $stock->delete();
        return redirect()->route('stock.index')->with('success', 'Barang dihapus.');
    }
}
