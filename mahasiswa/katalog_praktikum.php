<?php
$pageTitle = 'Praktikum';
$activePage = 'praktikum';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

// Ambil ID mahasiswa dari session login
$id_mahasiswa = $_SESSION['user_id'];

// Cek jika ada aksi daftar
if (isset($_GET['daftar']) && isset($_GET['id'])) {
    $id_praktikum = intval($_GET['id']);

    // Cek apakah sudah pernah daftar
    $cek = $pdo->prepare("SELECT COUNT(*) FROM peserta_praktikum WHERE id_mahasiswa = ? AND id_praktikum = ?");
    $cek->execute([$id_mahasiswa, $id_praktikum]);
    if ($cek->fetchColumn() == 0) {
        $stmt_daftar = $pdo->prepare("INSERT INTO peserta_praktikum (id_mahasiswa, id_praktikum) VALUES (?, ?)");
        $stmt_daftar->execute([$id_mahasiswa, $id_praktikum]);
        $notif = "Berhasil mendaftar praktikum.";
    } else {
        $notif = "Anda sudah terdaftar pada praktikum ini.";
    }
}

// Cek jika ada aksi batal
if (isset($_GET['batal']) && isset($_GET['id'])) {
    $id_praktikum = intval($_GET['id']);
    $stmt_batal = $pdo->prepare("DELETE FROM peserta_praktikum WHERE id_mahasiswa = ? AND id_praktikum = ?");
    $stmt_batal->execute([$id_mahasiswa, $id_praktikum]);
    $notif = "Pendaftaran praktikum berhasil dibatalkan.";
}

// Ambil daftar mata praktikum
try {
    $stmt = $pdo->query("SELECT * FROM praktikum");
} catch (PDOException $e) {
    die("Query error: " . $e->getMessage());
}
?>

<?php if (!empty($notif)): ?>
    <div class="alert alert-info text-center"><?php echo $notif; ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Katalog Praktikum</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container py-4">
        <h2 class="mb-4">Daftar Mata Praktikum</h2>
        <div class="row">
            <?php while ($praktikum = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <?php
                    $cek_terdaftar = $pdo->prepare("SELECT COUNT(*) FROM peserta_praktikum WHERE id_mahasiswa = ? AND id_praktikum = ?");
                    $cek_terdaftar->execute([$id_mahasiswa, $praktikum['id']]);
                    $sudah_terdaftar = $cek_terdaftar->fetchColumn() > 0;
                ?>
                <div class="col-md-4 mb-4">
                    <div class="p-4 bg-white border border-gray-200 rounded shadow-sm">
                        <h5 class="mb-2 text-xl fw-bold text-dark">
                            <?php echo htmlspecialchars($praktikum['nama_praktikum']); ?>
                        </h5>
                        <p class="text-muted mb-3">
                            <?php echo htmlspecialchars($praktikum['deskripsi']); ?>
                        </p>

                        <?php if ($sudah_terdaftar): ?>
                            <!-- Tombol Trigger Modal -->
                            <button type="button"
                                    class="btn btn-danger w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalBatal<?php echo $praktikum['id']; ?>">
                                Batal Pendaftaran
                            </button>

                            <!-- Modal Konfirmasi -->
                            <div class="modal fade" id="modalBatal<?php echo $praktikum['id']; ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo $praktikum['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel<?php echo $praktikum['id']; ?>">Konfirmasi Pembatalan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah kamu yakin ingin membatalkan pendaftaran pada <strong><?php echo htmlspecialchars($praktikum['nama_praktikum']); ?></strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                                            <a href="?batal=1&id=<?php echo $praktikum['id']; ?>" class="btn btn-danger">Ya, Batalkan</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="?daftar=1&id=<?php echo $praktikum['id']; ?>"
                               class="btn btn-warning text-white w-100">
                                Daftar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
