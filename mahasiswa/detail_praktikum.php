<?php
require_once '../config.php';
require_once 'templates/header_mahasiswa.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    die("Akses ditolak.");
}

$id_mahasiswa = $_SESSION['user_id'];
$id_praktikum = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pesan = isset($_GET['success']) ? 'Laporan berhasil dikumpulkan.' : null;

// Ambil info praktikum
$stmt = $pdo->prepare("SELECT * FROM praktikum WHERE id = ?");
$stmt->execute([$id_praktikum]);
$praktikum = $stmt->fetch();

// Ambil modul yang memiliki file materi dan milik praktikum ini
$stmt_modul = $pdo->prepare("
    SELECT m.*, u.nama AS nama_asisten 
    FROM modul m
    JOIN users u ON m.id_asisten = u.id
    WHERE m.id_praktikum = ? AND m.file_materi IS NOT NULL
");
$stmt_modul->execute([$id_praktikum]);
$modulList = $stmt_modul->fetchAll();

// Ambil laporan yang sudah dikumpulkan mahasiswa
$stmt_laporan = $pdo->prepare("
    SELECT * FROM laporan 
    WHERE id_user = ? 
    AND id_modul IN (
        SELECT id FROM modul WHERE id_praktikum = ?
    )
");
$stmt_laporan->execute([$id_mahasiswa, $id_praktikum]);
$laporanMahasiswa = [];
foreach ($stmt_laporan as $l) {
    $laporanMahasiswa[$l['id_modul']] = $l;
}
?>

<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold mb-4 text-gray-800">
        <?= htmlspecialchars($praktikum['nama_praktikum'] ?? 'Praktikum Tidak Ditemukan') ?>
    </h2>
    <p class="text-gray-700 mb-6">
        <?= htmlspecialchars($praktikum['deskripsi'] ?? '-') ?>
    </p>

    <?php if ($pesan): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= $pesan ?>
        </div>
    <?php endif; ?>

    <h3 class="text-xl font-semibold mb-2 text-gray-800">Modul Praktikum</h3>
    <ul class="mb-6 list-disc pl-6">
        <?php if (count($modulList) > 0): ?>
            <?php foreach ($modulList as $m): ?>
                <li>
                    <a href="../uploads/modul/<?= htmlspecialchars($m['file_materi']) ?>"
                       class="text-blue-600 hover:underline" target="_blank">
                        <?= htmlspecialchars($m['judul']) ?> - oleh <?= htmlspecialchars($m['nama_asisten']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="text-gray-500">Belum ada modul dari asisten.</li>
        <?php endif; ?>
    </ul>

    <h3 class="text-xl font-semibold mb-2 text-gray-800">Tugas & Pengumpulan Laporan</h3>
    <?php if (count($modulList) > 0): ?>
        <?php foreach ($modulList as $m): 
            $laporan = $laporanMahasiswa[$m['id']] ?? null;
        ?>
            <div class="bg-white p-4 rounded shadow mb-4">
                <h4 class="font-bold text-gray-700"><?= htmlspecialchars($m['judul']) ?></h4>
                <p class="text-sm text-gray-600 mb-2">
                    Tugas dari asisten: <?= htmlspecialchars($m['nama_asisten']) ?>
                </p>

                <?php if ($laporan): ?>
                    <p class="mb-2 text-green-700">
                        Laporan sudah dikumpulkan:
                        <a href="../uploads/<?= htmlspecialchars($laporan['file_laporan']) ?>" target="_blank"
                           class="underline text-blue-600">
                            <?= htmlspecialchars($laporan['file_laporan']) ?>
                        </a>
                    </p>

                    <?php if (is_numeric($laporan['nilai'])): ?>
                        <p class="mb-2 text-indigo-700">
                            Nilai dari asisten: <strong><?= $laporan['nilai'] ?></strong>
                        </p>
                    <?php else: ?>
                        <p class="mb-2 text-yellow-600 italic">Nilai belum diberikan oleh asisten.</p>
                    <?php endif; ?>

                    <?php if (!empty($laporan['feedback'])): ?>
                        <p class="mb-2 text-gray-800">
                            Feedback: <em><?= htmlspecialchars($laporan['feedback']) ?></em>
                        </p>
                    <?php endif; ?>

                    <form action="upload_laporan.php?id=<?= $id_praktikum ?>" method="POST" enctype="multipart/form-data" class="space-y-2">
                        <input type="hidden" name="id_modul" value="<?= $m['id'] ?>">
                        <input type="file" name="file_laporan" accept=".pdf,.docx" required
                               class="block w-full border px-3 py-2 rounded-md text-sm">
                        <button type="submit" 
                                class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                            Ganti Laporan
                        </button>
                    </form>
                <?php else: ?>
                    <form action="upload_laporan.php?id=<?= $id_praktikum ?>" method="POST" enctype="multipart/form-data" class="space-y-2">
                        <input type="hidden" name="id_modul" value="<?= $m['id'] ?>">
                        <input type="file" name="file_laporan" accept=".pdf,.docx" required
                               class="block w-full border px-3 py-2 rounded-md text-sm">
                        <button type="submit" 
                                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Upload Laporan
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-gray-500">Belum ada tugas dari asisten untuk dikumpulkan.</p>
    <?php endif; ?>
</div>
