<?php
// File: asisten/edit_modul.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// Validasi ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: modul.php");
    exit();
}
$id = intval($_GET['id']);

// Ambil data modul yang akan diedit
$stmt = mysqli_prepare($conn, "SELECT * FROM modul WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$modul = mysqli_fetch_assoc($result);

if (!$modul) {
    header("Location: modul.php");
    exit();
}

// Ambil data mata praktikum untuk dropdown
$praktikum_list = mysqli_query($conn, "SELECT id, nama FROM mata_praktikum ORDER BY nama");

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $praktikum_id = trim($_POST['praktikum_id']);
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $pertemuan_ke = trim($_POST['pertemuan_ke']);
    $file_materi_path = $modul['file_materi']; // Default ke file lama

    if (empty($praktikum_id) || empty($judul) || empty($pertemuan_ke)) {
        $errors[] = "Semua field wajib diisi.";
    }

    // Handle file upload jika ada file baru
    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/materi/';
        
        // Hapus file lama jika ada
        if (!empty($modul['file_materi']) && file_exists($upload_dir . $modul['file_materi'])) {
            unlink($upload_dir . $modul['file_materi']);
        }

        $file_name = time() . '_' . basename($_FILES['file_materi']['name']);
        $target_file = $upload_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (!in_array($file_type, ['pdf', 'doc', 'docx', 'ppt', 'pptx'])) {
            $errors[] = "Hanya file PDF, DOC, DOCX, PPT, PPTX yang diizinkan.";
        } else {
            if (move_uploaded_file($_FILES['file_materi']['tmp_name'], $target_file)) {
                $file_materi_path = $file_name;
            } else {
                $errors[] = "Gagal mengunggah file materi baru.";
            }
        }
    }

    if (count($errors) === 0) {
        $stmt_update = mysqli_prepare($conn, "UPDATE modul SET praktikum_id=?, judul=?, deskripsi=?, pertemuan_ke=?, file_materi=? WHERE id=?");
        mysqli_stmt_bind_param($stmt_update, "issisi", $praktikum_id, $judul, $deskripsi, $pertemuan_ke, $file_materi_path, $id);

        if (mysqli_stmt_execute($stmt_update)) {
            $success_message = "Data modul berhasil diperbarui!";
            // Refresh data modul setelah update
            $modul['praktikum_id'] = $praktikum_id;
            $modul['judul'] = $judul;
            $modul['deskripsi'] = $deskripsi;
            $modul['pertemuan_ke'] = $pertemuan_ke;
            $modul['file_materi'] = $file_materi_path;
        } else {
            $errors[] = "Gagal memperbarui data.";
        }
    }
}

$pageTitle = 'Edit Modul';
$activePage = 'modul';
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-emerald-700 mb-6">Edit Modul Praktikum</h2>

    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <p class="font-bold">Berhasil!</p>
            <p><?php echo $success_message; ?> <a href="modul.php" class="underline font-semibold">Kembali ke daftar modul</a>.</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <p class="font-bold">Terjadi Kesalahan</p>
            <ul class="list-disc pl-5 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-5">
        <div>
            <label for="praktikum_id" class="block text-gray-700 font-medium mb-1">Mata Praktikum</label>
            <select id="praktikum_id" name="praktikum_id"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-400 focus:border-emerald-400 focus:outline-none"
                    required>
                <?php mysqli_data_seek($praktikum_list, 0); ?>
                <?php while($prak = mysqli_fetch_assoc($praktikum_list)): ?>
                    <option value="<?php echo $prak['id']; ?>" <?php echo ($prak['id'] == $modul['praktikum_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($prak['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div>
            <label for="judul" class="block text-gray-700 font-medium mb-1">Judul Modul</label>
            <input type="text" name="judul" id="judul"
                    value="<?php echo htmlspecialchars($modul['judul']); ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-400 focus:border-emerald-400 focus:outline-none"
                    required>
        </div>

        <div>
            <label for="pertemuan_ke" class="block text-gray-700 font-medium mb-1">Pertemuan Ke-</label>
            <input type="number" name="pertemuan_ke" id="pertemuan_ke"
                    value="<?php echo htmlspecialchars($modul['pertemuan_ke']); ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-400 focus:border-emerald-400 focus:outline-none"
                    required>
        </div>

        <div>
            <label for="deskripsi" class="block text-gray-700 font-medium mb-1">Deskripsi (Opsional)</label>
            <textarea name="deskripsi" id="deskripsi" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-400 focus:border-emerald-400 focus:outline-none"><?php echo htmlspecialchars($modul['deskripsi']); ?></textarea>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-1">File Materi Saat Ini</label>
            <?php if (!empty($modul['file_materi'])): ?>
                <a href="../uploads/materi/<?php echo htmlspecialchars($modul['file_materi']); ?>" target="_blank"
                    class="text-emerald-700 font-medium hover:underline"><?php echo htmlspecialchars($modul['file_materi']); ?></a>
            <?php else: ?>
                <p class="text-gray-500 italic">Tidak ada file materi.</p>
            <?php endif; ?>
        </div>

        <div>
            <label for="file_materi" class="block text-gray-700 font-medium mb-1">Unggah File Baru (opsional)</label>
            <input type="file" name="file_materi" id="file_materi"
                    class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full
                            file:border-0 file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" />
        </div>

        <div class="flex justify-end pt-4 space-x-2">
            <a href="modul.php"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded-md">
                Batal
            </a>
            <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-md">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>


<?php require_once 'templates/footer.php'; ?>