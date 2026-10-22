<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function index()
    {
        $absensis = Absensi::with('user')->orderBy('employee_name')->get();
        $presentCount = $absensis->whereNotNull('clock_in')->count();
        $total = $absensis->count();
        $absentCount = $total - $presentCount;

        return view('absensi.index', compact('absensis', 'presentCount', 'absentCount', 'total'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('absensi.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'employee_name' => 'required|string|max:255',
            'role' => 'nullable|string|max:100',
            'status' => 'required|string|in:Hadir,Sakit,Izin,Alpa',
            'clock_in' => 'nullable|string',
            'clock_out' => 'nullable|string',
        ]);

        $role = $data['role'] ?? null;
        $userId = $data['user_id'] ?? null;
        $employeeName = $data['employee_name'];

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $employeeName = $user->name;
                $role = $user->role ?: 'Karyawan';
            }
        } else {
            $matchedUser = User::where('name', 'LIKE', trim($employeeName))->first();
            if ($matchedUser) {
                $userId = $matchedUser->id;
                $role = $matchedUser->role ?: 'Karyawan';
            }
        }

        if (!$role) {
            $role = 'Karyawan';
        }

        $lateMinutes = 0;
        if ($data['status'] === 'Hadir') {
            $clock_in = $request->input('clock_in') ?: '08:00';
            $clock_out = $request->input('clock_out');

            $parts = explode(':', $clock_in);
            if (count($parts) >= 2) {
                $hours = (int) $parts[0];
                $minutes = (int) $parts[1];
                $totalMinutes = ($hours * 60) + $minutes;
                $standardMinutes = 8 * 60; // Standard 08:00 WIB
                $lateMinutes = max(0, $totalMinutes - $standardMinutes);
            }
        } else {
            $clock_in = null;
            $clock_out = null;
        }

        Absensi::create([
            'user_id' => $userId,
            'employee_name' => $employeeName,
            'role' => $role,
            'clock_in' => $clock_in,
            'clock_out' => $clock_out,
            'late_minutes' => $lateMinutes,
        ]);

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $absensi = Absensi::findOrFail($id);

        $currentUserRole = Auth::check() ? Auth::user()->role : null;
        $isRestricted = $currentUserRole && in_array(strtolower($currentUserRole), ['mekanik', 'kasir']);

        if ($isRestricted) {
            return redirect()->route('absensi.index')->with('error', 'Akses ditolak: Akun dengan role Mekanik dan Kasir tidak diizinkan untuk mengedit data absensi.');
        }

        $users = User::orderBy('name')->get();
        return view('absensi.edit', compact('absensi', 'users'));
    }

    public function update(Request $request, $id)
    {
        $absensi = Absensi::findOrFail($id);

        $currentUserRole = Auth::check() ? Auth::user()->role : null;
        $isRestricted = $currentUserRole && in_array(strtolower($currentUserRole), ['mekanik', 'kasir']);

        if ($isRestricted) {
            return redirect()->route('absensi.index')->with('error', 'Akses ditolak: Akun dengan role Mekanik dan Kasir tidak diizinkan untuk mengedit data absensi.');
        }

        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'employee_name' => 'required|string|max:255',
            'role' => 'nullable|string|max:100',
            'status' => 'required|string|in:Hadir,Sakit,Izin,Alpa',
            'clock_in' => 'nullable|string',
            'clock_out' => 'nullable|string',
        ]);

        $role = $data['role'] ?? null;
        $userId = $data['user_id'] ?? null;
        $employeeName = $data['employee_name'];

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $employeeName = $user->name;
                $role = $user->role ?: 'Karyawan';
            }
        } else {
            $matchedUser = User::where('name', 'LIKE', trim($employeeName))->first();
            if ($matchedUser) {
                $userId = $matchedUser->id;
                $role = $matchedUser->role ?: 'Karyawan';
            }
        }

        if (!$role) {
            $role = $absensi->role ?: 'Karyawan';
        }

        $lateMinutes = 0;
        if ($data['status'] === 'Hadir') {
            $clock_in = $request->input('clock_in') ?: '08:00';
            $clock_out = $request->input('clock_out');

            $parts = explode(':', $clock_in);
            if (count($parts) >= 2) {
                $hours = (int) $parts[0];
                $minutes = (int) $parts[1];
                $totalMinutes = ($hours * 60) + $minutes;
                $standardMinutes = 8 * 60; // Standard 08:00 WIB
                $lateMinutes = max(0, $totalMinutes - $standardMinutes);
            }
        } else {
            $clock_in = null;
            $clock_out = null;
        }

        $absensi->update([
            'user_id' => $userId,
            'employee_name' => $employeeName,
            'role' => $role,
            'clock_in' => $clock_in,
            'clock_out' => $clock_out,
            'late_minutes' => $lateMinutes,
        ]);

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $absensi = Absensi::findOrFail($id);

        $currentUserRole = Auth::check() ? Auth::user()->role : null;
        $isRestricted = $currentUserRole && in_array(strtolower($currentUserRole), ['mekanik', 'kasir']);

        if ($isRestricted) {
            return response()->json(['success' => false, 'message' => 'Role Mekanik dan Kasir tidak diizinkan menghapus data absensi.'], 403);
        }

        $absensi->delete();

        return response()->json(['success' => true]);
    }
}
