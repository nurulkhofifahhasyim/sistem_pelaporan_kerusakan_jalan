-- ============================================================
-- SEEDER: Data awal untuk development
-- Password: admin123  → bcrypt hash
-- ============================================================

USE `db_pelaporan_jalan`;

-- USER_ADMIN (password: admin123)
INSERT INTO `USER_ADMIN` (`nama_petugas`, `username`, `password`, `jabatan`) VALUES
  ('Super Administrator', 'admin',    '$2y$10$WS/aiZJVK5AOpHFe7Yhdv.nzudN0FDnnl3jSHWgd63X88OddgrKBC', 'Superadmin'),
  ('Petugas Satu',        'petugas1', '$2y$10$WS/aiZJVK5AOpHFe7Yhdv.nzudN0FDnnl3jSHWgd63X88OddgrKBC', 'Petugas');

-- KECAMATAN (Kota Kendari, Sulawesi Tenggara — 11 Kecamatan)
INSERT INTO `KECAMATAN` (`nama_kecamatan`) VALUES
  ('Mandonga'),
  ('Baruga'),
  ('Puuwatu'),
  ('Kadia'),
  ('Wua-Wua'),
  ('Kendari'),
  ('Kendari Barat'),
  ('Poasia'),
  ('Kambu'),
  ('Abeli'),
  ('Nambo');

-- KELURAHAN (Kota Kendari)
INSERT INTO `KELURAHAN` (`id_kecamatan`, `nama_kelurahan`) VALUES
  -- Mandonga (1)
  (1, 'Mandonga'),
  (1, 'Korumba'),
  (1, 'Anggilowu'),
  (1, 'Alolama'),
  (1, 'Wawombalata'),
  (1, 'Labibia'),
  -- Baruga (2)
  (2, 'Baruga'),
  (2, 'Lepo-Lepo'),
  (2, 'Wundudopi'),
  (2, 'Watubangga'),
  (2, 'Purirano'),
  -- Puuwatu (3)
  (3, 'Puuwatu'),
  (3, 'Tobuuha'),
  (3, 'Punggolaka'),
  (3, 'Watulondo'),
  (3, 'Abeli Dalam'),
  -- Kadia (4)
  (4, 'Kadia'),
  (4, 'Bende'),
  (4, 'Watu-Watu'),
  (4, 'Pondambea'),
  (4, 'Anaiwoi'),
  -- Wua-Wua (5)
  (5, 'Wua-Wua'),
  (5, 'Bonggoeya'),
  (5, 'Mataiwoi'),
  (5, 'Anawai'),
  (5, 'Bataraguru'),
  -- Kendari (6)
  (6, 'Kendari Caddi'),
  (6, 'Mangga Dua'),
  (6, 'Kandai'),
  (6, 'Gunung Jati'),
  (6, 'Kampung Salo'),
  -- Kendari Barat (7)
  (7, 'Kemaraya'),
  (7, 'Sodohoa'),
  (7, 'Sanua'),
  (7, 'Tipulu'),
  (7, 'Lahundape'),
  -- Poasia (8)
  (8, 'Andonohu'),
  (8, 'Rahandouna'),
  (8, 'Anggoeya'),
  (8, 'Matabubu'),
  (8, 'Talia'),
  -- Kambu (9)
  (9, 'Kambu'),
  (9, 'Padaleu'),
  (9, 'Lalolara'),
  (9, 'Mokoau'),
  -- Abeli (10)
  (10, 'Abeli'),
  (10, 'Anggalomelai'),
  (10, 'Tobimeita'),
  (10, 'Benuanirae'),
  (10, 'Puday'),
  -- Nambo (11)
  (11, 'Nambo'),
  (11, 'Sambuli'),
  (11, 'Tondonggeu'),
  (11, 'Petoaha');
