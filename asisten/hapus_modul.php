<?php
// File: asisten/hapus_modul.php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// Validasi input ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: modul.php");
    exit();
}
$id = intval($_GET['id']);

// 1. Ambil nama file sebelum menghapus data dari DB
$stmt_select = mysqli_prepare($conn, "SELECT file_materi FROM modul WHERE id = ?");
mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$modul = mysqli_fetch_assoc($result);
$file_to_delete = $modul['file_materi'] ?? null;
mysqli_stmt_close($stmt_select);

// 2. Hapus data dari database
$stmt_delete = mysqli_prepare($conn, "DELETE FROM modul WHERE id = ?");
if ($stmt_delete) {
    mysqli_stmt_bind_param($stmt_delete, "i", $id);
    if (mysqli_stmt_execute($stmt_delete)) {
        // 3. Jika berhasil, hapus file fisik dari server
        if (!empty($file_to_delete)) {
            $file_path = '../uploads/materi/' . $file_to_delete;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }
    mysqli_stmt_close($stmt_delete);
}

// 4. Kembali ke halaman daftar modul
header("Location: modul.php");
exit();
?>