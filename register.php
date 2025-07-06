<?php
require_once 'config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Validasi sederhana
    if (empty($nama) || empty($email) || empty($password) || empty($role)) {
        $message = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format email tidak valid!";
    } elseif (!in_array($role, ['mahasiswa', 'asisten'])) {
        $message = "Peran tidak valid!";
    } else {
        // Cek apakah email sudah terdaftar
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email sudah terdaftar. Silakan gunakan email lain.";
        } else {
            // Hash password untuk keamanan
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Simpan ke database
            $sql_insert = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $nama, $email, $hashed_password, $role);

            if ($stmt_insert->execute()) {
                header("Location: login.php?status=registered");
                exit();
            } else {
                $message = "Terjadi kesalahan. Silakan coba lagi.";
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Akun - SIMPRAK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Anda bisa menambahkan custom font di sini jika mau */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-100">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 to-teal-100 p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-emerald-600">SIMPRAK</h1>
                <p class="text-slate-500 mt-2">Sistem Informasi Manajemen Praktikum</p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-xl">
                <h2 class="text-2xl font-bold text-slate-800 text-center mb-1">Buat Akun Baru</h2>
                <p class="text-slate-500 text-center mb-6">Lengkapi data di bawah untuk memulai.</p>

                <form action="register.php" method="post" class="space-y-5">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" required class="block w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" required class="block w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" required class="block w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-700 mb-1">Daftar Sebagai</label>
                        <select id="role" name="role" required class="block w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="asisten">Asisten</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 mt-2">
                        Daftar
                    </button>
                    </form>
                
                <div class="text-center mt-6">
                    <p class="text-sm text-slate-600">
                        Sudah punya akun? <a href="login.php" class="font-medium text-emerald-600 hover:text-emerald-500 hover:underline">Login di sini</a>
                    </p>
                </div>
            </div>
             <p class="text-center text-xs text-slate-400 mt-8">© 2025 SIMPRAK. All rights reserved.</p>
        </div>
    </div>
</body>
</html>