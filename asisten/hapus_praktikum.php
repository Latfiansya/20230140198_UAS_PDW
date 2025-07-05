<?php
// File: asisten/hapus_praktikum.php

session_start();

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// Validasi input ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: praktikum.php");
    exit();
}

$id = intval($_GET['id']);

// Eksekusi penghapusan
$stmt = mysqli_prepare($conn, "DELETE FROM mata_praktikum WHERE id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Kembali ke halaman daftar
header("Location: praktikum.php");
exit();
