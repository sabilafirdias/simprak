CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','asisten') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE praktikum (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_praktikum VARCHAR(100) NOT NULL,
    deskripsi TEXT
);

CREATE TABLE modul (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_praktikum INT NOT NULL,
    id_asisten INT,
    judul VARCHAR(100) NOT NULL,
    file_materi VARCHAR(255),
    FOREIGN KEY (id_praktikum) REFERENCES praktikum(id) ON DELETE CASCADE,
    FOREIGN KEY (id_asisten) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE modul_file (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_modul INT NOT NULL,
    file_materi VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_modul) REFERENCES modul(id) ON DELETE CASCADE
);


CREATE TABLE peserta_praktikum (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_mahasiswa INT NOT NULL,
    id_praktikum INT NOT NULL,
    UNIQUE (id_mahasiswa, id_praktikum),
    FOREIGN KEY (id_mahasiswa) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_praktikum) REFERENCES praktikum(id) ON DELETE CASCADE
);

CREATE TABLE laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_modul INT NOT NULL,
    file_laporan VARCHAR(255),
    tanggal_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_user, id_modul),
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_modul) REFERENCES modul(id) ON DELETE CASCADE
);

CREATE TABLE nilai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_modul INT NOT NULL,
    nilai VARCHAR(10),
    komentar TEXT,
    tanggal_nilai DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_user, id_modul),
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_modul) REFERENCES modul(id) ON DELETE CASCADE
);

CREATE TABLE notifikasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_mahasiswa INT NOT NULL,
  pesan TEXT NOT NULL,
  icon VARCHAR(10) DEFAULT '🔔',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_mahasiswa) REFERENCES users(id)
);

ALTER TABLE laporan ADD COLUMN deskripsi TEXT;

ALTER TABLE laporan ADD COLUMN id_praktikum INT NULL;

ALTER TABLE laporan ADD COLUMN judul VARCHAR(255) DEFAULT NULL;

ALTER TABLE laporan ADD COLUMN feedback TEXT;

ALTER TABLE modul ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;


-- Praktikum
INSERT INTO praktikum (nama_praktikum, deskripsi) VALUES
('Pemrograman Web', 'HTML, CSS, PHP');

-- Modul dibuat oleh asisten (id = 2)
INSERT INTO modul (id_praktikum, id_asisten, judul, file_materi) VALUES
(1, 2, 'Pertemuan 1', 'pertemuan1.pdf');

-- Mahasiswa (id = 1) mendaftar
INSERT INTO peserta_praktikum (id_user, id_praktikum) VALUES (1, 1);

-- Mahasiswa upload laporan
INSERT INTO laporan (id_user, id_modul, file_laporan) VALUES (1, 1, 'laporan1.pdf');

-- Asisten memberi nilai
INSERT INTO nilai (id_user, id_modul, nilai, komentar) VALUES (1, 1, '85', 'Bagus');