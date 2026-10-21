-- ============================================================
-- SCHEMA: Sistem Pelaporan Kerusakan Jalan
-- Versi     : 1.0
-- Dibuat    : 2026-05-05
-- ============================================================

CREATE DATABASE IF NOT EXISTS `db_pelaporan_jalan`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `db_pelaporan_jalan`;

-- ============================================================
-- 1. KECAMATAN
-- ============================================================
CREATE TABLE IF NOT EXISTS `KECAMATAN` (
  `id_kecamatan`   INT            NOT NULL AUTO_INCREMENT,
  `nama_kecamatan` VARCHAR(100)   NOT NULL,
  PRIMARY KEY (`id_kecamatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. KELURAHAN
-- ============================================================
CREATE TABLE IF NOT EXISTS `KELURAHAN` (
  `id_kelurahan`   INT          NOT NULL AUTO_INCREMENT,
  `id_kecamatan`   INT          NOT NULL,
  `nama_kelurahan` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_kelurahan`),
  CONSTRAINT `fk_kelurahan_kecamatan`
    FOREIGN KEY (`id_kecamatan`)
    REFERENCES `KECAMATAN` (`id_kecamatan`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. MASYARAKAT  (pelapor, tanpa autentikasi)
-- ============================================================
CREATE TABLE IF NOT EXISTS `MASYARAKAT` (
  `id_masyarakat` INT           NOT NULL AUTO_INCREMENT,
  `nama_lengkap`  VARCHAR(150)  NOT NULL,
  `email`         VARCHAR(150)  NOT NULL,
  `no_telepon`    VARCHAR(20)   DEFAULT NULL,
  `alamat`        TEXT          DEFAULT NULL,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_masyarakat`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. PENGADUAN  (laporan kerusakan jalan)
-- ============================================================
CREATE TABLE IF NOT EXISTS `PENGADUAN` (
  `id_pengaduan`     INT          NOT NULL AUTO_INCREMENT,
  `kode_tiket`       VARCHAR(25)  NOT NULL,
  `id_masyarakat`    INT          NOT NULL,
  `id_kelurahan`     INT          NOT NULL,
  `isi_laporan`      TEXT         NOT NULL,
  `foto_bukti`       VARCHAR(255) NOT NULL,
  `status_pengaduan` ENUM('Diproses','Selesai','Ditolak') NOT NULL DEFAULT 'Diproses',
  `tgl_pengaduan`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengaduan`),
  UNIQUE KEY `uk_kode_tiket` (`kode_tiket`),
  KEY `idx_status` (`status_pengaduan`),
  KEY `idx_id_kelurahan` (`id_kelurahan`),
  KEY `idx_id_masyarakat` (`id_masyarakat`),
  CONSTRAINT `fk_pengaduan_masyarakat`
    FOREIGN KEY (`id_masyarakat`)
    REFERENCES `MASYARAKAT` (`id_masyarakat`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pengaduan_kelurahan`
    FOREIGN KEY (`id_kelurahan`)
    REFERENCES `KELURAHAN` (`id_kelurahan`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. USER_ADMIN  (petugas / superadmin)
-- ============================================================
CREATE TABLE IF NOT EXISTS `USER_ADMIN` (
  `id_user`      INT          NOT NULL AUTO_INCREMENT,
  `nama_petugas` VARCHAR(150) NOT NULL,
  `username`     VARCHAR(50)  NOT NULL,
  `password`     VARCHAR(255) NOT NULL,
  `jabatan`      ENUM('Superadmin','Petugas') NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. TANGGAPAN  (respons admin per laporan)
-- ============================================================
CREATE TABLE IF NOT EXISTS `TANGGAPAN` (
  `id_tanggapan`     INT      NOT NULL AUTO_INCREMENT,
  `id_pengaduan`     INT      NOT NULL,
  `id_user`          INT      NOT NULL,
  `isi_tanggapan`    TEXT     NOT NULL,
  `foto_tanggapan`   VARCHAR(255) DEFAULT NULL,
  `status_saat_itu`  ENUM('Diproses','Selesai','Ditolak') NOT NULL DEFAULT 'Diproses',
  `tgl_tanggapan`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_tanggapan`),
  KEY `idx_id_pengaduan` (`id_pengaduan`),
  CONSTRAINT `fk_tanggapan_pengaduan`
    FOREIGN KEY (`id_pengaduan`)
    REFERENCES `PENGADUAN` (`id_pengaduan`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tanggapan_user`
    FOREIGN KEY (`id_user`)
    REFERENCES `USER_ADMIN` (`id_user`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. LOG_AKTIVITAS  (rekam jejak tindakan admin)
-- ============================================================
CREATE TABLE IF NOT EXISTS `LOG_AKTIVITAS` (
  `id_log`    INT          NOT NULL AUTO_INCREMENT,
  `id_user`   INT          NOT NULL,
  `aktivitas` VARCHAR(255) DEFAULT NULL,
  `tgl_log`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `idx_id_user` (`id_user`),
  CONSTRAINT `fk_log_user`
    FOREIGN KEY (`id_user`)
    REFERENCES `USER_ADMIN` (`id_user`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. SARAN  (aspirasi umum masyarakat)
-- ============================================================
CREATE TABLE IF NOT EXISTS `SARAN` (
  `id_saran`      INT      NOT NULL AUTO_INCREMENT,
  `nama_pengirim` VARCHAR(150) DEFAULT NULL,
  `isi_saran`     TEXT         NOT NULL,
  `tgl_saran`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_saran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
