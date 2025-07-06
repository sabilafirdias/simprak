<?php
$pageTitle = 'Mata Praktikum';
$activePage = 'matapraktikum';
require_once '../config.php';
require_once 'templates/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah'])) {
        $nama = $_POST['nama_praktikum'];
        $deskripsi = $_POST['deskripsi'];
        $stmt = $pdo->prepare("INSERT INTO praktikum (nama_praktikum, deskripsi) VALUES (?, ?)");
        $stmt->execute([$nama, $deskripsi]);
    } elseif (isset($_POST['hapus'])) {
        $id = $_POST['id'];
        $pdo->prepare("DELETE FROM praktikum WHERE id = ?")->execute([$id]);
    } elseif (isset($_POST['ubah'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama_praktikum'];
        $deskripsi = $_POST['deskripsi'];
        $pdo->prepare("UPDATE praktikum SET nama_praktikum = ?, deskripsi = ? WHERE id = ?")->execute([$nama, $deskripsi, $id]);
    }
}

$data = $pdo->query("SELECT * FROM praktikum")->fetchAll();
?>

<div class="p-6">

    <form method="POST" class="mb-6 space-y-2">
        <input name="nama_praktikum" class="border p-2 w-full" placeholder="Nama Praktikum">
        <textarea name="deskripsi" class="border p-2 w-full" placeholder="Deskripsi"></textarea>
        <button name="tambah" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah</button>
    </form>

    <table class="w-full table-auto border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1">Nama Praktikum</th>
                <th class="border px-2 py-1">Deskripsi</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
            <tr>
                <form method="POST">
                    <td class="border px-2 py-1">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="text" name="nama_praktikum" value="<?= htmlspecialchars($row['nama_praktikum']) ?>" class="w-full border px-2 py-1">
                    </td>
                    <td class="border px-2 py-1">
                        <textarea name="deskripsi" class="w-full border px-2 py-1"><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                    </td>
                    <td class="border px-2 py-1 space-x-1">
                        <button name="ubah" class="bg-yellow-500 text-white px-2 py-1 rounded">Ubah</button>
                        <button name="hapus" class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                    </td>
                </form>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
