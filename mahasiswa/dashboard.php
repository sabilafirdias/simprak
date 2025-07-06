<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    die("Akses ditolak.");
}

$id_mahasiswa = $_SESSION['user_id'];

// Jumlah praktikum yang diikuti
$stmt = $pdo->prepare("SELECT COUNT(*) FROM peserta_praktikum WHERE id_mahasiswa = ?");
$stmt->execute([$id_mahasiswa]);
$praktikum_diikuti = $stmt->fetchColumn();

// Total tugas (modul)
$stmt = $pdo->prepare("
    SELECT COUNT(m.id)
    FROM modul m
    JOIN peserta_praktikum pp ON m.id_praktikum = pp.id_praktikum
    WHERE pp.id_mahasiswa = ?
");
$stmt->execute([$id_mahasiswa]);
$total_tugas = $stmt->fetchColumn();

// Tugas selesai (sudah ada laporan)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM laporan WHERE id_user = ?");
$stmt->execute([$id_mahasiswa]);
$tugas_selesai = $stmt->fetchColumn();

// Tugas menunggu = total - selesai
$tugas_menunggu = max(0, $total_tugas - $tugas_selesai);

// Notifikasi terbaru dari asisten
$stmt = $pdo->prepare("
    SELECT m.judul, m.file_materi, m.created_at, p.nama_praktikum, u.nama AS nama_asisten
    FROM modul m
    JOIN praktikum p ON m.id_praktikum = p.id
    JOIN users u ON m.id_asisten = u.id
    JOIN peserta_praktikum pp ON m.id_praktikum = pp.id_praktikum
    WHERE pp.id_mahasiswa = ?
    ORDER BY m.created_at DESC
    LIMIT 5
");
$stmt->execute([$id_mahasiswa]);
$notifikasi = $stmt->fetchAll();
?>

<div class="bg-gradient-to-r from-cyan-500 to-yellow-400 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama']) ?>!</h1>
    <p class="mt-2 opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-600"><?= $praktikum_diikuti ?></div>
        <div class="mt-2 text-lg text-gray-600">Praktikum Diikuti</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-green-500"><?= $tugas_selesai ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Selesai</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-yellow-500"><?= $tugas_menunggu ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Menunggu</div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Notifikasi Terbaru</h3>
    <ul class="space-y-4">
        <?php if (count($notifikasi) > 0): ?>
            <?php foreach ($notifikasi as $notif): ?>
                <li class="border-b pb-2">
                    <p class="text-gray-800">
                        Asisten <strong><?= htmlspecialchars($notif['nama_asisten']) ?></strong> mengunggah modul
                        <strong><?= htmlspecialchars($notif['judul']) ?></strong> untuk praktikum
                        <em><?= htmlspecialchars($notif['nama_praktikum']) ?></em>.
                    </p>
                    <p class="text-sm text-gray-500"><?= date('d M Y H:i', strtotime($notif['created_at'])) ?></p>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="text-gray-500">Belum ada notifikasi.</li>
        <?php endif; ?>
    </ul>
</div>
