<?php
// File: asisten/akun_edit.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: akun.php");
    exit();
}
$id = intval($_GET['id']);

// Ambil data user yang akan diedit
$stmt = mysqli_prepare($conn, "SELECT id, nama, email, role FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
if (!$user) {
    header("Location: akun.php");
    exit();
}

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $password = trim($_POST['password']);

    if (empty($nama) || empty($email) || empty($role)) {
        $errors[] = "Nama, Email, dan Role tidak boleh kosong.";
    }
    
    // Cek duplikasi email jika email diubah
    if ($email != $user['email']) {
        $stmt_check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        if (mysqli_stmt_get_result($stmt_check)->num_rows > 0) {
            $errors[] = "Email sudah digunakan oleh akun lain.";
        }
    }

    if (empty($errors)) {
        if (!empty($password)) {
            // Jika password diisi, update password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt_update = mysqli_prepare($conn, "UPDATE users SET nama=?, email=?, role=?, password=? WHERE id=?");
            mysqli_stmt_bind_param($stmt_update, "ssssi", $nama, $email, $role, $hashed_password, $id);
        } else {
            // Jika password kosong, jangan update password
            $stmt_update = mysqli_prepare($conn, "UPDATE users SET nama=?, email=?, role=? WHERE id=?");
            mysqli_stmt_bind_param($stmt_update, "sssi", $nama, $email, $role, $id);
        }

        if (mysqli_stmt_execute($stmt_update)) {
            $success_message = "Data akun berhasil diperbarui!";
            // Refresh data setelah update
            $user['nama'] = $nama;
            $user['email'] = $email;
            $user['role'] = $role;
        } else {
            $errors[] = "Gagal memperbarui data.";
        }
    }
}

$pageTitle = 'Edit Akun';
$activePage = 'akun';
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
    <h2 class="text-2xl font-bold text-emerald-700 mb-6">Formulir Edit Akun</h2>

    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <p class="font-semibold">Berhasil!</p>
            <p><?php echo $success_message; ?> <a href="akun.php" class="underline">Kembali ke daftar akun</a>.</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <p class="font-semibold">Terjadi Kesalahan:</p>
            <ul class="list-disc pl-5 mt-2 text-sm">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
        <div>
            <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input type="text" name="nama" id="nama"
                    value="<?php echo htmlspecialchars($user['nama']); ?>"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                    required>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email"
                    value="<?php echo htmlspecialchars($user['email']); ?>"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                    required>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
            <input type="password" name="password" id="password"
                    placeholder="Kosongkan jika tidak ingin diubah"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
        </div>

        <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select id="role" name="role"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                    required>
                <option value="mahasiswa" <?php echo ($user['role'] == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                <option value="asisten" <?php echo ($user['role'] == 'asisten') ? 'selected' : ''; ?>>Asisten</option>
            </select>
        </div>

        <div class="flex justify-end pt-4 space-x-2">
            <a href="akun.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-4 py-2 rounded-md">Batal</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-md">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>


<?php require_once 'templates/footer.php'; ?>