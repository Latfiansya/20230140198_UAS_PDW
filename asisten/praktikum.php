<?php
// File: asisten/praktikum.php

// Mulai session dan cek login
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php'; // koneksi DB

// Ambil semua data mata praktikum
$query = "SELECT * FROM mata_praktikum";
$result = mysqli_query($conn, $query);

$pageTitle = 'Manajemen Praktikum';
$activePage = 'praktikum';
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Mata Praktikum</h2>
        <a href="tambah_praktikum.php" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md font-semibold">+ Tambah Praktikum</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-800 text-white text-sm uppercase">
                <tr>
                    <th class="py-3 px-4 text-left">No</th>
                    <th class="py-3 px-4 text-left">Nama</th>
                    <th class="py-3 px-4 text-left">Deskripsi</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4"><?php echo $no++; ?></td>
                        <td class="py-3 px-4 font-medium text-gray-900"><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                        <td class="py-3 px-4 text-center">
                            <div class="inline-flex gap-2">
                                <a href="edit_praktikum.php?id=<?php echo $row['id']; ?>"
                                class="bg-yellow-400 hover:bg-yellow-500 text-yellow-800 text-xs px-3 py-1 rounded-md font-semibold shadow-sm">
                                Edit
                                </a>
                                <a href="hapus_praktikum.php?id=<?php echo $row['id']; ?>"
                                class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded-md font-semibold shadow-sm"
                                onclick="return confirm('Yakin ingin menghapus?')">
                                Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
