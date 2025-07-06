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


<div class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat Datang Kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p class="mt-2 opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-600"><?php echo $count_praktikum_diikuti; ?></div>
        <div class="mt-2 text-lg text-gray-600">Praktikum Diikuti</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-green-500"><?php echo $count_tugas_selesai; ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Selesai</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-yellow-500"><?php echo $tugas_menunggu; ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Menunggu</div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Notifikasi Terbaru</h3>
    <ul class="space-y-4">
        
        <?php if ($notif_nilai): ?>
        <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
            <span class="text-xl mr-4">🔔</span>
            <div>
                Nilai untuk <strong class="font-semibold text-blue-600"><?php echo htmlspecialchars($notif_nilai['judul_modul']); ?></strong> telah diberikan. Silakan cek di detail praktikum terkait.
            </div>
        </li>
        <?php endif; ?>

        <li class="flex items-start p-3">
            <span class="text-xl mr-4">✅</span>
            <div>
                Selamat datang di SIMPRAK. Anda bisa mulai mencari praktikum yang tersedia di menu <a href="courses.php" class="font-semibold text-blue-600 hover:underline">Cari Praktikum</a>.
            </div>
        </li>
        
        <?php if (!$notif_nilai): ?>
             <li class="flex items-start p-3 border-t border-gray-100">
                <span class="text-xl mr-4">💡</span>
                <div>
                    Belum ada notifikasi baru untuk Anda.
                </div>
            </li>
        <?php endif; ?>
    </ul>
</div>

<?php
// Panggil Footer
require_once 'templates/footer_mahasiswa.php';
?>