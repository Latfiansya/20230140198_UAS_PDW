<?php
// File: asisten/akun_hapus.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: akun.php");
    exit();
}
$id = intval($_GET['id']);

// PENTING: Mencegah asisten menghapus akunnya sendiri
if ($id == $_SESSION['user_id']) {
    // Bisa ditambahkan pesan error di session jika mau, lalu redirect
    header("Location: akun.php");
    exit();
}

// Hapus data dari database
// Dengan ON DELETE CASCADE di schema, data di tabel pendaftaran, laporan, dan penilaian
// yang terkait dengan user ini akan otomatis terhapus.
$stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: akun.php");
exit();
?>