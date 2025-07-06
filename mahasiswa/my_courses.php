<?php
// File: mahasiswa/my_courses.php

$pageTitle = 'Praktikum Saya';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$user_id = $_SESSION['user_id'];

// Ambil data praktikum yang diikuti oleh user ini
$sql = "SELECT mp.id, mp.nama, mp.deskripsi
        FROM mata_praktikum mp
        JOIN pendaftaran p ON mp.id = p.praktikum_id
        WHERE p.user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Praktikum yang Saya Ikuti</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col transform hover:-translate-y-1 transition-transform duration-300">
                    <div class="p-6 flex-grow">
                        <h3 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($row['nama']); ?></h3>
                        <p class="mt-2 text-gray-600 text-sm">
                            <?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?>
                        </p>
                    </div>
                    <div class="bg-gray-50 p-4">
                        <a href="course_detail.php?id=<?php echo $row['id']; ?>" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition-colors">
                            Lihat Detail & Tugas
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-3 text-center py-12 bg-gray-50 rounded-lg">
                <p class="text-gray-500">Anda belum mendaftar pada mata praktikum manapun.</p>
                <a href="courses.php" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">
                    Cari Praktikum Sekarang
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>