<?php
// File: asisten/tambah_modul.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// Ambil data mata praktikum untuk dropdown
$praktikum_list = mysqli_query($conn, "SELECT id, nama FROM mata_praktikum ORDER BY nama");

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $praktikum_id = trim($_POST['praktikum_id']);
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $pertemuan_ke = trim($_POST['pertemuan_ke']);
    $file_materi_path = null;

    // Validasi dasar
    if (empty($praktikum_id) || empty($judul) || empty($pertemuan_ke)) {
        $errors[] = "Mata Praktikum, Judul, dan Pertemuan Ke tidak boleh kosong.";
    }

    // Handle file upload
    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/materi/';
        $file_name = time() . '_' . basename($_FILES['file_materi']['name']);
        $target_file = $upload_dir . $file_name;
        
        // Cek tipe file (opsional, tapi disarankan)
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (!in_array($file_type, ['pdf', 'doc', 'docx', 'ppt', 'pptx'])) {
            $errors[] = "Hanya file PDF, DOC, DOCX, PPT, PPTX yang diizinkan.";
        } else {
            if (move_uploaded_file($_FILES['file_materi']['tmp_name'], $target_file)) {
                $file_materi_path = $file_name; // Simpan nama filenya saja
            } else {
                $errors[] = "Gagal mengunggah file materi.";
            }
        }
    }

    if (count($errors) === 0) {
        $stmt = mysqli_prepare($conn, "INSERT INTO modul (praktikum_id, judul, deskripsi, pertemuan_ke, file_materi) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issis", $praktikum_id, $judul, $deskripsi, $pertemuan_ke, $file_materi_path);

        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Modul baru berhasil ditambahkan!";
        } else {
            $errors[] = "Gagal menyimpan data ke database.";
        }
    }
}

$pageTitle = 'Tambah Modul';
$activePage = 'modul';
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Formulir Tambah Modul</h2>

    <?php if ($success_message): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p class="font-bold">Berhasil!</p>
            <p><?php echo $success_message; ?> <a href="modul.php" class="underline">Kembali ke daftar modul</a>.</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <ul class="list-disc pl-5 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label for="praktikum_id" class="block text-sm font-medium text-gray-700">Mata Praktikum</label>
            <select id="praktikum_id" name="praktikum_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required>
                <option value="">-- Pilih Mata Praktikum --</option>
                <?php while($prak = mysqli_fetch_assoc($praktikum_list)): ?>
                    <option value="<?php echo $prak['id']; ?>"><?php echo htmlspecialchars($prak['nama']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div>
            <label for="judul" class="block text-sm font-medium text-gray-700">Judul Modul</label>
            <input type="text" name="judul" id="judul" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
        </div>

        <div>
            <label for="pertemuan_ke" class="block text-sm font-medium text-gray-700">Pertemuan Ke-</label>
            <input type="number" name="pertemuan_ke" id="pertemuan_ke" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
        </div>

        <div>
            <label for="file_materi" class="block text-sm font-medium text-gray-700">File Materi (PDF, DOCX, PPTX)</label>
            <input type="file" name="file_materi" id="file_materi" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <div class="flex justify-end pt-4">
            <a href="modul.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-md mr-2">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">Simpan Modul</button>
        </div>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>