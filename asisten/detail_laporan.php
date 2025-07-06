<?php
// File: asisten/detail_laporan.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: laporan.php");
    exit();
}
$laporan_id = intval($_GET['id']);

// Proses Simpan/Update Nilai
$errors = [];
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = trim($_POST['nilai']);
    $feedback = trim($_POST['feedback']);

    if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
        $errors[] = "Nilai harus berupa angka antara 0 dan 100.";
    }

    if (empty($errors)) {
        // Cek apakah sudah ada penilaian
        $stmt_check = mysqli_prepare($conn, "SELECT id FROM penilaian WHERE laporan_id = ?");
        mysqli_stmt_bind_param($stmt_check, "i", $laporan_id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) > 0) {
            // Update
            $stmt_update = mysqli_prepare($conn, "UPDATE penilaian SET nilai = ?, feedback = ? WHERE laporan_id = ?");
            mysqli_stmt_bind_param($stmt_update, "dsi", $nilai, $feedback, $laporan_id);
            if (mysqli_stmt_execute($stmt_update)) {
                $success_message = "Nilai berhasil diperbarui!";
            }
        } else {
            // Insert
            $stmt_insert = mysqli_prepare($conn, "INSERT INTO penilaian (laporan_id, nilai, feedback) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "ids", $laporan_id, $nilai, $feedback);
            if (mysqli_stmt_execute($stmt_insert)) {
                $success_message = "Nilai berhasil disimpan!";
            }
        }
    }
}

// Ambil detail lengkap laporan
$sql = "SELECT 
            l.id, l.file_laporan, l.waktu_kumpul,
            u.nama AS nama_mahasiswa,
            mp.nama AS nama_praktikum,
            m.judul AS judul_modul,
            p.nilai, p.feedback
        FROM laporan l
        JOIN users u ON l.user_id = u.id
        JOIN modul m ON l.modul_id = m.id
        JOIN mata_praktikum mp ON m.praktikum_id = mp.id
        LEFT JOIN penilaian p ON l.id = p.laporan_id
        WHERE l.id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $laporan_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$laporan = mysqli_fetch_assoc($result);

if (!$laporan) {
    header("Location: laporan.php");
    exit();
}

$pageTitle = 'Detail Laporan';
$activePage = 'laporan';
require_once 'templates/header.php';
?>

<div class="max-w-4xl mx-auto">
    <?php if ($success_message): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p class="font-bold"><?php echo $success_message; ?></p>
        </div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <ul class="list-disc pl-5 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">Detail Laporan</h3>
            <div class="space-y-3 text-gray-700">
                <p><strong>Mahasiswa:</strong><br> <?php echo htmlspecialchars($laporan['nama_mahasiswa']); ?></p>
                <p><strong>Praktikum:</strong><br> <?php echo htmlspecialchars($laporan['nama_praktikum']); ?></p>
                <p><strong>Modul:</strong><br> <?php echo htmlspecialchars($laporan['judul_modul']); ?></p>
                <p><strong>Waktu Kumpul:</strong><br> <?php echo date('d F Y, H:i:s', strtotime($laporan['waktu_kumpul'])); ?></p>
            </div>
            <div class="mt-6">
                <a href="../uploads/laporan/<?php echo htmlspecialchars($laporan['file_laporan']); ?>" target="_blank" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Unduh File Laporan
                </a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">Form Penilaian</h3>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="nilai" class="block text-sm font-medium text-gray-700">Nilai (0-100)</label>
                    <input type="number" step="0.01" name="nilai" id="nilai" value="<?php echo htmlspecialchars($laporan['nilai'] ?? ''); ?>" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                </div>
                <div>
                    <label for="feedback" class="block text-sm font-medium text-gray-700">Feedback / Catatan</label>
                    <textarea name="feedback" id="feedback" rows="8" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"><?php echo htmlspecialchars($laporan['feedback'] ?? ''); ?></textarea>
                </div>
                <div class="flex justify-end pt-4">
                     <a href="laporan.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-md mr-2">Kembali</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>