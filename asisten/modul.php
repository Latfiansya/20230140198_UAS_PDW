<?php
// File: asisten/modul.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// Ambil semua data modul dengan nama mata praktikumnya
$query = "SELECT m.*, mp.nama AS nama_praktikum 
          FROM modul m
          JOIN mata_praktikum mp ON m.praktikum_id = mp.id
          ORDER BY mp.nama, m.pertemuan_ke";
$result = mysqli_query($conn, $query);

$pageTitle = 'Manajemen Modul';
$activePage = 'modul';
require_once 'templates/header.php';
?>


<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Modul Praktikum</h2>
        <a href="tambah_modul.php" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md font-semibold flex items-center shadow">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>  
            Tambah Modul
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-800 text-white text-sm uppercase">
                <tr>
                    <th class="py-3 px-4 text-left">No</th>
                    <th class="py-3 px-4 text-left">Judul Modul</th>
                    <th class="py-3 px-4 text-left">Mata Praktikum</th>
                    <th class="py-3 px-4 text-center">Pertemuan Ke-</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4"><?php echo $no++; ?></td>
                            <td class="py-3 px-4 font-medium text-gray-900"><?php echo htmlspecialchars($row['judul']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                            <td class="py-3 px-4 text-center"><?php echo htmlspecialchars($row['pertemuan_ke']); ?></td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="edit_modul.php?id=<?php echo $row['id']; ?>"
                                        class="bg-yellow-400 hover:bg-yellow-500 text-yellow-800 text-xs px-3 py-1 rounded-md font-semibold shadow-sm">
                                        Edit
                                    </a>
                                    <a href="hapus_modul.php?id=<?php echo $row['id']; ?>"
                                        class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded-md font-semibold shadow-sm"
                                        onclick="return confirm('Yakin ingin menghapus modul ini?')">
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="py-5 px-4 text-center text-gray-500 italic">Belum ada modul yang ditambahkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>



<?php require_once 'templates/footer.php'; ?>