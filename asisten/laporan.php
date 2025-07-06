<?php
// File: asisten/laporan.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// Ambil data untuk filter dropdowns
$praktikum_list = mysqli_query($conn, "SELECT id, nama FROM mata_praktikum ORDER BY nama");
$modul_list = mysqli_query($conn, "SELECT id, judul FROM modul ORDER BY judul");
$mahasiswa_list = mysqli_query($conn, "SELECT id, nama FROM users WHERE role = 'mahasiswa' ORDER BY nama");

// Persiapan Query Dasar
$sql = "SELECT 
            l.id, 
            l.waktu_kumpul,
            u.nama AS nama_mahasiswa,
            mp.nama AS nama_praktikum,
            m.judul AS judul_modul,
            p.id AS id_penilaian
        FROM laporan l
        JOIN users u ON l.user_id = u.id
        JOIN modul m ON l.modul_id = m.id
        JOIN mata_praktikum mp ON m.praktikum_id = mp.id
        LEFT JOIN penilaian p ON l.id = p.laporan_id";

// Filtering
$where_clauses = [];
$params = [];
$types = '';

if (!empty($_GET['praktikum_id'])) {
    $where_clauses[] = "mp.id = ?";
    $params[] = $_GET['praktikum_id'];
    $types .= 'i';
}
if (!empty($_GET['modul_id'])) {
    $where_clauses[] = "m.id = ?";
    $params[] = $_GET['modul_id'];
    $types .= 'i';
}
if (!empty($_GET['mahasiswa_id'])) {
    $where_clauses[] = "u.id = ?";
    $params[] = $_GET['mahasiswa_id'];
    $types .= 'i';
}
if (!empty($_GET['status'])) {
    if ($_GET['status'] == 'dinilai') {
        $where_clauses[] = "p.id IS NOT NULL";
    } elseif ($_GET['status'] == 'belum_dinilai') {
        $where_clauses[] = "p.id IS NULL";
    }
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY l.waktu_kumpul DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$pageTitle = 'Laporan Masuk';
$activePage = 'laporan';
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Laporan Masuk dari Mahasiswa</h2>

    <form method="GET" class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
        <div>
            <label for="praktikum_id" class="block text-sm font-medium text-gray-700">Praktikum</label>
            <select name="praktikum_id" id="praktikum_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                <option value="">Semua</option>
                <?php while ($prak = mysqli_fetch_assoc($praktikum_list)): ?>
                    <option value="<?php echo $prak['id']; ?>" <?php echo (isset($_GET['praktikum_id']) && $_GET['praktikum_id'] == $prak['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($prak['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label for="modul_id" class="block text-sm font-medium text-gray-700">Modul</label>
            <select name="modul_id" id="modul_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                <option value="">Semua</option>
                 <?php while ($mod = mysqli_fetch_assoc($modul_list)): ?>
                    <option value="<?php echo $mod['id']; ?>" <?php echo (isset($_GET['modul_id']) && $_GET['modul_id'] == $mod['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($mod['judul']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label for="mahasiswa_id" class="block text-sm font-medium text-gray-700">Mahasiswa</label>
            <select name="mahasiswa_id" id="mahasiswa_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                <option value="">Semua</option>
                <?php while ($mhs = mysqli_fetch_assoc($mahasiswa_list)): ?>
                    <option value="<?php echo $mhs['id']; ?>" <?php echo (isset($_GET['mahasiswa_id']) && $_GET['mahasiswa_id'] == $mhs['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($mhs['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                <option value="">Semua</option>
                <option value="dinilai" <?php echo (isset($_GET['status']) && $_GET['status'] == 'dinilai') ? 'selected' : ''; ?>>Sudah Dinilai</option>
                <option value="belum_dinilai" <?php echo (isset($_GET['status']) && $_GET['status'] == 'belum_dinilai') ? 'selected' : ''; ?>>Belum Dinilai</option>
            </select>
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">Filter</button>
            <a href="laporan.php" class="w-full text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md">Reset</a>
        </div>
    </form>
    
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-700 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Mahasiswa</th>
                    <th class="py-3 px-4 text-left">Praktikum & Modul</th>
                    <th class="py-3 px-4 text-left">Waktu Kumpul</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-semibold"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($row['nama_praktikum']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['judul_modul']); ?></div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600"><?php echo date('d M Y, H:i', strtotime($row['waktu_kumpul'])); ?></td>
                            <td class="py-3 px-4 text-center">
                                <?php if ($row['id_penilaian']): ?>
                                    <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">Sudah Dinilai</span>
                                <?php else: ?>
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">Belum Dinilai</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="detail_laporan.php?id=<?php echo $row['id']; ?>" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium">Detail & Nilai</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="py-6 px-4 text-center text-gray-500">Tidak ada laporan yang sesuai dengan filter.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>