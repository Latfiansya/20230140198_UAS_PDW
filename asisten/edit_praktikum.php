<?php
// File: asisten/edit_praktikum.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

$errors = [];
$success = false;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: praktikum.php");
    exit();
}

$id = intval($_GET['id']);

// Ambil data lama
$stmt = mysqli_prepare($conn, "SELECT * FROM mata_praktikum WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$praktikum = mysqli_fetch_assoc($result);

if (!$praktikum) {
    header("Location: praktikum.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($nama)) {
        $errors[] = "Nama praktikum tidak boleh kosong.";
    }

    if (count($errors) === 0) {
        $stmt = mysqli_prepare($conn, "UPDATE mata_praktikum SET nama = ?, deskripsi = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $nama, $deskripsi, $id);

        if (mysqli_stmt_execute($stmt)) {
            $success = true;
            // refresh data
            $praktikum['nama'] = $nama;
            $praktikum['deskripsi'] = $deskripsi;
        } else {
            $errors[] = "Gagal menyimpan perubahan.";
        }
    }
}

$pageTitle = 'Edit Praktikum';
$activePage = 'praktikum';
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md max-w-xl mx-auto">
    <h2 class="text-2xl font-bold text-blue-700 mb-4">Edit Mata Praktikum</h2>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Perubahan berhasil disimpan. <a href="praktikum.php" class="underline">Kembali ke daftar</a>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-gray-700 font-semibold">Nama Praktikum</label>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($praktikum['nama']); ?>" class="w-full mt-1 p-2 border rounded" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Deskripsi</label>
            <textarea name="deskripsi" class="w-full mt-1 p-2 border rounded" rows="4"><?php echo htmlspecialchars($praktikum['deskripsi']); ?></textarea>
        </div>

        <div class="flex justify-end">
            <a href="praktikum.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
        </div>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
