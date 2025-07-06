<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once '../config.php';
require_once 'templates/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    die('Akses ditolak.');
}

$id_asisten = $_SESSION['user_id'];

// Query data statistik
$totalModul = $pdo->prepare("SELECT COUNT(*) FROM modul WHERE id_asisten = ?");
$totalModul->execute([$id_asisten]);
$totalModul = $totalModul->fetchColumn();

$totalLaporan = $pdo->prepare("
    SELECT COUNT(*) 
    FROM laporan l 
    JOIN modul m ON l.id_modul = m.id 
    WHERE m.id_asisten = ?
");
$totalLaporan->execute([$id_asisten]);
$totalLaporan = $totalLaporan->fetchColumn();

$laporanBelumDinilai = $pdo->prepare("
    SELECT COUNT(*) 
    FROM laporan l 
    JOIN modul m ON l.id_modul = m.id 
    WHERE m.id_asisten = ? AND l.nilai IS NULL
");
$laporanBelumDinilai->execute([$id_asisten]);
$laporanBelumDinilai = $laporanBelumDinilai->fetchColumn();

// Aktivitas laporan terbaru
$aktivitas = $pdo->prepare("
    SELECT u.nama AS nama_mahasiswa, m.judul AS nama_modul, l.tanggal_upload
    FROM laporan l
    JOIN users u ON l.id_user = u.id
    JOIN modul m ON l.id_modul = m.id
    WHERE m.id_asisten = ? AND u.role = 'mahasiswa'
    ORDER BY l.tanggal_upload DESC
    LIMIT 5
");
$aktivitas->execute([$id_asisten]);
$aktivitasList = $aktivitas->fetchAll();
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-blue-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-blue-600" ...>...</svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Modul Diajarkan</p>
            <p class="text-2xl font-bold text-gray-800"><?= $totalModul ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-green-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-green-600" ...>...</svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Laporan Masuk</p>
            <p class="text-2xl font-bold text-gray-800"><?= $totalLaporan ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-yellow-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-yellow-600" ...>...</svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Laporan Belum Dinilai</p>
            <p class="text-2xl font-bold text-gray-800"><?= $laporanBelumDinilai ?></p>
        </div>
    </div>
</div>

<!-- Aktivitas Laporan -->
<div class="bg-white p-6 rounded-lg shadow-md mt-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Laporan Terbaru</h3>
    <div class="space-y-4">
        <?php foreach ($aktivitasList as $a): ?>
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                    <span class="font-bold text-gray-500"><?= strtoupper(substr($a['nama_mahasiswa'], 0, 2)) ?></span>
                </div>
                <div>
                    <p class="text-gray-800">
                        <strong><?= htmlspecialchars($a['nama_mahasiswa']) ?></strong> mengumpulkan laporan untuk <strong><?= htmlspecialchars($a['nama_modul']) ?></strong>
                    </p>
                    <p class="text-sm text-gray-500"><?= date('d M Y H:i', strtotime($a['tanggal_upload'])) ?></p>
                </div>
            </div>
        <?php endforeach ?>
        <?php if (count($aktivitasList) === 0): ?>
            <p class="text-gray-500">Belum ada aktivitas laporan terbaru.</p>
        <?php endif ?>
    </div>
</div>
