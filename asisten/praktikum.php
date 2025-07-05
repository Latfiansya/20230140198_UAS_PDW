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
        <h2 class="text-2xl font-bold text-blue-700">Daftar Mata Praktikum</h2>
        <a href="tambah_praktikum.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-semibold">+ Tambah Praktikum</a>
    </div>

    <table class="min-w-full bg-white border border-gray-200">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="py-2 px-4 border-b">No</th>
                <th class="py-2 px-4 border-b">Nama</th>
                <th class="py-2 px-4 border-b">Deskripsi</th>
                <th class="py-2 px-4 border-b">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="border-b hover:bg-gray-100">
                    <td class="py-2 px-4 text-center"><?php echo $no++; ?></td>
                    <td class="py-2 px-4"><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td class="py-2 px-4"><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                    <td class="py-2 px-4 text-center space-x-2">
                        <a href="edit_praktikum.php?id=<?php echo $row['id']; ?>" class="text-white bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded">Edit</a>
                        <a href="hapus_praktikum.php?id=<?php echo $row['id']; ?>" class="text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'templates/footer.php'; ?>
