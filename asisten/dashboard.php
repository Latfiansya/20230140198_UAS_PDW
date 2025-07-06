<?php
// File: asisten/dashboard.php

// 1. Definisi Variabel untuk Template
$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// 2. Panggil Header
require_once 'templates/header.php'; 
require_once '../config.php'; // Pastikan koneksi DB dipanggil

// --- KUMPULAN SEMUA QUERY UNTUK DASHBOARD ---

// Query untuk menghitung total modul
$query_total_modul = "SELECT COUNT(*) AS total FROM modul";
$result_total_modul = mysqli_query($conn, $query_total_modul);
$count_total_modul = mysqli_fetch_assoc($result_total_modul)['total'];

// Query untuk menghitung total laporan masuk
$query_total_laporan = "SELECT COUNT(*) AS total FROM laporan";
$result_total_laporan = mysqli_query($conn, $query_total_laporan);
$count_total_laporan = mysqli_fetch_assoc($result_total_laporan)['total'];

// Query untuk menghitung laporan yang belum dinilai
$query_belum_dinilai = "SELECT COUNT(*) AS total FROM laporan WHERE id NOT IN (SELECT laporan_id FROM penilaian)";
$result_belum_dinilai = mysqli_query($conn, $query_belum_dinilai);
$count_belum_dinilai = mysqli_fetch_assoc($result_belum_dinilai)['total'];

// Query untuk aktivitas laporan terbaru
$query_aktivitas = "SELECT u.nama AS nama_mahasiswa, m.judul AS judul_modul, l.waktu_kumpul 
                    FROM laporan l
                    JOIN users u ON l.user_id = u.id
                    JOIN modul m ON l.modul_id = m.id
                    ORDER BY l.waktu_kumpul DESC
                    LIMIT 5"; // Ambil 5 terbaru
$result_aktivitas = mysqli_query($conn, $query_aktivitas);

?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-blue-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Modul Diajarkan</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $count_total_modul; ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-green-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Laporan Masuk</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $count_total_laporan; ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-yellow-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Laporan Belum Dinilai</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $count_belum_dinilai; ?></p>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md mt-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Laporan Terbaru</h3>
    <div class="space-y-4">
        <?php if (mysqli_num_rows($result_aktivitas) > 0): ?>
            <?php while ($aktivitas = mysqli_fetch_assoc($result_aktivitas)): ?>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                        <span class="font-bold text-gray-500"><?php echo strtoupper(substr($aktivitas['nama_mahasiswa'], 0, 2)); ?></span>
                    </div>
                    <div>
                        <p class="text-gray-800"><strong><?php echo htmlspecialchars($aktivitas['nama_mahasiswa']); ?></strong> mengumpulkan laporan untuk <strong><?php echo htmlspecialchars($aktivitas['judul_modul']); ?></strong></p>
                        <p class="text-sm text-gray-500"><?php echo date('d M Y, H:i', strtotime($aktivitas['waktu_kumpul'])); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500">Belum ada aktivitas laporan.</p>
        <?php endif; ?>
    </div>
</div>

<?php
// 3. Panggil Footer
require_once 'templates/footer.php';
?>