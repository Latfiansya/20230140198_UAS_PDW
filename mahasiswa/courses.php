<?php
// File: mahasiswa/courses.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

$pageTitle = 'Cari Praktikum';
$activePage = 'courses';
$user_id = $_SESSION['user_id'];

// Ambil semua praktikum beserta info apakah user sudah mendaftar
$query = "
    SELECT mp.id, mp.nama, mp.deskripsi, mp.semester, mp.tahun_ajaran,
           (SELECT COUNT(*) FROM pendaftaran WHERE user_id = ? AND praktikum_id = mp.id) AS sudah_daftar
    FROM mata_praktikum mp
    ORDER BY mp.tahun_ajaran DESC, mp.semester DESC
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

require_once 'templates/header_mahasiswa.php';
?>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-6">Daftar Mata Praktikum</h1>

    <?php if (mysqli_num_rows($result) === 0): ?>
        <div class="bg-white p-6 rounded-lg shadow text-gray-600">
            Tidak ada mata praktikum tersedia saat ini.
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition p-6 flex flex-col justify-between min-h-[270px]">
                    <div>
                        <h2 class="text-xl font-bold text-emerald-700 mb-2">
                            <?= htmlspecialchars($row['nama']); ?>
                        </h2>
                        <p class="text-sm text-gray-600 mb-4">
                            <?= htmlspecialchars($row['deskripsi']); ?>
                        </p>
                    </div>

                    <div class="mt-auto pt-4 border-t">
                        <p class="text-sm text-gray-500 mb-1">
                            Semester: <span class="font-semibold"><?= htmlspecialchars($row['semester']); ?></span>
                        </p>
                        <p class="text-sm text-gray-500 mb-4">
                            Tahun Ajaran: <span class="font-semibold"><?= htmlspecialchars($row['tahun_ajaran']); ?></span>
                        </p>

                        <?php if ($row['sudah_daftar'] > 0): ?>
                            <button class="w-full bg-gray-300 text-gray-600 font-semibold text-sm px-4 py-2 rounded-lg cursor-not-allowed" disabled>
                                Sudah Terdaftar
                            </button>
                        <?php else: ?>
                            <form method="POST" action="daftar.php">
                                <input type="hidden" name="praktikum_id" value="<?= $row['id']; ?>">
                                <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-4 py-2 rounded-lg transition duration-200">
                                    Daftar Praktikum
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
