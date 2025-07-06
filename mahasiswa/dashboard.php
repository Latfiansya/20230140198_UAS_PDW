<?php
// File: mahasiswa/dashboard.php

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php'; // Panggil koneksi DB

$user_id = $_SESSION['user_id'];

// --- KUMPULAN QUERY UNTUK STATISTIK MAHASISWA ---

// 1. Menghitung jumlah praktikum yang diikuti
$query_praktikum = "SELECT COUNT(*) AS total FROM pendaftaran WHERE user_id = ?";
$stmt_praktikum = mysqli_prepare($conn, $query_praktikum);
mysqli_stmt_bind_param($stmt_praktikum, "i", $user_id);
mysqli_stmt_execute($stmt_praktikum);
$count_praktikum_diikuti = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_praktikum))['total'];

// 2. Menghitung jumlah tugas/laporan yang sudah dikumpulkan (Tugas Selesai)
$query_laporan = "SELECT COUNT(*) AS total FROM laporan WHERE user_id = ?";
$stmt_laporan = mysqli_prepare($conn, $query_laporan);
mysqli_stmt_bind_param($stmt_laporan, "i", $user_id);
mysqli_stmt_execute($stmt_laporan);
$count_tugas_selesai = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_laporan))['total'];

// 3. Menghitung total modul dari semua praktikum yang diikuti
$query_total_modul = "SELECT COUNT(m.id) AS total 
                      FROM modul m
                      JOIN pendaftaran p ON m.praktikum_id = p.praktikum_id
                      WHERE p.user_id = ?";
$stmt_total_modul = mysqli_prepare($conn, $query_total_modul);
mysqli_stmt_bind_param($stmt_total_modul, "i", $user_id);
mysqli_stmt_execute($stmt_total_modul);
$count_total_modul_diikuti = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_total_modul))['total'];

// 4. Menghitung Tugas Menunggu
$tugas_menunggu = $count_total_modul_diikuti - $count_tugas_selesai;
// Pastikan tidak negatif
$tugas_menunggu = max(0, $tugas_menunggu);

// 5. Mengambil notifikasi: nilai terbaru yang diberikan
$query_notif_nilai = "SELECT m.judul AS judul_modul FROM penilaian p
                        JOIN laporan l ON p.laporan_id = l.id
                        JOIN modul m ON l.modul_id = m.id
                        WHERE l.user_id = ? ORDER BY p.waktu_nilai DESC LIMIT 1";
$stmt_notif_nilai = mysqli_prepare($conn, $query_notif_nilai);
mysqli_stmt_bind_param($stmt_notif_nilai, "i", $user_id);
mysqli_stmt_execute($stmt_notif_nilai);
$notif_nilai = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_notif_nilai));
?>


<div class="space-y-8">
    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white p-8 rounded-2xl shadow-lg">
        <h1 class="text-4xl font-bold">Selamat Datang Kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
        <p class="mt-2 text-lg opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-200 flex items-center space-x-5 transition-transform transform hover:-translate-y-1.5 duration-300">
            <div class="bg-sky-100 text-sky-600 p-4 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-medium">Praktikum Diikuti</p>
                <p class="text-3xl font-bold text-slate-800"><?php echo $count_praktikum_diikuti; ?></p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-200 flex items-center space-x-5 transition-transform transform hover:-translate-y-1.5 duration-300">
            <div class="bg-green-100 text-green-600 p-4 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-medium">Tugas Selesai</p>
                <p class="text-3xl font-bold text-slate-800"><?php echo $count_tugas_selesai; ?></p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-200 flex items-center space-x-5 transition-transform transform hover:-translate-y-1.5 duration-300">
             <div class="bg-amber-100 text-amber-600 p-4 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-medium">Tugas Menunggu</p>
                <p class="text-3xl font-bold text-slate-800"><?php echo $tugas_menunggu; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-200">
        <h3 class="text-xl font-bold text-slate-800 mb-5">Notifikasi Terbaru</h3>
        <ul class="space-y-4">
            
            <?php if ($notif_nilai): ?>
            <li class="flex items-start space-x-4 p-4 rounded-lg hover:bg-slate-50 transition-colors">
                <div class="flex-shrink-0 bg-blue-100 text-blue-600 rounded-full h-10 w-10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <p class="text-slate-700">
                        Nilai untuk <strong class="font-semibold text-emerald-700"><?php echo htmlspecialchars($notif_nilai['judul_modul']); ?></strong> telah diberikan. Silakan cek di detail praktikum terkait.
                    </p>
                    <span class="text-xs text-slate-400">Baru saja</span>
                </div>
            </li>
            <?php endif; ?>

            <li class="flex items-start space-x-4 p-4 rounded-lg hover:bg-slate-50 transition-colors <?php if($notif_nilai) echo 'border-t border-slate-100'; ?>">
                <div class="flex-shrink-0 bg-green-100 text-green-600 rounded-full h-10 w-10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-slate-700">
                        Selamat datang di SIMPRAK. Anda bisa mulai mencari praktikum yang tersedia di menu <a href="courses.php" class="font-semibold text-emerald-600 hover:underline">Cari Praktikum</a>.
                    </p>
                    <span class="text-xs text-slate-400">1 hari yang lalu</span>
                </div>
            </li>
            
            <?php if (!$notif_nilai): ?>
            <li class="flex items-start space-x-4 p-4 rounded-lg hover:bg-slate-50 transition-colors border-t border-slate-100">
                <div class="flex-shrink-0 bg-slate-100 text-slate-500 rounded-full h-10 w-10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-slate-500">
                        Belum ada notifikasi baru untuk Anda.
                    </p>
                </div>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php
// Panggil Footer
require_once 'templates/footer_mahasiswa.php';
?>