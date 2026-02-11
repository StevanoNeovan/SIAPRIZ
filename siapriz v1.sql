-- ============================================================
-- DATABASE
-- ============================================================


-- ============================================================
-- 1. TABEL UTAMA
-- ============================================================

CREATE TABLE perusahaan (
    id_perusahaan INT AUTO_INCREMENT PRIMARY KEY,
    nama_perusahaan VARCHAR(100) NOT NULL,
    logo_url VARCHAR(500),
    bidang_usaha VARCHAR(100),
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_aktif BOOLEAN DEFAULT TRUE,
    INDEX idx_nama_perusahaan (nama_perusahaan)
) ENGINE=InnoDB;

CREATE TABLE role (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    nama_role VARCHAR(50) NOT NULL UNIQUE,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nama_role (nama_role)
) ENGINE=InnoDB;

CREATE TABLE pengguna (
    id_pengguna INT AUTO_INCREMENT PRIMARY KEY,
    id_perusahaan INT NOT NULL,
    id_role INT NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    login_terakhir TIMESTAMP NULL,
    is_aktif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_perusahaan) REFERENCES perusahaan(id_perusahaan) ON DELETE CASCADE,
    FOREIGN KEY (id_role) REFERENCES role(id_role),
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (id_role)
) ENGINE=InnoDB;

CREATE TABLE marketplace (
    id_marketplace INT AUTO_INCREMENT PRIMARY KEY,
    nama_marketplace VARCHAR(100) NOT NULL,
    kode_marketplace VARCHAR(50) NOT NULL UNIQUE,
    logo_url VARCHAR(500),
    is_aktif BOOLEAN DEFAULT TRUE,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_kode_marketplace (kode_marketplace)
) ENGINE=InnoDB;

CREATE TABLE produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,
    id_perusahaan INT NOT NULL,
    sku VARCHAR(100) NOT NULL,
    nama_produk VARCHAR(100) NOT NULL,
    kategori VARCHAR(100),
    harga_dasar DECIMAL(15,2) NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_aktif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_perusahaan) REFERENCES perusahaan(id_perusahaan) ON DELETE CASCADE,
    UNIQUE KEY uk_perusahaan_sku (id_perusahaan, sku),
    INDEX idx_nama_produk (nama_produk),
    INDEX idx_sku (sku)
) ENGINE=InnoDB;

CREATE TABLE penjualan_transaksi (
    id_transaksi BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_perusahaan INT NOT NULL,
    id_marketplace INT NOT NULL,
    order_id VARCHAR(100) NOT NULL,
    tanggal_order DATE NOT NULL,
    status_order ENUM('selesai','proses','dibatalkan','dikembalikan') DEFAULT 'selesai',

    total_pesanan DECIMAL(15,2) NOT NULL,
    total_diskon DECIMAL(15,2) DEFAULT 0,
    ongkos_kirim DECIMAL(15,2) DEFAULT 0,
    biaya_komisi DECIMAL(15,2) DEFAULT 0,
    pendapatan_bersih DECIMAL(15,2),

    nama_customer VARCHAR(100),
    kota_customer VARCHAR(50),
    provinsi_customer VARCHAR(50),

    id_batch_upload INT,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (id_perusahaan) REFERENCES perusahaan(id_perusahaan) ON DELETE CASCADE,
    FOREIGN KEY (id_marketplace) REFERENCES marketplace(id_marketplace),

    INDEX idx_order_id (order_id),
    INDEX idx_tanggal_order (tanggal_order),
    INDEX idx_marketplace (id_marketplace),
    INDEX idx_batch_upload (id_batch_upload),
    INDEX idx_status (status_order),
    INDEX idx_perusahaan_tanggal (id_perusahaan, tanggal_order)
) ENGINE=InnoDB;

CREATE TABLE penjualan_transaksi_detail (
    id_detail BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi BIGINT NOT NULL,
    id_produk INT NOT NULL,

    sku VARCHAR(100) NOT NULL,
    nama_produk VARCHAR(255) NOT NULL,
    variasi VARCHAR(255),

    quantity INT NOT NULL,
    harga_satuan DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2),

    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_transaksi) REFERENCES penjualan_transaksi(id_transaksi) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk),

    INDEX idx_transaksi (id_transaksi),
    INDEX idx_produk (id_produk),
    INDEX idx_sku (sku)
) ENGINE=InnoDB;

