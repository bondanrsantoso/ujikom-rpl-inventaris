DELIMITER $$
CREATE TRIGGER after_peminjaman_update
	AFTER UPDATE ON peminjaman
	FOR EACH ROW
BEGIN
	DECLARE I INT;
	DECLARE _ROWS INT;
	DECLARE _JUMLAH INT;
	DECLARE _ID INT;
	SELECT COUNT(*) INTO _ROWS FROM detail_pinjams WHERE id_peminjaman = NEW.id_peminjaman;
	SET I = 0;
	WHILE I < _ROWS DO
		SELECT jumlah INTO _JUMLAH FROM detail_pinjams WHERE id_peminjaman = NEW.id_peminjaman LIMIT 1 OFFSET I;
		SELECT id_inventaris INTO _ID  FROM detail_pinjams WHERE id_peminjaman = NEW.id_peminjaman LIMIT 1 OFFSET I;
		
		UPDATE inventaris SET jumlah = IF(NEW.kembali = 1, jumlah + _JUMLAH, jumlah - _JUMLAH) WHERE id_inventaris = _ID;
		SET I = I + 1;
	END WHILE;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER after_detail_pinjams_insert
	AFTER INSERT ON detail_pinjams
	FOR EACH ROW
BEGIN
	UPDATE inventaris SET inventaris.jumlah = (inventaris.jumlah - NEW.jumlah) WHERE inventaris.id_inventaris = NEW.id_inventaris;
END$$
DELIMITER ;