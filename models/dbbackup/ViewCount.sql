DELIMITER $$

DROP FUNCTION IF EXISTS `snackr_db`.`ViewCount`$$

CREATE FUNCTION `snackr_db`.`ViewCount`($dish_id BIGINT) RETURNS INT
    DETERMINISTIC
    BEGIN
	DECLARE cnt INT;
	SET cnt = (SELECT COUNT(id) FROM tbl_treatment WHERE dish_id=$dish_id);
	RETURN cnt;
    END$$

DELIMITER ;