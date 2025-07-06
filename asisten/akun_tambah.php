<?php
// File: asisten/akun_tambah.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Validasi
    if (empty($nama) || empty($email) || empty($password) || empty($role)) {
        $errors[] = "Semua field wajib diisi.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }
    if (!in_array($role, ['mahasiswa', 'asisten'])) {
        $errors[] = "Role tidak valid.";
    }

    // Cek duplikasi email
    $stmt_check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    if (mysqli_num_rows($result_check) > 0) {
        $errors[] = "Email sudah terdaftar. Gunakan email lain.";
    }
    mysqli_stmt_close($stmt_check);

    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt_insert = mysqli_prepare($conn, "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_insert, "ssss", $nama, $email, $hashed_password, $role);

        if (mysqli_stmt_execute($stmt_insert)) {
            $success_message = "Akun baru berhasil dibuat!";
        } else {
            $errors[] = "Gagal membuat akun. Silakan coba lagi.";
        }
        mysqli_stmt_close($stmt_insert);
    }
}

$pageTitle = 'Tambah Akun';
$activePage = 'akun';
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Formulir Tambah Akun</h2>

    <?php if ($success_message): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p class="font-bold">Berhasil!</p>
            <p><?php echo $success_message; ?> <a href="akun.php" class="underline">Kembali ke daftar akun</a>.</p>
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

    <form method="POST" class="space-y-6">
        <div>
            <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
        </div>
        <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select id="role" name="role" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required>
                <option value="">-- Pilih Role --</option>
                <option value="mahasiswa">Mahasiswa</option>
                <option value="asisten">Asisten</option>
            </select>
        </div>
        <div class="flex justify-end pt-4">
            <a href="akun.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-md mr-2">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">Simpan Akun</button>
        </div>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>