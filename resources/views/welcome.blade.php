<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistem Penjadwalan Mata Kuliah - ACO</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background:
                linear-gradient(135deg,
                    #eff6ff 0%,
                    #dbeafe 35%,
                    #bfdbfe 100%);
            min-height: 100vh;
            overflow-x: hidden;
            color: #1e293b;
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

            /* garis bawah biru */
            border-bottom: 2px solid rgba(37, 99, 235, 0.15);

            /* shadow biru halus */
            box-shadow:
                0 4px 20px rgba(37, 99, 235, 0.06);
        }

        .card-hover {
            transition: all 0.35s ease;
        }

        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.12);
        }

        .btn-primary {
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .hero-title {
            line-height: 1.2;
            letter-spacing: -1px;
            color: #1e3a8a;
        }

        .hero-title span {
            background: linear-gradient(to right, #2563eb, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .floating {
            animation: floating 5s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .section-title {
            font-weight: 800;
            letter-spacing: -1px;
            color: #1e3a8a;
        }

        .text-soft {
            color: #475569;
        }

        .glow {
            box-shadow:
                0 10px 25px rgba(37, 99, 235, 0.18);
        }

        /* mobile touch improvements */
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
    </style>
</head>

<body class="antialiased">

    <div class="min-h-screen">

        <!-- Navbar -->
        <nav class="nav-blur fixed w-full z-50">

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="flex justify-between items-center h-16 md:h-20">

                    <!-- Logo -->
                    <div class="flex items-center gap-4">

                        <img src="{{ asset('image/logo.png') }}" alt="Logo" class="w-14 h-14 object-contain">

                        <div>

                            <h1 class="font-bold text-lg md:text-xl leading-tight text-slate-800">
                                Sistem Penjadwalan
                            </h1>

                            <p class="text-slate-500 text-sm hidden md:block">
                                Fakultas Teknik Universitas Wiraraja
                            </p>

                        </div>

                    </div>

                    <!-- Menu -->
                    <div class="flex items-center gap-2 md:gap-4">

                        <a href="{{ route('login') }}"
                            class="px-3 md:px-5 py-1.5 md:py-2 text-slate-700 hover:bg-blue-50 rounded-xl transition flex items-center gap-1.5 md:gap-2 text-sm md:text-base">
                            <i class="fas fa-sign-in-alt text-blue-500"></i>
                            <span class="hidden md:inline">Login</span>
                        </a>

                        <a href="{{ route('jadwalglobal.list') }}"
                            class="px-3 md:px-5 py-1.5 md:py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition flex items-center gap-1.5 md:gap-2 font-semibold glow text-sm md:text-base">
                            <i class="fas fa-calendar"></i>
                            <span class="hidden md:inline">Jadwal</span>
                        </a>

                    </div>

                </div>

            </div>

        </nav>

        <!-- Hero -->
        <section class="relative flex items-center justify-center md:min-h-screen px-4 md:px-10 pt-24 md:pt-28 mb-4 md:mb-12">

            <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-4 md:gap-16 items-center">

                <!-- Left -->
                <div class="order-2 md:order-1">

                    <h1 class="text-[1.5rem] md:text-5xl font-black hero-title mb-2 md:mb-6">
                        Fakultas Teknik
                        <span>Universitas Wiraraja</span>
                        Sumenep Madura
                    </h1>

                    <!-- Deskripsi + Statistik -->
                    <p class="text-xs md:text-xl leading-relaxed max-w-2xl font-light mb-3 md:mb-8 text-soft">

                        <span class="font-semibold text-blue-700">
                            Sistem penjadwalan mata kuliah otomatis
                        </span>
                        untuk membantu proses penyusunan jadwal secara cepat,
                        efektif, dan meminimalkan bentrok jadwal dosen,
                        ruangan, maupun kelas.

                    </p>

                    <!-- Mobile stat badges -->
                    <div class="flex flex-wrap gap-1.5 md:gap-2 mb-3 md:mb-8">
                        <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-800 text-xs md:text-sm font-semibold px-3 py-1.5 rounded-full">
                            <i class="fas fa-book text-blue-500 text-[10px] md:text-xs"></i>
                            {{ number_format($totalMatkul, 0, ',', '.') }} Matkul
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-800 text-xs md:text-sm font-semibold px-3 py-1.5 rounded-full">
                            <i class="fas fa-chalkboard-teacher text-blue-500 text-[10px] md:text-xs"></i>
                            {{ number_format($totalDosen, 0, ',', '.') }} Dosen
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-800 text-xs md:text-sm font-semibold px-3 py-1.5 rounded-full">
                            <i class="fas fa-door-open text-blue-500 text-[10px] md:text-xs"></i>
                            {{ number_format($totalRuangan, 0, ',', '.') }} Ruangan
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-800 text-xs md:text-sm font-semibold px-3 py-1.5 rounded-full">
                            <i class="fas fa-users text-blue-500 text-[10px] md:text-xs"></i>
                            {{ number_format($totalKelas, 0, ',', '.') }} Kelas
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-800 text-xs md:text-sm font-semibold px-3 py-1.5 rounded-full">
                            <i class="fas fa-sun text-blue-500 text-[10px] md:text-xs"></i>
                            {{ number_format($matkulGanjil, 0, ',', '.') }} Ganjil
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-800 text-xs md:text-sm font-semibold px-3 py-1.5 rounded-full">
                            <i class="fas fa-moon text-blue-500 text-[10px] md:text-xs"></i>
                            {{ number_format($matkulGenap, 0, ',', '.') }} Genap
                        </span>
                    </div>
                    <!-- Button -->
                    <div class="flex flex-col md:flex-row gap-2 md:gap-4">

                        <a href="{{ route('login') }}"
                            class="w-full md:w-auto px-5 md:px-8 py-2.5 md:py-4 bg-white border border-blue-200 text-blue-700 font-semibold rounded-xl md:rounded-2xl shadow-sm hover:bg-blue-50 btn-primary flex items-center justify-center gap-2 text-xs md:text-base">

                            <i class="fas fa-sign-in-alt"></i>
                            Login Admin / Kaprodi

                        </a>

                        <a href="{{ route('jadwalglobal.list') }}"
                            class="w-full md:w-auto px-5 md:px-8 py-2.5 md:py-4 bg-blue-600 text-white font-semibold rounded-xl md:rounded-2xl shadow-lg hover:bg-blue-700 btn-primary flex items-center justify-center gap-2 glow text-xs md:text-base">

                            <i class="fas fa-calendar-week"></i>
                            Lihat Jadwal

                        </a>

                    </div>

                </div>

                <!-- Right -->
                <div class="relative floating order-1 md:order-2">

                    <div class="glass rounded-xl md:rounded-3xl p-0.5 md:p-1">

                        <img src="{{ asset('image/bg.jpg') }}" alt="Background Fakultas Teknik"
                            class="w-full h-[160px] md:h-[420px] object-cover rounded-xl md:rounded-3xl">

                    </div>

                </div>

            </div>

        </section>

        <!-- Keterangan Sistem -->
        <section class="pb-10 md:pb-24 px-4 md:px-10">

            <div class="max-w-6xl mx-auto">

                <!-- Heading -->
                <div class="text-center mb-6 md:mb-14">

                    <h2 class="text-xl md:text-5xl section-title mb-2 md:mb-4">
                        Keterangan Sistem
                    </h2>

                    <p class="text-soft text-xs md:text-lg max-w-3xl mx-auto leading-relaxed px-2 md:px-0">
                        Sistem Penjadwalan Mata Kuliah Otomatis menggunakan
                        metode Ant Colony Optimization (ACO) untuk membantu
                        proses penyusunan jadwal perkuliahan secara otomatis,
                        cepat, dan lebih optimal.
                    </p>

                </div>

                <!-- Card -->
                <div class="grid md:grid-cols-2 gap-3 md:gap-6">

                    <!-- Card 1 -->
                    <div class="glass rounded-xl md:rounded-3xl p-4 md:p-8 card-hover">

                        <div class="text-xl md:text-4xl mb-2 md:mb-4 text-blue-500">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>

                        <h3 class="text-sm md:text-xl font-bold mb-1 md:mb-3 text-slate-800">
                            Penanganan Bentrok Jadwal
                        </h3>

                        <p class="text-soft text-xs md:text-base leading-relaxed">
                            Sistem dapat menangani bentrok jadwal dosen,
                            ruangan, dan kelas sehingga jadwal perkuliahan
                            dapat tersusun lebih teratur dan efisien.
                        </p>

                    </div>

                    <!-- Card 2 -->
                    <div class="glass rounded-xl md:rounded-3xl p-4 md:p-8 card-hover">

                        <div class="text-xl md:text-4xl mb-2 md:mb-4 text-blue-500">
                            <i class="fas fa-sliders-h"></i>
                        </div>

                        <h3 class="text-sm md:text-xl font-bold mb-1 md:mb-3 text-slate-800">
                            Constraint / Batasan Sistem
                        </h3>

                        <p class="text-soft text-xs md:text-base leading-relaxed">
                            Sistem menerapkan constraint seperti
                            ketersediaan dosen, penggunaan ruangan tertentu,
                            dan penyesuaian slot jadwal perkuliahan.
                        </p>

                    </div>

                    <!-- Card 3 -->
                    <div class="glass rounded-xl md:rounded-3xl p-4 md:p-8 card-hover">

                        <div class="text-xl md:text-4xl mb-2 md:mb-4 text-blue-500">
                            <i class="fas fa-door-open"></i>
                        </div>

                        <h3 class="text-sm md:text-xl font-bold mb-1 md:mb-3 text-slate-800">
                            Penyesuaian Ruangan
                        </h3>

                        <p class="text-soft text-xs md:text-base leading-relaxed">
                            Sistem menyesuaikan kapasitas dan jenis ruangan
                            sesuai kebutuhan kelas dan mata kuliah agar
                            pembelajaran berjalan optimal.
                        </p>

                    </div>

                    <!-- Card 4 -->
                    <div class="glass rounded-xl md:rounded-3xl p-4 md:p-8 card-hover">

                        <div class="text-xl md:text-4xl mb-2 md:mb-4 text-blue-500">
                            <i class="fas fa-moon"></i>
                        </div>

                        <h3 class="text-sm md:text-xl font-bold mb-1 md:mb-3 text-slate-800">
                            Jadwal Ramadan
                        </h3>

                        <p class="text-soft text-xs md:text-base leading-relaxed">
                            Sistem mendukung penyesuaian jadwal Ramadan
                            dengan durasi perkuliahan 35 menit per SKS.
                        </p>

                    </div>

                </div>

            </div>

        </section>

        <!-- Footer -->
        <footer class="border-t border-blue-100 py-4 md:py-8 text-center">

            <p class="font-semibold text-slate-700">
                © {{ date('Y') }}
                Sistem Penjadwalan Mata Kuliah Otomatis
            </p>

            <p class="text-sm mt-2 text-slate-500">
                Fakultas Teknik Universitas Wiraraja Sumenep Madura
            </p>

        </footer>

    </div>

</body>

</html>
