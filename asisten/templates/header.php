<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek jika pengguna belum login atau bukan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Asisten - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

<div class="flex h-screen">
    <!-- SIDEBAR -->
    <aside class="w-64 bg-gradient-to-t from-emerald-500 via-emerald-600 to-emerald-600 text-white flex flex-col shadow-md">
        <div class="p-6 text-center border-b border-emerald-700">
            <h3 class="text-xl font-bold tracking-wide">Panel Asisten</h3>
            <p class="text-sm text-emerald-100 mt-1"><?php echo htmlspecialchars($_SESSION['nama']); ?></p>
        </div>
        <nav class="flex-grow">
            <ul class="space-y-2 p-4 text-sm font-medium">
                <?php 
                    $activeClass = 'bg-emerald-700 text-white font-semibold';
                    $inactiveClass = 'text-emerald-100 hover:bg-emerald-600 hover:text-white transition-colors';
                ?>
                <li>
                    <a href="dashboard.php" class="<?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2 rounded-md">
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 3.75A.75.75 0 013.75 3h16.5a.75.75 0 01.75.75V9a.75.75 0 01-.75.75H3.75A.75.75 0 013 9V3.75zM3.75 10.5h16.5M9.75 15.75h4.5" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="modul.php" class="<?php echo ($activePage == 'modul') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2 rounded-md">
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6v12m6-6H6" />
                        </svg>
                        <span>Manajemen Modul</span>
                    </a>
                </li>
                <li>
                    <a href="laporan.php" class="<?php echo ($activePage == 'laporan') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2 rounded-md">
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8.25 6.75h7.5m-7.5 4.5h7.5m-7.5 4.5h4.5M4.5 6.75h.007v.008H4.5V6.75zm0 4.5h.007v.008H4.5v-.008zm0 4.5h.007v.008H4.5v-.008z" />
                        </svg>
                        <span>Laporan Masuk</span>
                    </a>
                </li>
                <li>
                    <a href="akun.php" class="<?php echo ($activePage == 'akun') ? $activeClass : $inactiveClass; ?> flex items-center px-4 py-2 rounded-md">
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 12a3 3 0 100-6 3 3 0 000 6zm0 0v7.5m0 0h-6m6 0h6" />
                        </svg>
                        <span>Manajemen Akun</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-6 lg:p-10">
        <header class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
            <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                Logout
            </a>
        </header>