CREATE TABLE log_upload (
    id_upload INT AUTO_INCREMENT PRIMARY KEY,
    id_perusahaan INT NOT NULL,
    id_marketplace INT NOT NULL,
    id_pengguna INT NOT NULL,
    nama_file VARCHAR(255) NOT NULL,
    ukuran_file INT,
    total_baris INT DEFAULT 0,
    baris_sukses INT DEFAULT 0,
    baris_gagal INT DEFAULT 0,
    status_upload ENUM('proses','selesai','gagal') DEFAULT 'proses',
    pesan_error TEXT,
    tanggal_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_perusahaan) REFERENCES perusahaan(id_perusahaan) ON DELETE CASCADE,
    FOREIGN KEY (id_marketplace) REFERENCES marketplace(id_marketplace),
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna),
    INDEX idx_tanggal_upload (tanggal_upload),
    INDEX idx_perusahaan (id_perusahaan)
) ENGINE=InnoDB;

CREATE TABLE log_audit (
    id_audit BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_pengguna INT,
    id_perusahaan INT,
    jenis_aksi VARCHAR(50) NOT NULL,
    nama_tabel VARCHAR(100),
    id_record BIGINT,
    nilai_lama TEXT,
    nilai_baru TEXT,
    ip_address VARCHAR(45),
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pengguna (id_pengguna),
    INDEX idx_jenis_aksi (jenis_aksi),
    INDEX idx_dibuat_pada (dibuat_pada)
) ENGINE=InnoDB;

-- ============================================================
-- 2. STORED PROCEDURES (6)
-- ============================================================

DELIMITER $$

CREATE PROCEDURE sp_ambil_data_dashboard(
    IN p_id_perusahaan INT,
    IN p_tanggal_mulai DATE,
    IN p_tanggal_akhir DATE
)
BEGIN
    SELECT 
        COUNT(DISTINCT pt.id_transaksi) AS total_order,
        COALESCE(SUM(pt.pendapatan_bersih),0) AS pendapatan_bersih,
        COALESCE(SUM(ptd.quantity),0) AS total_item_terjual,
        COUNT(DISTINCT ptd.id_produk) AS produk_unik,
        CASE 
            WHEN COUNT(DISTINCT pt.id_transaksi) > 0
            THEN ROUND(SUM(pt.pendapatan_bersih) / COUNT(DISTINCT pt.id_transaksi),0)
            ELSE 0
        END AS rata_rata_nilai_order
    FROM penjualan_transaksi pt
    LEFT JOIN penjualan_transaksi_detail ptd 
        ON pt.id_transaksi = ptd.id_transaksi
    WHERE pt.id_perusahaan = p_id_perusahaan
      AND pt.status_order = 'selesai'
      AND pt.tanggal_order BETWEEN p_tanggal_mulai AND p_tanggal_akhir;
END$$

CREATE PROCEDURE sp_proses_upload(IN p_id_upload INT)
BEGIN
    DECLARE v_jumlah_sukses INT DEFAULT 0;

    SELECT COUNT(*) INTO v_jumlah_sukses
    FROM penjualan_transaksi
    WHERE id_batch_upload = p_id_upload;

    UPDATE log_upload
    SET baris_sukses = v_jumlah_sukses,
        status_upload = 'selesai'
    WHERE id_upload = p_id_upload;
END$$

CREATE PROCEDURE sp_perbandingan_marketplace(
    IN p_id_perusahaan INT,
    IN p_tahun INT,
    IN p_bulan INT
)
BEGIN
    SELECT 
        m.nama_marketplace,
        COUNT(pt.id_transaksi) AS total_order,
        SUM(ptd.quantity) AS item_terjual,
        SUM(pt.total_pesanan) AS pendapatan_kotor,
        SUM(pt.pendapatan_bersih) AS pendapatan_bersih,
        SUM(pt.biaya_komisi) AS komisi_dibayar,
        ROUND((SUM(pt.pendapatan_bersih) / SUM(pt.total_pesanan) * 100),2) AS profit_margin_persen
    FROM penjualan_transaksi pt
    JOIN marketplace m ON pt.id_marketplace = m.id_marketplace
    JOIN penjualan_transaksi_detail ptd ON pt.id_transaksi = ptd.id_transaksi
    WHERE pt.id_perusahaan = p_id_perusahaan
      AND YEAR(pt.tanggal_order) = p_tahun
      AND MONTH(pt.tanggal_order) = p_bulan
      AND pt.status_order = 'selesai'
    GROUP BY m.id_marketplace;
