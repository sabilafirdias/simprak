<?php
require_once '../config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    die("Akses ditolak.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_SESSION['user_id'];
    $id_modul = intval($_POST['id_modul']);

    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['file_laporan']['name']);
        $file_tmp = $_FILES['file_laporan']['tmp_name'];

        // Validasi ekstensi file
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['pdf', 'docx'];

        if (!in_array($ext, $allowed)) {
            die("Format file tidak didukung. Hanya file PDF dan DOCX yang diperbolehkan.");
        }

        // Ganti nama file agar aman dan unik
        $safe_name = time() . '_' . preg_replace('/\s+/', '_', $file_name);
        $upload_dir = '../uploads/laporan/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_dest = $upload_dir . $safe_name;

        if (move_uploaded_file($file_tmp, $file_dest)) {
            $stmt = $pdo->prepare("REPLACE INTO laporan (id_user, id_modul, file_laporan, tanggal_upload) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$id_user, $id_modul, $safe_name]);

            header("Location: detail_praktikum.php?id=" . $_GET['id']);
            exit;
        } else {
            echo "Gagal mengunggah file.";
        }
    } else {
        echo "Tidak ada file yang diunggah atau terjadi kesalahan.";
    }
}
?>
