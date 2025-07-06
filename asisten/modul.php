<?php
$pageTitle = 'Manajemen Modul';
$activePage = 'modul';
ob_start();
session_start();

require_once '../config.php';
require_once 'templates/header.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    die('Akses ditolak.');
}

$id_asisten = $_SESSION['user_id'];
$praktikumList = $pdo->query("SELECT * FROM praktikum")->fetchAll();

// Tambah modul
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_praktikum = $_POST['id_praktikum'] ?? null;
    $judul = $_POST['judul'] ?? '';
    $id = $_POST['id'] ?? null;

    // Tambah
    if (isset($_POST['tambah'])) {
        $stmt = $pdo->prepare("INSERT INTO modul (id_praktikum, id_asisten, judul) VALUES (?, ?, ?)");
        $stmt->execute([$id_praktikum, $id_asisten, $judul]);
        $modul_id = $pdo->lastInsertId();

        if (isset($_FILES['files'])) {
            foreach ($_FILES['files']['name'] as $i => $name) {
                if ($_FILES['files']['error'][$i] === 0) {
                    $originalName = $_FILES['files']['name'][$i];
                    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                    $allowed = ['pdf', 'docx'];
                    if (in_array(strtolower($ext), $allowed)) {
                        $uploadDir = "../uploads";
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                        $safeName = time() . '_' . preg_replace('/\s+/', '_', basename($originalName));
                        move_uploaded_file($_FILES['files']['tmp_name'][$i], "$uploadDir/$safeName");

                        $stmtFile = $pdo->prepare("INSERT INTO modul_file (id_modul, file_materi) VALUES (?, ?)");
                        $stmtFile->execute([$modul_id, $safeName]);
                    }
                }
            }
        }

        header("Location: modul.php");
        exit;
    }

    // Ubah
    if (isset($_POST['ubah']) && $id) {
        $stmt = $pdo->prepare("UPDATE modul SET id_praktikum=?, id_asisten=?, judul=? WHERE id=?");
        $stmt->execute([$id_praktikum, $id_asisten, $judul, $id]);

        if (isset($_FILES['files'])) {
            foreach ($_FILES['files']['name'] as $i => $name) {
                if ($_FILES['files']['error'][$i] === 0) {
                    $originalName = $_FILES['files']['name'][$i];
                    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                    $allowed = ['pdf', 'docx'];
                    if (in_array(strtolower($ext), $allowed)) {
                        $uploadDir = "../uploads";
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                        $safeName = time() . '_' . preg_replace('/\s+/', '_', basename($originalName));
                        move_uploaded_file($_FILES['files']['tmp_name'][$i], "$uploadDir/$safeName");

                        $stmtFile = $pdo->prepare("INSERT INTO modul_file (id_modul, file_materi) VALUES (?, ?)");
                        $stmtFile->execute([$id, $safeName]);
                    }
                }
            }
        }

        header("Location: modul.php");
        exit;
    }

    // Hapus
    if (isset($_POST['hapus']) && $id) {
        $stmt = $pdo->prepare("DELETE FROM modul WHERE id=?");
        $stmt->execute([$id]);
        header("Location: modul.php");
        exit;
    }
}

// Ambil data
$data = $pdo->query("SELECT m.*, p.nama_praktikum FROM modul m JOIN praktikum p ON m.id_praktikum = p.id")->fetchAll();
$fileData = $pdo->query("SELECT * FROM modul_file")->fetchAll();

$filesByModul = [];
foreach ($fileData as $file) {
    $filesByModul[$file['id_modul']][] = $file['file_materi'];
}
?>

<div class="p-6">

    <!-- Form Tambah -->
    <form method="POST" enctype="multipart/form-data" class="mb-6 space-y-2">
        <select name="id_praktikum" class="border p-2 w-full" required>
            <option value="">Pilih Praktikum</option>
            <?php foreach ($praktikumList as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_praktikum']) ?></option>
            <?php endforeach; ?>
        </select>
        <input name="judul" class="border p-2 w-full" placeholder="Judul Modul" required>
        <input type="file" name="files[]" accept=".pdf,.docx" class="w-full" multiple>
        <button name="tambah" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah Modul</button>
    </form>

    <!-- Tabel Modul -->
    <table class="w-full table-auto border">
        <thead class="bg-gray-100">
            <tr><th class="border px-2 py-1">Praktikum</th><th class="border px-2 py-1">Judul</th><th class="border px-2 py-1">File</th><th class="border px-2 py-1">Ubah</th><th class="border px-2 py-1">Hapus</th></tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
            <tr>
                <form method="POST" enctype="multipart/form-data">
                    <td class="border px-2 py-1">
                        <select name="id_praktikum" class="w-full border">
                            <?php foreach ($praktikumList as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $p['id'] == $row['id_praktikum'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nama_praktikum']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <input type="text" name="judul" value="<?= htmlspecialchars($row['judul']) ?>" class="w-full border px-1">
                    </td>
                    <td class="border px-2 py-1 text-sm">
                        <?php if (!empty($filesByModul[$row['id']])): ?>
                            <?php foreach ($filesByModul[$row['id']] as $f): ?>
                                <div>
                                    <a href="../uploads/<?= $f ?>" target="_blank" class="text-blue-600 underline">
                                        <?= htmlspecialchars(preg_replace('/^\d+_/', '', $f)) ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <em class="text-gray-400">Belum ada file</em>
                        <?php endif; ?>
                        <input type="file" name="files[]" accept=".pdf,.docx" class="w-full mt-1" multiple>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button name="ubah" class="bg-yellow-500 text-white px-2 py-1 rounded">Ubah</button>
                    </td>
                </form>
                <td class="border px-2 py-1 text-center">
                    <form method="POST" onsubmit="return confirm('Yakin hapus?')">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="id_praktikum" value="<?= $row['id_praktikum'] ?>">
                        <input type="hidden" name="judul" value="<?= htmlspecialchars($row['judul']) ?>">
                        <button name="hapus" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php ob_end_flush(); ?>