END$$

CREATE PROCEDURE sp_analisis_performa_produk(
    IN p_id_perusahaan INT,
    IN p_id_produk INT,
    IN p_tanggal_mulai DATE,
    IN p_tanggal_akhir DATE
)
BEGIN
    SELECT 
        pr.nama_produk,
        pr.sku,
        pr.kategori,
        COUNT(DISTINCT pt.id_transaksi) AS total_order,
        SUM(ptd.quantity) AS total_terjual,
        SUM(ptd.subtotal) AS total_pendapatan,
        ROUND(AVG(ptd.harga_satuan),2) AS rata_rata_harga_jual
    FROM penjualan_transaksi_detail ptd
    JOIN penjualan_transaksi pt ON ptd.id_transaksi = pt.id_transaksi
    JOIN produk pr ON ptd.id_produk = pr.id_produk
    WHERE pt.id_perusahaan = p_id_perusahaan
      AND ptd.id_produk = p_id_produk
      AND pt.tanggal_order BETWEEN p_tanggal_mulai AND p_tanggal_akhir
      AND pt.status_order = 'selesai'
    GROUP BY pr.id_produk;
END$$

CREATE PROCEDURE sp_produk_terlaris_per_periode(
    IN p_id_perusahaan INT,
    IN p_tanggal_mulai DATE,
    IN p_tanggal_akhir DATE,
    IN p_limit INT
)
BEGIN
    SELECT
        pr.id_produk,
        pr.nama_produk,
        pr.kategori,
        SUM(ptd.quantity) AS total_terjual,
        SUM(ptd.subtotal) AS total_pendapatan,
        COUNT(DISTINCT pt.id_transaksi) AS jumlah_transaksi
    FROM penjualan_transaksi pt
    JOIN penjualan_transaksi_detail ptd ON pt.id_transaksi = ptd.id_transaksi
    JOIN produk pr ON ptd.id_produk = pr.id_produk
    WHERE pt.id_perusahaan = p_id_perusahaan
      AND pt.status_order = 'selesai'
      AND pt.tanggal_order BETWEEN p_tanggal_mulai AND p_tanggal_akhir
    GROUP BY pr.id_produk
    ORDER BY total_terjual DESC
    LIMIT p_limit;
END$$

CREATE PROCEDURE sp_kinerja_produk_per_marketplace(
    IN p_id_perusahaan INT,
    IN p_tanggal_mulai DATE,
    IN p_tanggal_akhir DATE
)
BEGIN
    SELECT
        m.nama_marketplace,
        pr.id_produk,
        pr.nama_produk,
        SUM(ptd.quantity) AS total_terjual,
        SUM(ptd.subtotal) AS total_pendapatan,
        COUNT(DISTINCT pt.id_transaksi) AS jumlah_order
    FROM penjualan_transaksi pt
    JOIN penjualan_transaksi_detail ptd ON pt.id_transaksi = ptd.id_transaksi
    JOIN produk pr ON ptd.id_produk = pr.id_produk
    JOIN marketplace m ON pt.id_marketplace = m.id_marketplace
    WHERE pt.id_perusahaan = p_id_perusahaan
      AND pt.status_order = 'selesai'
      AND pt.tanggal_order BETWEEN p_tanggal_mulai AND p_tanggal_akhir
    GROUP BY m.id_marketplace, pr.id_produk;
END$$

DELIMITER ;

-- ============================================================
-- 3. FUNCTIONS
-- ============================================================

DELIMITER $$

CREATE FUNCTION fn_hitung_total_pendapatan(
    p_id_perusahaan INT,
    p_tanggal_mulai DATE,
    p_tanggal_akhir DATE
)
RETURNS DECIMAL(15,2)
DETERMINISTIC
BEGIN
    DECLARE v_total DECIMAL(15,2);
    SELECT COALESCE(SUM(pendapatan_bersih),0)
    INTO v_total
    FROM penjualan_transaksi
    WHERE id_perusahaan = p_id_perusahaan
      AND status_order = 'selesai'
      AND tanggal_order BETWEEN p_tanggal_mulai AND p_tanggal_akhir;
    RETURN v_total;
