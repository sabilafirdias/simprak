<?php
$pageTitle = 'Manajemen Akun';
$activePage = 'akun';
require_once '../config.php';
require_once 'templates/header.php';

// Tambah
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

    // Ubah
    if (isset($_POST['ubah'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        $stmt = $pdo->prepare("UPDATE users SET nama=?, email=?, role=? WHERE id=?");
        $stmt->execute([$nama, $email, $role, $id]);
    }

    // Hapus
    if (isset($_POST['hapus'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
        $stmt->execute([$id]);
    }
}

$data = $pdo->query("SELECT * FROM users")->fetchAll();
?>

<div class="p-6">
    <!-- Tabel Daftar Akun -->
    <table class="w-full table-auto border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Nama</th>
                <th class="border px-2 py-1">Email</th>
                <th class="border px-2 py-1">Role</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $user): ?>
            <tr>
                <form method="POST">
                    <td class="border px-2 py-1">
                        <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" class="w-full border p-1">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full border p-1">
                    </td>
                    <td class="border px-2 py-1">
                        <select name="role" class="w-full border p-1">
                            <option value="mahasiswa" <?= $user['role'] === 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                            <option value="asisten" <?= $user['role'] === 'asisten' ? 'selected' : '' ?>>Asisten</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1 space-y-1 text-center">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <button name="ubah" class="bg-yellow-500 text-white px-2 py-1 rounded">Ubah</button>
                        <button name="hapus" onclick="return confirm('Yakin hapus akun ini?')" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                    </td>
                </form>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
