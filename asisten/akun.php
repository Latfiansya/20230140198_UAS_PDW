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
        <a href="akun_tambah.php" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md font-semibold flex items-center">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Akun
        </a>
    </div>

    <div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-800 text-white text-sm uppercase">
            <tr>
                <th class="py-3 px-4 text-left">Nama Lengkap</th>
                <th class="py-3 px-4 text-left">Email</th>
                <th class="py-3 px-4 text-center">Role</th>
                <th class="py-3 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-700 text-sm">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium text-gray-900"><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold shadow-sm
                                <?php echo $row['role'] == 'asisten' 
                                    ? 'bg-green-100 text-green-700' 
                                    : 'bg-purple-100 text-purple-700'; ?>">
                                <?php echo ucfirst($row['role']); ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="inline-flex gap-2 justify-center">
                                <a href="akun_edit.php?id=<?php echo $row['id']; ?>"
                                    class="bg-yellow-400 hover:bg-yellow-500 text-yellow-800 text-xs px-3 py-1 rounded-md font-semibold shadow-sm">
                                    Edit
                                </a>
                                <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                    <a href="akun_hapus.php?id=<?php echo $row['id']; ?>"
                                        class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded-md font-semibold shadow-sm"
                                        onclick="return confirm('Yakin ingin menghapus akun ini? Semua data terkait (laporan, nilai) akan ikut terhapus.')">
                                        Hapus
                                    </a>
                                <?php else: ?>
                                    <button class="bg-gray-400 text-white text-xs px-3 py-1 rounded-md font-semibold shadow-sm cursor-not-allowed" disabled>
                                        Hapus
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="py-5 px-4 text-center text-gray-500 italic">Tidak ada akun pengguna.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>