END$$

CREATE FUNCTION fn_persentase_share_marketplace(
    p_id_perusahaan INT,
    p_id_marketplace INT,
    p_tahun INT,
    p_bulan INT
)
RETURNS DECIMAL(5,2)
DETERMINISTIC
BEGIN
    DECLARE v_market DECIMAL(15,2);
    DECLARE v_total DECIMAL(15,2);

    SELECT COALESCE(SUM(pendapatan_bersih),0)
    INTO v_market
    FROM penjualan_transaksi
    WHERE id_perusahaan = p_id_perusahaan
      AND id_marketplace = p_id_marketplace
      AND YEAR(tanggal_order) = p_tahun
      AND MONTH(tanggal_order) = p_bulan
      AND status_order = 'selesai';

    SELECT COALESCE(SUM(pendapatan_bersih),0)
    INTO v_total
    FROM penjualan_transaksi
    WHERE id_perusahaan = p_id_perusahaan
      AND YEAR(tanggal_order) = p_tahun
      AND MONTH(tanggal_order) = p_bulan
      AND status_order = 'selesai';

    RETURN IF(v_total > 0, (v_market / v_total) * 100, 0);
END$$

CREATE FUNCTION fn_hitung_profit_margin(
    p_total_pesanan DECIMAL(15,2),
    p_pendapatan_bersih DECIMAL(15,2)
)
RETURNS DECIMAL(5,2)
DETERMINISTIC
BEGIN
    RETURN IF(p_total_pesanan > 0, (p_pendapatan_bersih / p_total_pesanan) * 100, 0);
END$$

DELIMITER ;

-- ============================================================
-- 4. TRIGGERS
-- ============================================================

DELIMITER $$

CREATE TRIGGER trg_validasi_penjualan_insert
BEFORE INSERT ON penjualan_transaksi
FOR EACH ROW
BEGIN
    IF NEW.total_pesanan <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Total pesanan harus lebih besar dari 0';
    END IF;

    IF NEW.pendapatan_bersih IS NULL THEN
        SET NEW.pendapatan_bersih =
            NEW.total_pesanan - NEW.total_diskon - NEW.biaya_komisi;
    END IF;
END$$

CREATE TRIGGER trg_validasi_detail_insert
BEFORE INSERT ON penjualan_transaksi_detail
FOR EACH ROW
BEGIN
    IF NEW.quantity <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Quantity harus lebih besar dari 0';
    END IF;

    IF NEW.subtotal IS NULL THEN
        SET NEW.subtotal = NEW.quantity * NEW.harga_satuan;
    END IF;
END$$

CREATE TRIGGER trg_audit_penjualan_insert
AFTER INSERT ON penjualan_transaksi
FOR EACH ROW
BEGIN
    INSERT INTO log_audit
    (id_perusahaan, jenis_aksi, nama_tabel, id_record, nilai_baru)
    VALUES
    (NEW.id_perusahaan, 'INSERT', 'penjualan_transaksi',
     NEW.id_transaksi, CONCAT('Order ', NEW.order_id));
END$$

CREATE TRIGGER trg_audit_penjualan_update
AFTER UPDATE ON penjualan_transaksi
FOR EACH ROW
BEGIN
    INSERT INTO log_audit
    (id_perusahaan, jenis_aksi, nama_tabel, id_record, nilai_lama, nilai_baru)
    VALUES
    (NEW.id_perusahaan, 'UPDATE', 'penjualan_transaksi',
     NEW.id_transaksi,
     OLD.status_order,
     NEW.status_order);
END$$

DELIMITER ;

-- ============================================================
-- 5. INDEX TAMBAHAN
-- ============================================================

CREATE INDEX idx_transaksi_perusahaan_tanggal_status
ON penjualan_transaksi(id_perusahaan, tanggal_order, status_order);

CREATE INDEX idx_transaksi_marketplace_tanggal
ON penjualan_transaksi(id_marketplace, tanggal_order);

CREATE INDEX idx_detail_transaksi_produk
ON penjualan_transaksi_detail(id_transaksi, id_produk);


ALTER TABLE log_upload
ADD COLUMN file_path VARCHAR(255) NOT NULL AFTER nama_file;


DELETE FROM PENGGUNA
