<?php
$besok = date('Y-m-d', strtotime('+1 day'));

$stmt = $pdo->prepare("SELECT * FROM laporan WHERE deadline = ?");
$stmt->execute([$besok]);

foreach ($stmt as $laporan) {
    $id_mhs = $laporan['id_mahasiswa'];
    $judul = $laporan['judul'];
    $pesan = "Batas waktu pengumpulan laporan <strong>$judul</strong> adalah besok!";
    $pdo->prepare("INSERT INTO notifikasi (id_mahasiswa, pesan, icon) VALUES (?, ?, ?)")->execute([$id_mhs, $pesan, '⏳']);
}
?>