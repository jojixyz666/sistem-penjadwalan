<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jadwal Publik - Sistem Penjadwalan ACO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 35%, #bfdbfe 100%);
            min-height: 100vh;
        }

        .glass {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.08);
        }

        .nav-blur {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            border-bottom: 2px solid rgba(37, 99, 235, 0.15);
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.06);
        }

        .glow {
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.18);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            justify-content: center;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: #2563eb;
            color: white;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.18);
        }

        .btn-primary:hover {
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
        }

        .btn-success {
            background: #059669;
            color: white;
        }

        .btn-ghost {
            background: white;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .btn-ghost:hover {
            background: #f8fafc;
        }

        th {
            @apply px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider;
        }

        td {
            @apply px-4 py-3 text-sm;
        }

        .table-jadwal thead th {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-jadwal th,
        .table-jadwal td {
            border: 1px solid #e2e8f0;
        }

        .table-jadwal thead th {
            border-color: #1e293b;
        }

        .day-separator td {
            padding: 0 !important;
            border-left: none;
            border-right: none;
        }

        .day-separator .day-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        select,
        input {
            @apply border border-gray-300 rounded-lg px-4 py-2.5 w-full text-sm;
            transition: all 0.3s ease;
        }

        select:focus,
        input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        @media (max-width: 767px) {
            .nav-blur .flex {
                min-height: 56px;
            }
            .nav-blur h1 {
                font-size: 1rem;
            }
            .nav-blur img {
                width: 2.5rem;
                height: 2.5rem;
            }
        }

        @media (min-width: 768px) {
            .filter-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }
    </style>
</head>

<body class="antialiased">

    {{-- Navbar --}}
    <nav class="nav-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('image/logo.png') }}" alt="Logo" class="w-14 h-14 object-contain">
                    <div>
                        <h1 class="font-bold text-lg md:text-xl leading-tight text-slate-800">Sistem Penjadwalan</h1>
                        <p class="text-slate-500 text-sm hidden md:block">Fakultas Teknik Universitas Wiraraja</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 md:gap-4">
                    <a href="{{ route('welcome') }}"
                        class="px-3 md:px-5 py-1.5 md:py-2 text-slate-700 hover:bg-blue-50 rounded-xl transition flex items-center gap-1.5 md:gap-2 text-sm md:text-base">
                        <i class="fas fa-home text-blue-500"></i>
                        <span class="hidden md:inline">Beranda</span>
                    </a>
                    <a href="{{ route('login') }}"
                        class="px-3 md:px-5 py-1.5 md:py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition flex items-center gap-1.5 md:gap-2 font-semibold glow text-sm md:text-base">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="hidden md:inline">Login</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-blue-900">
                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i> Jadwal Kuliah
            </h1>
            <p class="text-slate-500 mt-1 text-sm">Fakultas Teknik Universitas Wiraraja Sumenep Madura</p>
        </div>

        {{-- Filter --}}
        <div class="glass rounded-2xl p-5 mb-6">
            <form method="GET" class="grid grid-cols-2 md:grid-cols-6 gap-3">
                <select name="tampilan">
                    <option value="normal" {{ request('tampilan', 'normal') == 'normal' ? 'selected' : '' }}>Jadwal
                        Normal</option>
                    <option value="ramadan" {{ request('tampilan') == 'ramadan' ? 'selected' : '' }}>Jadwal Ramadan
                    </option>
                </select>
                <select name="prodi">
                    <option value="">Semua Prodi</option>
                    @foreach ($prodiList as $p)
                        <option value="{{ $p }}" {{ request('prodi') == $p ? 'selected' : '' }}>
                            {{ $p }}</option>
                    @endforeach
                </select>
                <select name="kelas">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>
                            {{ $k }}</option>
                    @endforeach
                </select>
                <select name="dosen">
                    <option value="">Semua Dosen</option>
                    @foreach ($dosenList as $d)
                        <option value="{{ $d }}" {{ request('dosen') == $d ? 'selected' : '' }}>
                            {{ $d }}</option>
                    @endforeach
                </select>
                <select name="ruangan">
                    <option value="">Semua Ruangan</option>
                    @foreach ($ruanganList as $r)
                        <option value="{{ $r }}" {{ request('ruangan') == $r ? 'selected' : '' }}>
                            {{ $r }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary flex-1">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('jadwalglobal.list') }}" class="btn btn-ghost px-3">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="glass rounded-2xl overflow-hidden">
            <div class="bg-white px-4 md:px-5 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-xs md:text-sm font-semibold text-gray-700 truncate">
                        <i class="fas fa-table mr-1.5 text-blue-500"></i> Data Jadwal
                    </h2>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('jadwalglobal.cetak', request()->all()) }}" target="_blank"
                            class="inline-flex items-center gap-1 px-2.5 md:px-4 py-1.5 md:py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg md:rounded-xl shadow transition hover:-translate-y-0.5">
                            <i class="fas fa-print"></i>
                            <span class="hidden sm:inline">Cetak</span>
                        </a>
                        <span class="text-xs text-gray-400">{{ $jadwals->count() }} jadwal</span>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                @php
                    $currentDay = null;
                    $rowNum = 0;
                @endphp
                <table class="table-jadwal min-w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-gray-600">Hari</th>
                            <th class="text-gray-600">Jam</th>
                            <th class="text-gray-600">Mata Kuliah</th>
                            <th class="text-gray-600">Kelas</th>
                            <th class="text-gray-600">Prodi</th>
                            <th class="text-gray-600 text-center">SKS</th>
                            <th class="text-gray-600 text-center">SMT </th>
                            <th class="text-gray-600">Dosen</th>
                            <th class="text-gray-600">Ruangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwals as $j)
                            @php
                                if ($tampilan == 'ramadan') {
                                    $jamMulai = \Carbon\Carbon::parse($j->jam_mulai)->format('H:i');
                                    $jamSelesai = \Carbon\Carbon::parse($j->jam_selesai)->format('H:i');
                                    $hariNama =
                                        ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$j->hari] ?? '-';
                                } else {
                                    $start = 7 * 60 + 30 + $j->slot_mulai * 50;
                                    $end = $start + $j->mataKuliah->sks * 50;
                                    $jamMulai = sprintf('%02d:%02d', floor($start / 60), $start % 60);
                                    $jamSelesai = sprintf('%02d:%02d', floor($end / 60), $end % 60);
                                    $hariNama =
                                        ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$j->hari] ?? '-';
                                }
                                $dayChanged = $hariNama !== $currentDay;
                                if ($dayChanged) {
                                    $currentDay = $hariNama;
                                    $rowNum = 0;
                                }
                            @endphp
                            @if ($dayChanged)
                                <tr class="day-separator">
                                    <td colspan="9">
                                        <div class="day-label bg-indigo-50 text-indigo-700 border-b border-indigo-100">
                                            <i class="fas fa-calendar-day text-indigo-400"></i>
                                            {{ $hariNama }}
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            <tr class="{{ $rowNum % 2 == 0 ? 'bg-white' : 'bg-slate-50/50' }} hover:bg-blue-50/60">
                                <td class="font-semibold text-slate-800">{{ $hariNama }}</td>
                                <td class="text-slate-600 whitespace-nowrap">
                                    <span class="font-medium text-slate-700">{{ $jamMulai }}</span>
                                    <span class="text-slate-300">–</span>
                                    <span>{{ $jamSelesai }}</span>
                                </td>
                                <td>
                                    <span class="font-semibold text-slate-800">{{ $j->mataKuliah->nama ?? '-' }}</span>
                                    @if (!empty($j->mataKuliah->kode))
                                        <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $j->mataKuliah->kode }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        {{ ($j->kelas->angkatan->tahun ?? '') . ($j->kelas->nama ?? '') }}
                                    </span>
                                </td>
                                <td class="text-slate-600">{{ $j->kelas->angkatan->prodi->nama ?? '-' }}</td>
                                <td class="text-center">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($j->mataKuliah->sks ?? 0) >= 4 ? 'bg-purple-100 text-purple-700' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ $j->mataKuliah->sks ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ $j->mataKuliah->semester_ke ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-slate-600">
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="fas fa-user text-slate-400 text-xs"></i>
                                        {{ $j->dosen->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-slate-600">
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="fas fa-door-open text-slate-400 text-xs"></i>
                                        {{ $j->ruangan->kode ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            @php $rowNum++; @endphp
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-12 text-gray-400">
                                    <i class="fas fa-calendar-times text-4xl mb-3 block"></i>
                                    <p>Tidak ada data jadwal yang tersedia</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer --}}
        <footer class="text-center py-6 text-xs text-slate-400">
            &copy; {{ date('Y') }} Sistem Penjadwalan Mata Kuliah - ACO &mdash; Fakultas Teknik Universitas
            Wiraraja Sumenep Madura
        </footer>
    </div>

</body>

</html>
