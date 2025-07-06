<?php
// File: mahasiswa/courses.php

$pageTitle = 'Cari Praktikum';
$activePage = 'courses';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$user_id = $_SESSION['user_id'];

// Ambil semua mata praktikum dan cek status pendaftaran user saat ini
$sql = "SELECT mp.*, p.id AS id_pendaftaran
        FROM mata_praktikum mp
        LEFT JOIN pendaftaran p ON mp.id = p.praktikum_id AND p.user_id = ?
        ORDER BY mp.nama";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Katalog Mata Praktikum</h1>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p class="font-bold">Berhasil!</p>
            <p>Anda telah terdaftar pada mata praktikum. Lihat di <a href="my_courses.php" class="underline font-semibold">Praktikum Saya</a>.</p>
        </div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] == 'exists'): ?>
         <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
            <p class="font-bold">Informasi</p>
            <p>Anda sudah terdaftar pada mata praktikum tersebut.</p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col">
                    <div class="p-6 flex-grow">
                        <h3 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($row['nama']); ?></h3>
                        <p class="mt-2 text-gray-600 text-sm">
                            <?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?>
                        </p>
                    </div>
                    <div class="bg-gray-50 p-4">
                        <?php if ($row['id_pendaftaran']): ?>
                            <button class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-md cursor-not-allowed" disabled>
                                Sudah Terdaftar
                            </button>
                        <?php else: ?>
                            <a href="daftar_praktikum.php?id=<?php echo $row['id']; ?>" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition-colors">
                                Daftar Sekarang
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500 col-span-3">Saat ini belum ada mata praktikum yang tersedia.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>