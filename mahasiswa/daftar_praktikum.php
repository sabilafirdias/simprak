<?php
require 'config.php';

$id_praktikum = $_GET['id'];
$id_mahasiswa = $_SESSION['user_id'];

$stmt = $pdo->prepare("INSERT INTO peserta_praktikum (id_mahasiswa, id_praktikum) VALUES (?, ?)");
$stmt->execute([$id_mahasiswa, $id_praktikum]);

header("Location: praktikum_saya.php");
