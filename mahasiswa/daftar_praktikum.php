<?php
// File: mahasiswa/daftar_praktikum.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: courses.php");
    exit();
}

$praktikum_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Cek apakah sudah terdaftar untuk mencegah pendaftaran ganda
$stmt_check = mysqli_prepare($conn, "SELECT id FROM pendaftaran WHERE user_id = ? AND praktikum_id = ?");
mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $praktikum_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($result_check) > 0) {
    // Jika sudah terdaftar, redirect dengan status 'exists'
    header("Location: courses.php?status=exists");
    exit();
}

// Jika belum, lakukan pendaftaran
$stmt_insert = mysqli_prepare($conn, "INSERT INTO pendaftaran (user_id, praktikum_id) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt_insert, "ii", $user_id, $praktikum_id);

if (mysqli_stmt_execute($stmt_insert)) {
    // Redirect dengan status sukses
    header("Location: courses.php?status=success");
} else {
    // Handle error jika perlu
    header("Location: courses.php?status=error");
}

exit();
?>