@extends('layouts_kaprodi.app')

@section('title', 'Jadwal Kuliah')
@section('header', 'Manajemen Jadwal')

@push('styles')
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-active {
            display: flex;
        }

        .table-jadwal thead th {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-jadwal tbody tr {
            transition: background 0.15s ease;
        }

        .day-separator td {
            padding: 0 !important;
        }

        .day-separator .day-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .day-separator .day-label i {
            font-size: 0.7rem;
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if (auth()->user()->role == 'admin')
            <div class="mb-6 flex justify-end gap-2">
                <button onclick="openGenerateModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i> Generate Jadwal Baru (Normal)
                </button>
                <button onclick="openRamadanModal()"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition flex items-center gap-2">
                    <i class="fas fa-moon"></i> Generate Jadwal Ramadan (Tabel Terpisah)
                </button>
            </div>
        @endif

        {{-- Form Filter --}}
        <div class="bg-white rounded-xl shadow-md p-5 mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <i class="fas fa-filter text-blue-500"></i> Filter Jadwal
            </h3>
            <form method="GET" action="{{ route('jadwalauth.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @if (auth()->user()->role == 'admin')
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Program Studi</label>
                        <select name="prodi_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">-- Semua Prodi --</option>
                            @foreach ($prodiList as $prodi)
                                <option value="{{ $prodi->id }}"
                                    {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="prodi_id" value="{{ $userProdiId ?? '' }}">
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">-- Semua Kelas --</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->angkatan->tahun }}{{ $kelas->nama }}
                                ({{ $kelas->angkatan->prodi->nama ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Dosen</label>
                    <select name="dosen_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">-- Semua Dosen --</option>
                        @foreach ($dosenList as $dosen)
                            <option value="{{ $dosen->id }}" {{ request('dosen_id') == $dosen->id ? 'selected' : '' }}>
                                {{ $dosen->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Tampilan</label>
                    <select name="tampilan" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="normal" {{ request('tampilan', 'normal') == 'normal' ? 'selected' : '' }}>Jadwal
                            Normal</option>
                        <option value="ramadan" {{ request('tampilan') == 'ramadan' ? 'selected' : '' }}>Jadwal Ramadan
                        </option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-1">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <button type="reset" class="btn btn-ghost px-3"
                        onclick="window.location='{{ route('jadwalauth.index') }}'">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabel Jadwal --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-4 md:px-5 py-3 md:py-4 border-b border-gray-200">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="text-xs md:text-lg font-semibold text-gray-700 truncate">
                        @if ($tampilan == 'ramadan')
                            <i class="fas fa-moon text-emerald-600"></i> Jadwal Ramadan
                        @else
                            <i class="fas fa-calendar-alt text-indigo-600"></i> Jadwal Normal
                        @endif
                    </h3>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('jadwalauth.cetak', request()->all()) }}" target="_blank"
                            class="inline-flex items-center gap-1 px-2.5 md:px-4 py-1.5 md:py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg md:rounded-xl shadow transition hover:-translate-y-0.5">
                            <i class="fas fa-print"></i>
                            <span class="hidden sm:inline">Cetak</span>
                        </a>
                        <span class="text-xs text-gray-400">{{ $jadwals->count() }} jadwal</span>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                @if ($jadwals->count() > 0)
                    @php
                        $currentDay = null;
                        $rowNum = 0;
                    @endphp
                    <table class="table-jadwal min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th
                                    class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Hari & Jam</th>
                                <th
                                    class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Mata Kuliah</th>
                                <th
                                    class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Kelas</th>
                                <th
                                    class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Ruangan</th>
                                <th
                                    class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Dosen</th>
                                <th
                                    class="px-5 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    SKS</th>
                                <th
                                    class="px-5 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    SMT </th>
                                @if (auth()->user()->role == 'admin')
                                    <th
                                        class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Prodi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($jadwals as $jadwal)
                                @php
                                    $hari = $jadwal->hari_nama ?? '-';
                                    $dayChanged = $hari !== $currentDay;
                                    if ($dayChanged) {
                                        $currentDay = $hari;
                                        $rowNum = 0;
                                    }
                                @endphp
                                @if ($dayChanged)
                                    <tr class="day-separator">
                                        <td colspan="{{ 7 + (auth()->user()->role == 'admin' ? 1 : 0) }}">
                                            <div class="day-label bg-indigo-50 text-indigo-700 border-b border-indigo-100">
                                                <i class="fas fa-calendar-day text-indigo-400"></i>
                                                {{ $hari }}
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                <tr class="{{ $rowNum % 2 == 0 ? 'bg-white' : 'bg-slate-50/50' }} hover:bg-blue-50/60">
                                    <td class="px-5 py-3.5 text-sm text-slate-700 whitespace-nowrap">
                                        <span class="font-medium text-slate-800">{{ $jadwal->jam_mulai ?? '' }}</span>
                                        <span class="text-slate-400">–</span>
                                        <span class="text-slate-600">{{ $jadwal->jam_selesai ?? '' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="text-sm font-semibold text-slate-800">
                                            {{ $jadwal->mataKuliah->nama ?? '-' }}</div>
                                        <div class="text-xs text-slate-400 font-mono mt-0.5">
                                            {{ $jadwal->mataKuliah->kode ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-700 whitespace-nowrap">
                                        <span
                                            class="badge badge-blue">{{ $jadwal->kelas->angkatan->tahun ?? '' }}{{ $jadwal->kelas->nama ?? '-' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-700 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-door-open text-slate-400 text-xs"></i>
                                            {{ $jadwal->ruangan->nama ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-700 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-user text-slate-400 text-xs"></i>
                                            {{ $jadwal->dosen->nama ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-center whitespace-nowrap">
                                        <span
                                            class="badge {{ $jadwal->mataKuliah->sks >= 4 ? 'badge-purple' : 'badge-indigo' }}">
                                            {{ $jadwal->mataKuliah->sks ?? '-' }} SKS
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-center whitespace-nowrap">
                                        <span class="badge bg-slate-100 text-slate-700">
                                            {{ $jadwal->mataKuliah->semester_ke ?? '-' }}
                                        </span>
                                    </td>
                                    @if (auth()->user()->role == 'admin')
                                        <td class="px-5 py-3.5 text-sm text-slate-700 whitespace-nowrap">
                                            {{ $jadwal->kelas->angkatan->prodi->nama ?? '-' }}
                                        </td>
                                    @endif
                                </tr>
                                @php $rowNum++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-calendar-times text-5xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Belum ada jadwal yang tersedia.</p>
                        @if (auth()->user()->role == 'admin')
                            <p class="text-sm text-gray-400 mt-1">Silakan klik tombol "Generate Jadwal Baru" untuk membuat
                                jadwal normal, atau "Generate Jadwal Ramadan" untuk membuat jadwal Ramadan.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Generate --}}
    @if (auth()->user()->role == 'admin')
        <div id="generateModal" class="modal">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Generate Jadwal Baru (Normal)</h3>
                    <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <form action="{{ route('jadwalauth.regenerate') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                        <select name="semester" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                        <input type="number" name="tahun_ajaran" value="{{ date('Y') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm" required>
                        <p class="text-xs text-gray-500 mt-1">Contoh: 2025</p>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeGenerateModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Generate</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="ramadanModal" class="modal">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Generate Jadwal Ramadan</h3>
                    <button onclick="closeRamadanModal()" class="text-gray-400 hover:text-gray-600"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <form action="{{ route('jadwalauth.generateRamadan') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                        <select name="semester" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4 text-sm text-yellow-700">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Perhatian: Jam mulai 07:30, maksimal 15:00, 35
                        menit/SKS.
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeRamadanModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Generate
                            Ramadan</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openGenerateModal() {
                document.getElementById('generateModal').classList.add('modal-active');
            }

            function closeGenerateModal() {
                document.getElementById('generateModal').classList.remove('modal-active');
            }
            document.getElementById('generateModal').addEventListener('click', function(e) {
                if (e.target === this) closeGenerateModal();
            });

            function openRamadanModal() {
                document.getElementById('ramadanModal').classList.add('modal-active');
            }

            function closeRamadanModal() {
                document.getElementById('ramadanModal').classList.remove('modal-active');
            }
            document.getElementById('ramadanModal').addEventListener('click', function(e) {
                if (e.target === this) closeRamadanModal();
            });
        </script>
    @endif
@endsection
