-- 1. Kamar
INSERT INTO tb_kamar (nomor, harga) VALUES
('1', 50000),
('2', 60000),
('3', 70000);

-- 2. Barang
INSERT INTO tb_barang (nama, harga) VALUES
('Kipas Angin', 10000),
('Lemari', 15000),
('Meja', 5000);

-- 3. Penghuni
INSERT INTO tb_penghuni (nama, no_ktp, no_hp, tgl_masuk, tgl_keluar) VALUES
('Andi', '1234567890', '081234567890', DATE_SUB(CURDATE(), INTERVAL 28 DAY), NULL),
('Budi', '2345678901', '082345678901', DATE_SUB(CURDATE(), INTERVAL 40 DAY), NULL),
('Cici', '3456789012', '083456789012', DATE_SUB(CURDATE(), INTERVAL 10 DAY), NULL);

-- 4. Hunian (tb_kmr_penghuni)
INSERT INTO tb_kmr_penghuni (id_kamar, id_penghuni, tgl_masuk, tgl_keluar) VALUES
(1, 1, DATE_SUB(CURDATE(), INTERVAL 28 DAY), NULL), -- Andi di Kamar 1
(2, 2, DATE_SUB(CURDATE(), INTERVAL 40 DAY), NULL), -- Budi di Kamar 2
(3, 3, DATE_SUB(CURDATE(), INTERVAL 10 DAY), NULL); -- Cici di Kamar 3

-- 5. Barang Bawaan
INSERT INTO tb_brng_bawaan (id_penghuni, id_barang) VALUES
(1, 1), -- Andi bawa Kipas Angin
(1, 2), -- Andi bawa Lemari
(2, 2), -- Budi bawa Lemari
(3, 3); -- Cici bawa Meja

-- 6. Tagihan (id_kmr_penghuni = 1,2,3)
INSERT INTO tb_tagihan (bulan, id_kmr_penghuni, jml_tagihan) VALUES
(DATE_FORMAT(CURDATE(), '%Y-%m'), 1, 50000 + 10000 + 15000), -- Andi
(DATE_FORMAT(CURDATE(), '%Y-%m'), 2, 60000 + 15000),         -- Budi
(DATE_FORMAT(CURDATE(), '%Y-%m'), 3, 70000 + 5000);          -- Cici

-- 7. Pembayaran (id_tagihan = 1,2,3)
INSERT INTO tb_bayar (id_tagihan, jml_bayar, tgl_bayar, status) VALUES
(1, 30000, DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'cicil'), -- Andi cicil
(2, 75000, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'lunas');  -- Budi lunas
-- Cici belum bayar