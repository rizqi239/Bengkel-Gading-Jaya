@extends('layouts.app')

@section('title', 'Tambah Absensi')
@section('page_title', 'Tambah Absensi')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h4 class="text-lg font-bold text-slate-800">Form Input Absensi</h4>
                <p class="text-xs text-slate-500">Jam masuk otomatis mengunci sesuai jam berjalan (Standar 08:00 WIB)</p>
            </div>
            <a href="{{ route('absensi.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100">Kembali</a>
        </div>

        <form class="space-y-5" method="POST" action="{{ route('absensi.store') }}">
            @csrf
            
            {{-- Dropdown Pilih Akun --}}
            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Pilih Akun Karyawan Terdaftar</label>
                <select id="userSelect" name="user_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">-- Pilih Akun Terdaftar (Otomatis Role) --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" data-name="{{ $u->name }}" data-role="{{ $u->role ?: 'Karyawan' }}">
                            {{ $u->name }} &mdash; [Role: {{ $u->role ?: 'Karyawan' }}]
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Karyawan</label>
                <input id="employeeNameInput" name="employee_name" type="text" placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20" required>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Bagian / Jabatan (Role Akun)</label>
                <input id="roleInput" name="role" type="text" placeholder="Terisi otomatis sesuai role di akun" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 focus:outline-none cursor-not-allowed" readonly>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Status Kehadiran</label>
                <select id="attendanceStatus" name="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="Hadir">Hadir</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Izin">Izin</option>
                    <option value="Alpa">Alpa</option>
                </select>
            </div>

            <div id="timeFields" class="grid gap-4 md:grid-cols-2">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Jam Masuk (Otomatis &amp; Terkunci)</label>
                        <span id="lateIndicator" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200">Tepat Waktu</span>
                    </div>
                    <input id="clockInInput" name="clock_in" type="time" value="{{ date('H:i') }}" class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-sm font-bold text-slate-800 focus:outline-none cursor-not-allowed" readonly>
                    <p class="text-[11px] text-slate-400 mt-1">Mengikuti jam sistem saat ini (Standar 08:00 WIB, lebih dari 08:00 dihitung terlambat).</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Jam Pulang (Bebas / Opsional)</label>
                    <input name="clock_out" type="time" placeholder="Bebas diisi" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <p class="text-[11px] text-slate-400 mt-1">Dapat diisi bebas kapan saja setelah jam kerja selesai.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ route('absensi.index') }}" class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</a>
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-500">Simpan Absensi</button>
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
        const clockInInput = document.getElementById('clockInInput');
        const lateIndicator = document.getElementById('lateIndicator');

        // Otomatisasi Jam Masuk Sesuai Jam Berjalan (Real-Time)
        function setLiveClockIn() {
            if (!clockInInput) return;
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            clockInInput.value = `${hours}:${minutes}`;
            updateLateIndicator();
        }

        function updateLateIndicator() {
            if (!clockInInput || !lateIndicator) return;
            const val = clockInInput.value;
            if (!val) return;

            const [h, m] = val.split(':').map(Number);
            const totalMinutes = (h * 60) + m;
            const targetMinutes = 8 * 60; // Standar Jam 08:00

            if (totalMinutes <= targetMinutes) {
                lateIndicator.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200';
                lateIndicator.textContent = 'Tepat Waktu';
            } else {
                const diff = totalMinutes - targetMinutes;
                let lateStr = '';
                if (diff >= 60) {
                    const hours = Math.floor(diff / 60);
                    const mins = diff % 60;
                    lateStr = `Telat ${hours} jam ${mins > 0 ? mins + ' mnt' : ''}`;
                } else {
                    lateStr = `Telat ${diff} menit`;
                }
                lateIndicator.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700 border border-rose-200';
                lateIndicator.textContent = lateStr;
            }
        }

        // Live Clock In Interval
        setLiveClockIn();
        setInterval(setLiveClockIn, 1000);

        if (userSelect) {
            userSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                if (this.value) {
                    nameInput.value = selected.dataset.name || '';
                    roleInput.value = selected.dataset.role || 'Karyawan';
                }
            });
        }

        if (nameInput && roleInput) {
            nameInput.addEventListener('input', function() {
                if (!userSelect || !userSelect.value) {
                    roleInput.value = 'Karyawan';
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
