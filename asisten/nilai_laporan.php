<?php
$pageTitle = 'Nilai & Feedback';
$activePage = 'nilai';
ob_start();
session_start();

require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    die('Akses ditolak.');
}

$id_laporan = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("
    SELECT l.*, m.judul AS nama_modul, u.nama AS nama_mahasiswa, p.nama_praktikum
    FROM laporan l
    JOIN modul m ON l.id_modul = m.id
    JOIN users u ON l.id_user = u.id
    JOIN praktikum p ON m.id_praktikum = p.id
    WHERE l.id = ?
");
$stmt->execute([$id_laporan]);
$laporan = $stmt->fetch();

if (!$laporan) {
    echo "<p class='p-4 text-red-600'>Laporan tidak ditemukan.</p>";
    exit;
}

// Proses simpan nilai dan feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = isset($_POST['nilai']) ? intval($_POST['nilai']) : null;
    $feedback = trim($_POST['feedback'] ?? '');

    if ($nilai !== null && $nilai >= 0 && $nilai <= 100) {
        $update = $pdo->prepare("UPDATE laporan SET nilai = ?, feedback = ? WHERE id = ?");
        $update->execute([$nilai, $feedback, $id_laporan]);

        header("Location: laporan_masuk.php");
        exit;
    } else {
        $error = "Nilai harus di antara 0-100.";
    }
}

require_once 'templates/header.php';
?>

<div class="p-6">

    <?php if (isset($error)): ?>
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="mb-6">
        <p><strong>Praktikum:</strong> <?= htmlspecialchars($laporan['nama_praktikum']) ?></p>
        <p><strong>Modul:</strong> <?= htmlspecialchars($laporan['nama_modul']) ?></p>
        <p><strong>Mahasiswa:</strong> <?= htmlspecialchars($laporan['nama_mahasiswa']) ?></p>
        <p><strong>File Laporan:</strong>
            <?php if (!empty($laporan['file_laporan'])): ?>
                <?php
                    $ext = strtolower(pathinfo($laporan['file_laporan'], PATHINFO_EXTENSION));
                    $fileUrl = "../uploads/" . urlencode($laporan['file_laporan']);
                ?>
                <?php if ($ext === 'pdf'): ?>
                    <a href="<?= $fileUrl ?>" target="_blank" class="text-blue-600 underline">Lihat PDF</a>
                <?php elseif ($ext === 'docx'): ?>
                    <a href="<?= $fileUrl ?>" class="text-purple-600 underline" download>Unduh DOCX</a>
                <?php else: ?>
                    <a href="<?= $fileUrl ?>" class="text-blue-500 underline" download>Unduh</a>
                <?php endif; ?>
            <?php else: ?>
                <span class="text-gray-500">-</span>
            <?php endif; ?>
        </p>
    </div>

    <form method="POST" class="space-y-4">
        <label class="block">
            <span class="block font-medium text-gray-700">Nilai (0–100):</span>
            <input type="number" name="nilai" min="0" max="100" required
                   value="<?= htmlspecialchars($laporan['nilai'] ?? '') ?>"
                   class="border p-2 w-full rounded">
        </label>

        <label class="block">
            <span class="block font-medium text-gray-700">Feedback:</span>
            <textarea name="feedback" rows="4"
                      class="border p-2 w-full rounded"><?= htmlspecialchars($laporan['feedback'] ?? '') ?></textarea>
        </label>

        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
    </form>
</div>

<?php ob_end_flush(); ?>
