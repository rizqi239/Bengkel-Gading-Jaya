@extends('layouts.app')

@section('title', 'Edit Absensi')
@section('page_title', 'Edit Absensi')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h4 class="text-lg font-bold text-slate-800">Form Edit Absensi</h4>
                <p class="text-xs text-slate-500">Perbarui status dan jam kehadiran karyawan</p>
            </div>
            <a href="{{ route('absensi.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100">Kembali</a>
        </div>

        <form class="space-y-5" method="POST" action="{{ route('absensi.update', $absensi->id) }}">
            @csrf
            @method('PUT')
            
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Pilih Akun Karyawan Terdaftar</label>
                <select id="userSelect" name="user_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">-- Pilih Akun Terdaftar (Otomatis Role) --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" data-name="{{ $u->name }}" data-role="{{ $u->role ?: 'Karyawan' }}" {{ (old('user_id', $absensi->user_id) == $u->id || old('employee_name', $absensi->employee_name) == $u->name) ? 'selected' : '' }}>
                            {{ $u->name }} &mdash; [Role: {{ $u->role ?: 'Karyawan' }}]
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Karyawan</label>
                <input id="employeeNameInput" name="employee_name" type="text" value="{{ old('employee_name', $absensi->employee_name) }}" placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" required>
            </div>
            
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Bagian / Jabatan (Role Akun)</label>
                <input id="roleInput" name="role" type="text" value="{{ old('role', $absensi->role ?? $absensi->user?->role ?? 'Karyawan') }}" placeholder="Role terisi otomatis" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 focus:outline-none cursor-not-allowed" readonly>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Status Kehadiran</label>
                <select id="attendanceStatus" name="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="Hadir" {{ $absensi->clock_in ? 'selected' : '' }}>Hadir</option>
                    <option value="Sakit" {{ !$absensi->clock_in ? 'selected' : '' }}>Sakit</option>
                    <option value="Izin">Izin</option>
                    <option value="Alpa">Alpa</option>
                </select>
            </div>

            <div id="timeFields" class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Jam Masuk</label>
                    <input name="clock_in" type="time" value="{{ old('clock_in', $absensi->clock_in) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Jam Pulang</label>
                    <input name="clock_out" type="time" value="{{ old('clock_out', $absensi->clock_out) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ route('absensi.index') }}" class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</a>
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-500">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSelect = document.getElementById('userSelect');
        const nameInput = document.getElementById('employeeNameInput');
        const roleInput = document.getElementById('roleInput');
        const statusSelect = document.getElementById('attendanceStatus');
        const timeFields = document.getElementById('timeFields');

        if (userSelect) {
            userSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                if (this.value) {
                    nameInput.value = selected.dataset.name || '';
                    roleInput.value = selected.dataset.role || 'Karyawan';
                }
            });
        }

        function toggleTimeFields() {
            if (statusSelect.value === 'Hadir') {
                timeFields.style.display = 'grid';
            } else {
                timeFields.style.display = 'none';
            }
        }

        if (statusSelect && timeFields) {
            statusSelect.addEventListener('change', toggleTimeFields);
            toggleTimeFields();
        }
    });
</script>
@endsection
