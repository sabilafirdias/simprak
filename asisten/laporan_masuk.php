<?php
$pageTitle = 'Laporan Masuk';
$activePage = 'laporanmasuk';
require_once '../config.php';
require_once 'templates/header.php';

// Ambil nilai filter praktikum
$filter = isset($_GET['praktikum']) ? intval($_GET['praktikum']) : '';

// Query laporan + join modul, users, praktikum
$sql = "SELECT l.*, m.judul AS nama_modul, u.nama AS nama_mahasiswa, p.nama_praktikum 
        FROM laporan l
        JOIN modul m ON l.id_modul = m.id
        JOIN users u ON l.id_user = u.id
        JOIN praktikum p ON m.id_praktikum = p.id
        WHERE u.role = 'mahasiswa'";

if ($filter) {
    $sql .= " AND p.id = $filter";
}

$data = $pdo->query($sql)->fetchAll();

// Ambil daftar praktikum untuk filter
$praktikumList = $pdo->query("SELECT * FROM praktikum")->fetchAll();
?>

<div class="p-6">
    <!-- Filter -->
    <form method="GET" class="mb-4">
        <select name="praktikum" class="border p-2 rounded">
            <option value="">Semua Praktikum</option>
            <?php foreach ($praktikumList as $praktikum): ?>
                <option value="<?= $praktikum['id'] ?>" <?= $filter == $praktikum['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($praktikum['nama_praktikum']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Filter</button>
    </form>

    <!-- Tabel -->
    <table class="table-auto w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Praktikum</th>
                <th class="border px-2 py-1">Modul</th>
                <th class="border px-2 py-1">Mahasiswa</th>
                <th class="border px-2 py-1">File</th>
                <th class="border px-2 py-1">Nilai</th>
                <th class="border px-2 py-1">Feedback</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <?php
                    $fileName = $row['file_laporan'];
                    $filePath = __DIR__ . "/../uploads/laporan/" . $fileName;
                    $fileUrl = "../uploads/laporan/" . urlencode($fileName);
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    // Ekstrak nama tugas dari nama file
                    $namaTugas = '-';
                    $parts = explode('_', $fileName);
                    if (count($parts) >= 2) {
                        $namaTugas = $parts[1];
                    }
                ?>
                <tr>
                    <td class="border px-2 py-1"><?= htmlspecialchars($row['nama_praktikum']) ?></td>
                    <td class="border px-2 py-1"><?= htmlspecialchars($row['nama_modul']) ?></td>
                    <td class="border px-2 py-1"><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                    <td class="border px-2 py-1 text-sm text-gray-800">
                        <div class="flex flex-col">
                            <strong>Nama File:</strong>
                            <span><?= htmlspecialchars($fileName) ?></span>
                            <strong class="mt-1">Nama Tugas:</strong>
                            <span><?= htmlspecialchars($namaTugas) ?></span>

                            <?php if (!empty($fileName) && file_exists($filePath)): ?>
                                <?php if ($ext === 'pdf'): ?>
                                    <a href="<?= $fileUrl ?>" target="_blank" class="text-blue-600 hover:underline mt-1">📄 Lihat PDF</a>
                                <?php else: ?>
                                    <a href="<?= $fileUrl ?>" download class="text-green-600 hover:underline mt-1">⬇️ Unduh File</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-red-500 italic mt-1">File tidak ditemukan di server</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <?= is_numeric($row['nilai']) ? $row['nilai'] : '-' ?>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <?= !empty($row['feedback']) ? htmlspecialchars($row['feedback']) : '<span class="text-gray-400 italic">Belum ada</span>' ?>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <a href="nilai_laporan.php?id=<?= $row['id'] ?>" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Nilai</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
