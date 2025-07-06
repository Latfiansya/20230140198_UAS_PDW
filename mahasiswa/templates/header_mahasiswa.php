<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek jika pengguna belum login, arahkan ke halaman login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// Mengambil nama pengguna dari sesi untuk ditampilkan
// Pastikan variabel $_SESSION['nama'] ada setelah user login
$nama_user = isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'Mahasiswa';
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>Panel Mahasiswa - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        html { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex flex-col min-h-screen">

    <header class="bg-gradient-to-r from-emerald-600 to-teal-500 shadow-lg sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <div class="flex items-center">
                    <a href="dashboard.php" class="flex-shrink-0 text-3xl font-extrabold text-white tracking-wide">
                        SIMPRAK
                    </a>
                </div>

                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-2">
                        <?php 
                            // Logika PHP Anda untuk menentukan link aktif tetap di sini
                            // Variabel $activePage harus di-set di setiap halaman (e.g., $activePage = 'dashboard';)
                            $halaman_sekarang = basename($_SERVER['PHP_SELF']);
                            
                            $linkDashboard = ($halaman_sekarang == 'dashboard.php') ? 'bg-emerald-700' : 'hover:bg-emerald-700';
                            $linkMyCourses = ($halaman_sekarang == 'my_courses.php') ? 'bg-emerald-700' : 'hover:bg-emerald-700';
                            $linkCourses = ($halaman_sekarang == 'courses.php') ? 'bg-emerald-700' : 'hover:bg-emerald-700';
                        ?>
                        <a href="dashboard.php" class="text-white <?php echo $linkDashboard; ?> px-4 py-2 rounded-lg text-base font-semibold transition-colors duration-200">Dashboard</a>
                        <a href="my_courses.php" class="text-white <?php echo $linkMyCourses; ?> px-4 py-2 rounded-lg text-base font-semibold transition-colors duration-200">Praktikum Saya</a>
                        <a href="courses.php" class="text-white <?php echo $linkCourses; ?> px-4 py-2 rounded-lg text-base font-semibold transition-colors duration-200">Cari Praktikum</a>
                    </div>
                </div>

                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center text-white hover:bg-emerald-600 px-3 py-2 rounded-lg transition-colors duration-200">
                                <span class="text-base font-medium mr-2">Halo, <?php echo $nama_user; ?></span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 transition-transform" :class="{'rotate-180': open}">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div 
                                x-show="open" 
                                @click.away="open = false" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" 
                                style="display: none;">
                                
                                <a href="../logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 w-full text-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L9 10.414V17a1 1 0 102 0v-6.586l2.293 2.293z" clip-rule="evenodd" />
                                    </svg>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </nav>
    </header>

    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">