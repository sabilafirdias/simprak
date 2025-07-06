<?php
// Konfigurasi database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'simprak';
$charset = 'utf8mb4';

// Koneksi MySQLi (jika kamu masih pakai $conn di bagian lain)
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi MySQLi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Koneksi PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>