@extends('layouts.app')

@section('title', 'Absensi Karyawan')
@section('page_title', 'Absensi Karyawan')

@section('content')
<div class="space-y-8">
    {{-- Alert Error Notification --}}
    @if(session('error'))
    <div class="rounded-2xl bg-rose-50 border border-rose-200/80 p-4 text-rose-800 text-xs font-bold flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <i data-lucide="alert-circle" class="h-5 w-5 text-rose-600"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
    </div>
    @endif
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-linear-to-br from-indigo-900 to-indigo-950 p-6 rounded-2xl text-white shadow-md flex flex-col justify-between relative overflow-hidden">
            <div class="absolute right-0 top-0 -translate-y-12 translate-x-12 h-40 w-40 rounded-full bg-indigo-500/10 blur-2xl"></div>
            <div>
                <p class="text-xs text-indigo-300 font-bold uppercase tracking-wider">Waktu Sistem</p>
                <div class="text-3xl sm:text-4xl font-extrabold mt-1 tracking-tight" id="live-clock">00:00:00</div>
                <p class="text-xs text-indigo-200 mt-2 font-medium" id="live-date">Memuat Tanggal...</p>
            </div>
            <div class="mt-6 pt-4 border-t border-indigo-800/60 flex items-center justify-between">
                <span class="text-xs text-indigo-300 font-semibold">Status Perekaman</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/25 text-emerald-300 border border-emerald-500/30">Aktif</span>
            </div>
        </div>

        <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hadir</p>
                <div class="mt-4">
                    <h3 id="summary-present" class="text-3xl font-extrabold text-slate-800">{{ $presentCount ?? '-' }}</h3>
                    <div class="h-1.5 w-full bg-slate-100 rounded-full mt-2 overflow-hidden"><div id="summary-present-bar" class="h-full bg-emerald-500 rounded-full" style="width: {{ $presentCount ? min(100, $presentCount * 10) : 0 }}%"></div></div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Izin / Sakit</p>
                <div class="mt-4">
                    <h3 id="summary-absent" class="text-3xl font-extrabold text-slate-800">{{ $absentCount ?? '-' }}</h3>
                    <div class="h-1.5 w-full bg-slate-100 rounded-full mt-2 overflow-hidden"><div id="summary-absent-bar" class="h-full bg-indigo-500 rounded-full" style="width: {{ $absentCount ? min(100, $absentCount * 10) : 0 }}%"></div></div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between col-span-2 sm:col-span-1">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alpa</p>
                <div class="mt-4">
                    <h3 id="summary-alpha" class="text-3xl font-extrabold text-slate-800">-</h3>
                    <div class="h-1.5 w-full bg-slate-100 rounded-full mt-2 overflow-hidden"><div id="summary-alpha-bar" class="h-full bg-rose-500 rounded-full" style="width: 0%"></div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h4 class="text-lg font-bold text-slate-800">Presensi Karyawan</h4>
                <p class="text-xs text-slate-500">Kelola dan pantau absensi harian seluruh karyawan</p>
            </div>
            <a href="{{ route('absensi.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors rounded-xl shadow-xs">
                <i data-lucide="plus" class="h-4 w-4"></i>
                <span>Input Absensi Manual</span>
            </a>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden p-4 space-y-3">
            @forelse($absensis as $absensi)
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4 space-y-3" data-id="{{ $absensi->id }}">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">{{ strtoupper(substr($absensi->employee_name,0,2)) }}</div>
                            <div>
                                <h5 class="font-bold text-slate-800 text-sm">{{ $absensi->employee_name }}</h5>
                                <p class="text-[10px] text-slate-400 font-semibold">{{ $absensi->role ?? $absensi->user?->role ?? 'Karyawan' }}</p>
                            </div>
                        </div>
                        <div>
                            @if($absensi->clock_in)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Hadir</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">Alpa</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs border-t border-slate-200/60 pt-3">
                        <div class="bg-white p-2.5 rounded-xl border border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Jam Masuk</span>
                            <p class="font-bold text-slate-700 mt-0.5">
                                {{ $absensi->clock_in ? $absensi->clock_in . ' WIB' : '-' }}
                                @if($absensi->clock_in)
                                    <span class="block text-[10px] font-extrabold {{ $absensi->late_minutes > 0 ? 'text-rose-600' : 'text-emerald-600' }} mt-0.5">
                                        {{ $absensi->late_formatted }}
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Jam Pulang</span>
                            <p class="font-bold text-slate-700 mt-0.5">{{ $absensi->clock_out ? $absensi->clock_out . ' WIB' : '-' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1">
                        @php
                            $currentRole = strtolower(Illuminate\Support\Facades\Auth::check() ? (Illuminate\Support\Facades\Auth::user()->role ?? '') : '');
                            $isEditRestricted = in_array($currentRole, ['mekanik', 'kasir']);
                        @endphp

                        @if($isEditRestricted)
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400 text-xs font-bold cursor-not-allowed border border-slate-200/80" title="Role Mekanik dan Kasir tidak diizinkan untuk mengedit">
                                <i data-lucide="lock" class="h-3.5 w-3.5"></i> Terkunci
                            </span>
                        @else
                            <a href="{{ url('absensi/' . $absensi->id . '/edit') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-bold hover:bg-indigo-100">
                                <i data-lucide="edit-3" class="h-3.5 w-3.5"></i> Ubah
                            </a>
                            <button type="button" onclick="deleteAbsensiRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100">
                                <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Hapus
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-slate-500 text-xs font-medium">Belum ada data absensi.</div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-xs font-bold text-slate-400 uppercase">
                        <th class="py-4 px-6">Nama / Jabatan</th>
                        <th class="py-4 px-6">Status Presensi</th>
                        <th class="py-4 px-6">Jam Masuk</th>
                        <th class="py-4 px-6">Jam Pulang</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="absensiTableBody" class="divide-y divide-slate-100 text-sm text-slate-600">
                    @forelse($absensis as $absensi)
                        <tr class="hover:bg-slate-50/40 transition-colors" data-id="{{ $absensi->id }}">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-xs">{{ strtoupper(substr($absensi->employee_name,0,2)) }}</div>
                                    <div>
                                        <div class="font-bold text-slate-800">{{ $absensi->employee_name }}</div>
                                        <div class="text-[10px] font-semibold text-slate-400">{{ $absensi->role ?? $absensi->user?->role ?? 'Karyawan' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if($absensi->clock_in)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Hadir</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">Alpa</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-700">
                                @if($absensi->clock_in)
                                    <span>{{ $absensi->clock_in }} WIB</span>
                                    @if($absensi->late_minutes > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 ml-1.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200/80">{{ $absensi->late_formatted }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 ml-1.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/80">Tepat Waktu</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">Belum ada</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-400">{{ $absensi->clock_out ? $absensi->clock_out . ' WIB' : 'Belum Pulang' }}</td>
                            <td class="py-4 px-6 text-center">
                                @php
                                    $currentRole = strtolower(Illuminate\Support\Facades\Auth::check() ? (Illuminate\Support\Facades\Auth::user()->role ?? '') : '');
                                    $isEditRestricted = in_array($currentRole, ['mekanik', 'kasir']);
                                @endphp

                                @if($isEditRestricted)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-400 text-xs font-semibold cursor-not-allowed border border-slate-200/80" title="Role Mekanik dan Kasir tidak diizinkan untuk mengedit">
                                        <i data-lucide="lock" class="h-3 w-3"></i> Terkunci
                                    </span>
                                @else
                                    <a href="{{ url('absensi/' . $absensi->id . '/edit') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline px-2 py-1">Ubah</a>
                                    <button type="button" onclick="deleteAbsensiRow(this)" class="text-xs font-semibold text-rose-500 hover:text-rose-700 hover:underline px-2 py-1">Hapus</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-slate-500">Belum ada data absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function editAbsensiRow(button) {
        const row = button.closest('tr');
        const id = row?.dataset.id;
        if (id) {
            window.location.href = `${window.location.origin}/absensi/${id}/edit`;
        }
    }

    async function deleteAbsensiRow(button) {
        const row = button.closest('tr');
        const id = row?.dataset.id;
        if (!id) return;

        const baseUrl = '{{ url('absensi') }}';
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const showDeleteError = (message) => {
            let error = document.getElementById('absensi-delete-error');
            if (!error) {
                error = document.createElement('div');
                error.id = 'absensi-delete-error';
                error.className = 'rounded-2xl bg-rose-50 border border-rose-200/80 p-4 text-rose-800 text-xs font-bold';
                error.setAttribute('role', 'alert');
                document.querySelector('.space-y-8')?.prepend(error);
            }
            error.textContent = message;
        };

        button.disabled = true;

        try {
            const res = await fetch(`${baseUrl}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token || ''
                }
            });
            const json = await res.json();
            if (json.success) {
                row.remove();
                // update summary counts on page
                const presentEl = document.getElementById('summary-present');
                const absentEl = document.getElementById('summary-absent');
                const alphaEl = document.getElementById('summary-alpha');
                // simple recalculation: decrement total present if row had clock_in
                const hadClockIn = row.querySelector('td:nth-child(3)').textContent.trim() !== 'Belum ada';
                if (hadClockIn && presentEl) {
                    const val = Number(presentEl.textContent) || 0;
                    presentEl.textContent = Math.max(0, val - 1);
                }
                if (absentEl) {
                    const val = Number(absentEl.textContent) || 0;
                    absentEl.textContent = Math.max(0, val - (hadClockIn ? 0 : 1));
                }
                if (alphaEl) {
                    const val = Number(alphaEl.textContent) || 0;
                    alphaEl.textContent = Math.max(0, val - (hadClockIn ? 0 : 1));
                }
            } else {
                showDeleteError(json.message || 'Gagal menghapus data.');
                button.disabled = false;
            }
        } catch (e) {
            console.error(e);
            showDeleteError('Terjadi error saat menghapus data.');
            button.disabled = false;
        }
    }

    function updateLiveClock() {
        const now = new Date();
        const clockEl = document.getElementById('live-clock');
        const dateEl = document.getElementById('live-date');

        if (clockEl) {
            clockEl.textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
        }

        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }
    }

    updateLiveClock();
    setInterval(updateLiveClock, 1000);
</script>
@endsection
