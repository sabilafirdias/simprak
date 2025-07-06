<?php
$pageTitle = 'Praktikum Saya';
$activePage = 'praktikum_saya';
require_once '../config.php';
require_once 'templates/header_mahasiswa.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    die("Akses ditolak. Silahkan login dulu.");
}

$id_mahasiswa = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT p.id, p.nama_praktikum, p.deskripsi
    FROM peserta_praktikum pp
    JOIN praktikum p ON pp.id_praktikum = p.id
    WHERE pp.id_mahasiswa = ?
");
$stmt->execute([$id_mahasiswa]);
$praktikum_saya = $stmt->fetchAll();
?>

<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Praktikum Saya</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (count($praktikum_saya) > 0): ?>
            <?php foreach ($praktikum_saya as $p): ?>
                <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">
                        <?= htmlspecialchars($p['nama_praktikum']) ?>
                    </h5>
                    <p class="mb-3 font-normal text-gray-700">
                        <?= htmlspecialchars($p['deskripsi']) ?>
                    </p>
                    <a href="detail_praktikum.php?id=<?= $p['id'] ?>" class="inline-block bg-yellow-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded-md font-semibold shadow transition">
                        Lihat
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-500 col-span-full">Belum ada praktikum yang diikuti.</p>
        <?php endif; ?>
    </div>
</div>
