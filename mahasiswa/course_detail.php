<?php
// File: mahasiswa/course_detail.php

$pageTitle = 'Detail Praktikum';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$user_id = $_SESSION['user_id'];

// Validasi ID praktikum dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my_courses.php");
    exit();
}
$praktikum_id = intval($_GET['id']);

// Keamanan: Cek apakah mahasiswa terdaftar di praktikum ini
$stmt_check = mysqli_prepare($conn, "SELECT id FROM pendaftaran WHERE user_id = ? AND praktikum_id = ?");
mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $praktikum_id);
mysqli_stmt_execute($stmt_check);
if (mysqli_stmt_get_result($stmt_check)->num_rows == 0) {
    header("Location: my_courses.php"); // Jika tidak terdaftar, tendang kembali
    exit();
}

// Proses upload laporan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_laporan'])) {
    $modul_id = intval($_POST['modul_id']);
    
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/laporan/';
        $file_name = $user_id . '_' . $modul_id . '_' . time() . '_' . basename($_FILES['file_laporan']['name']);
        $target_file = $upload_dir . $file_name;

        // Cek apakah sudah pernah upload, jika ya, ini jadi update (opsional, untuk sekarang kita anggap insert saja)
        if (move_uploaded_file($_FILES['file_laporan']['tmp_name'], $target_file)) {
            $stmt_insert = mysqli_prepare($conn, "INSERT INTO laporan (user_id, modul_id, file_laporan) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "iis", $user_id, $modul_id, $file_name);
            mysqli_stmt_execute($stmt_insert);
            header("Location: course_detail.php?id=$praktikum_id&upload=success");
            exit();
        }
    }
}


// Ambil detail mata praktikum
$stmt_course = mysqli_prepare($conn, "SELECT nama FROM mata_praktikum WHERE id = ?");
mysqli_stmt_bind_param($stmt_course, "i", $praktikum_id);
mysqli_stmt_execute($stmt_course);
$course = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_course));
$pageTitle = $course['nama']; // Update page title dengan nama praktikum

// Ambil semua modul, status laporan, dan nilai untuk praktikum ini
$sql = "SELECT 
            m.*, 
            l.id as laporan_id, l.file_laporan, l.waktu_kumpul,
            p.nilai, p.feedback
        FROM modul m
        LEFT JOIN laporan l ON m.id = l.modul_id AND l.user_id = ?
        LEFT JOIN penilaian p ON l.id = p.laporan_id
        WHERE m.praktikum_id = ?
        ORDER BY m.pertemuan_ke";
$stmt_modules = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt_modules, "ii", $user_id, $praktikum_id);
mysqli_stmt_execute($stmt_modules);
$modules = mysqli_stmt_get_result($stmt_modules);

?>

<div class="container mx-auto">
    <a href="my_courses.php" class="text-blue-600 hover:underline mb-4 inline-block">&larr; Kembali ke Praktikum Saya</a>
    <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($course['nama']); ?></h1>
    
    <div class="space-y-6">
        <?php while($modul = mysqli_fetch_assoc($modules)): ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800">Pertemuan <?php echo $modul['pertemuan_ke']; ?>: <?php echo htmlspecialchars($modul['judul']); ?></h3>
            <p class="text-gray-600 mt-1 mb-4"><?php echo htmlspecialchars($modul['deskripsi']); ?></p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <div>
                    <?php if(!empty($modul['file_materi'])): ?>
                    <a href="../uploads/materi/<?php echo htmlspecialchars($modul['file_materi']); ?>" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 mb-4">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Unduh Materi
                    </a>
                    <?php endif; ?>

                    <?php if($modul['laporan_id'] && $modul['nilai'] !== null): ?>
                    <div class="mt-4 border-t pt-4">
                        <h4 class="font-semibold text-gray-700">Penilaian Laporan Anda:</h4>
                        <p class="text-4xl font-bold text-green-600"><?php echo htmlspecialchars($modul['nilai']); ?></p>
                        <p class="font-semibold mt-2">Feedback dari Asisten:</p>
                        <blockquote class="border-l-4 border-gray-200 pl-4 italic text-gray-600">
                            <?php echo !empty($modul['feedback']) ? nl2br(htmlspecialchars($modul['feedback'])) : 'Tidak ada feedback.'; ?>
                        </blockquote>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-2">Laporan Praktikum</h4>
                    <?php if ($modul['laporan_id']): ?>
                        <div class="text-sm">
                            <p class="font-semibold text-green-700">Anda sudah mengumpulkan laporan.</p>
                            <p><strong>File:</strong> <a href="../uploads/laporan/<?php echo htmlspecialchars($modul['file_laporan']); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($modul['file_laporan']); ?></a></p>
                            <p><strong>Waktu:</strong> <?php echo date('d M Y, H:i', strtotime($modul['waktu_kumpul'])); ?></p>
                            <?php if ($modul['nilai'] === null): ?>
                            <p class="mt-2 text-yellow-800 bg-yellow-100 px-2 py-1 rounded-md inline-block">Status: Menunggu Penilaian</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="modul_id" value="<?php echo $modul['id']; ?>">
                            <input type="file" name="file_laporan" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-3">
                            <button type="submit" name="submit_laporan" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md">Kumpulkan Laporan</button>
                        </form>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>