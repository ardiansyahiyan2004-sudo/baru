-- Database untuk aplikasi pengiriman barang
CREATE DATABASE IF NOT EXISTS pengiriman_db;
USE pengiriman_db;

CREATE TABLE login (
    id_login INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','petugas','kurir') NOT NULL,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif'
);

CREATE TABLE admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    id_login INT NOT NULL,
    nama_admin VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    no_hp VARCHAR(20),
    FOREIGN KEY (id_login) REFERENCES login(id_login)
);

CREATE TABLE petugas (
    id_petugas INT AUTO_INCREMENT PRIMARY KEY,
    id_login INT NOT NULL,
    nama_petugas VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    no_hp VARCHAR(20),
    FOREIGN KEY (id_login) REFERENCES login(id_login)
);

CREATE TABLE kurir (
    id_kurir INT AUTO_INCREMENT PRIMARY KEY,
    id_login INT NOT NULL,
    nama_kurir VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20),
    alamat TEXT,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    FOREIGN KEY (id_login) REFERENCES login(id_login)
);

CREATE TABLE barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    kode_resi VARCHAR(50) NOT NULL UNIQUE,
    nama_pengirim VARCHAR(100) NOT NULL,
    alamat_pengirim TEXT NOT NULL,
    nama_penerima VARCHAR(100) NOT NULL,
    alamat_penerima TEXT NOT NULL,
    no_hp_penerima VARCHAR(20),
    berat DECIMAL(10,2),
    jenis_barang VARCHAR(100),
    tgl_input DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_petugas INT,
    FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas)
);

CREATE TABLE pengiriman_barang (
    id_pengiriman INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT NOT NULL,
    id_kurir INT NOT NULL,
    status_pengiriman ENUM('Menunggu Pickup','Sedang Diantar','Terkirim','Gagal') DEFAULT 'Menunggu Pickup',
    tgl_kirim DATETIME,
    tgl_terima DATETIME,
    keterangan TEXT,
    update_oleh INT,
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang),
    FOREIGN KEY (id_kurir) REFERENCES kurir(id_kurir),
    FOREIGN KEY (update_oleh) REFERENCES login(id_login)
);

-- Data awal login
INSERT INTO login (username, password, role, status) VALUES
('admin1', MD5('admin123'), 'admin', 'aktif'),
('petugas1', MD5('petugas123'), 'petugas', 'aktif'),
('kurir1', MD5('kurir123'), 'kurir', 'aktif');

-- Hubungkan ke tabel role masing-masing
INSERT INTO admin (id_login, nama_admin, email, no_hp) VALUES
(1, 'Admin Utama', 'admin@example.com', '081234567890');

INSERT INTO petugas (id_login, nama_petugas, email, no_hp) VALUES
(2, 'Petugas Gudang', 'petugas@example.com', '081234567891');

INSERT INTO kurir (id_login, nama_kurir, no_hp, alamat, status) VALUES
(3, 'Kurir A', '081234567892', 'Jakarta', 'aktif');
