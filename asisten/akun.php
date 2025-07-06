<?php
// File: asisten/akun.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// Ambil semua data pengguna
$result = mysqli_query($conn, "SELECT id, nama, email, role FROM users ORDER BY nama");

$pageTitle = 'Manajemen Akun Pengguna';
$activePage = 'akun';
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Akun Pengguna</h2>
        <a href="akun_tambah.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-semibold flex items-center">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Akun
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-700 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Nama Lengkap</th>
                    <th class="py-3 px-4 text-left">Email</th>
                    <th class="py-3 px-4 text-center">Role</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-semibold"><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-1 font-semibold leading-tight rounded-full <?php echo $row['role'] == 'asisten' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700'; ?>">
                                    <?php echo ucfirst($row['role']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center space-x-2">
                                <a href="akun_edit.php?id=<?php echo $row['id']; ?>" class="text-white bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded-md text-sm font-medium">Edit</a>
                                <?php if ($row['id'] != $_SESSION['user_id']): // Mencegah asisten menghapus akunnya sendiri ?>
                                    <a href="akun_hapus.php?id=<?php echo $row['id']; ?>" class="text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded-md text-sm font-medium" onclick="return confirm('Yakin ingin menghapus akun ini? Semua data terkait (laporan, nilai) akan ikut terhapus.')">Hapus</a>
                                <?php else: ?>
                                    <button class="text-white bg-gray-400 px-3 py-1 rounded-md text-sm font-medium cursor-not-allowed" disabled>Hapus</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">Tidak ada akun pengguna.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>