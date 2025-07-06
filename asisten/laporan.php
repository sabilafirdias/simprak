<?php
$pageTitle = 'Manajemen Laporan';
$activePage = 'laporan';
ob_start();
session_start();

require_once '../config.php';
require_once 'templates/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    die('Akses ditolak.');
}

$id_asisten = $_SESSION['user_id'];

$praktikumList = $pdo->query("SELECT * FROM praktikum")->fetchAll();

// Ambil modul milik asisten
$modulList = $pdo->prepare("SELECT m.*, p.nama_praktikum FROM modul m JOIN praktikum p ON m.id_praktikum = p.id WHERE m.id_asisten = ?");
$modulList->execute([$id_asisten]);
$modulList = $modulList->fetchAll();


// Proses tambah/ubah/hapus laporan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_modul = $_POST['id_modul'];
    $id_laporan = $_POST['id'] ?? null;

    // Tambah laporan
    if (isset($_POST['tambah']) && isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $allowed = ['pdf', 'docx'];
        if (in_array(strtolower($ext), $allowed)) {
            $uploadDir = "../uploads";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $safeName = time() . '_' . preg_replace('/\s+/', '_', $_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], "$uploadDir/$safeName");

            $stmt = $pdo->prepare("INSERT INTO laporan (id_user, id_modul, file_laporan, tanggal_upload) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$id_asisten, $id_modul, $safeName]);
        }
        header("Location: laporan.php");
        exit;
    }

    // Ubah laporan (ganti file jika ada)
    if (isset($_POST['ubah']) && $id_laporan) {
        $updateQuery = "UPDATE laporan SET id_modul=?";

        $params = [$id_modul];

        if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $allowed = ['pdf', 'docx'];
            if (in_array(strtolower($ext), $allowed)) {
                $uploadDir = "../uploads";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $safeName = time() . '_' . preg_replace('/\s+/', '_', $_FILES['file']['name']);
                move_uploaded_file($_FILES['file']['tmp_name'], "$uploadDir/$safeName");
                $updateQuery .= ", file_laporan=?";
                $params[] = $safeName;
            }
        }

        $updateQuery .= " WHERE id=?";
        $params[] = $id_laporan;

        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute($params);

        header("Location: laporan.php");
        exit;
    }

    // Hapus laporan
    if (isset($_POST['hapus']) && $id_laporan) {
        $stmt = $pdo->prepare("DELETE FROM laporan WHERE id = ?");
        $stmt->execute([$id_laporan]);
        header("Location: laporan.php");
        exit;
    }
}

// Ambil semua laporan milik asisten berdasarkan modul yang dia buat
$data = $pdo->prepare("
    SELECT l.*, m.judul AS judul_modul, p.nama_praktikum
    FROM laporan l
    JOIN modul m ON l.id_modul = m.id
    JOIN praktikum p ON m.id_praktikum = p.id
    WHERE m.id_asisten = ? AND l.id_user = ?
");
$data->execute([$id_asisten, $id_asisten]);
$laporanList = $data->fetchAll();
?>

<div class="p-6">

    <!-- Form Tambah -->
    <form method="POST" enctype="multipart/form-data" class="mb-6 space-y-2">
        <select name="id_modul" class="border p-2 w-full" required>
    <option value="">Pilih Modul</option>
    <?php foreach ($modulList as $m): ?>
        <option value="<?= $m['id'] ?>">
            <?= htmlspecialchars($m['judul']) ?> (<?= htmlspecialchars($m['nama_praktikum']) ?>)
        </option>
    <?php endforeach; ?>
</select>

        <input name="judul" class="border p-2 w-full" placeholder="Judul Tugas" required>
        <input name="deskripsi" class="border p-2 w-full" placeholder="Deskripsi Tugas" required>
        <input type="file" name="file" accept=".pdf,.docx" required class="w-full">
        <button name="tambah" class="bg-blue-600 text-white px-4 py-2 rounded">Tambah Laporan</button>
    </form>

    <!-- Tabel -->
    <table class="w-full table-auto border">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Praktikum</th>
                <th class="border px-2 py-1">Modul</th>
                <th class="border px-2 py-1">File</th>
                <th class="border px-2 py-1">Tanggal</th>
                <th class="border px-2 py-1">Ubah</th>
                <th class="border px-2 py-1">Hapus</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($laporanList as $row): ?>
            <tr>
                <form method="POST" enctype="multipart/form-data">
                    <td class="border px-2 py-1"><?= htmlspecialchars($row['nama_praktikum']) ?></td>
                    <td class="border px-2 py-1">
                        <select name="id_modul" class="border">
                            <?php foreach ($modulList as $m): ?>
                                <option value="<?= $m['id'] ?>" <?= $m['id'] == $row['id_modul'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['judul']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td class="border px-2 py-1 text-sm">
                        <a href="../uploads/<?= $row['file_laporan'] ?>" target="_blank" class="text-blue-600 underline">
                            <?= htmlspecialchars(preg_replace('/^\d+_/', '', $row['file_laporan'])) ?>
                        </a>
                        <input type="file" name="file" accept=".pdf,.docx" class="mt-1 w-full">
                    </td>
                    <td class="border px-2 py-1"><?= htmlspecialchars($row['tanggal_upload']) ?></td>
                    <td class="border px-2 py-1 text-center">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button name="ubah" class="bg-yellow-500 text-white px-2 py-1 rounded">Ubah</button>
                    </td>
                </form>
                <td class="border px-2 py-1 text-center">
                    <form method="POST" onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button name="hapus" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php ob_end_flush(); ?>
