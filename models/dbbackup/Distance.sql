DELIMITER $$

DROP FUNCTION IF EXISTS `snackr_db`.`Distance`$$

CREATE FUNCTION `Distance`($coord1 VARCHAR(30), $coord2 VARCHAR(30)) RETURNS double
    DETERMINISTIC
BEGIN
	DECLARE lat1 double;
	DECLARE lon1 double;
	DECLARE lat2 double;
	DECLARE lon2 double;
	
	DECLARE R double;
	
	DECLARE f1 double;
	DECLARE f2 double;
	DECLARE df double;
	DECLARE dg double;
	
	DECLARE a double;
	DECLARE c double;
	DECLARE d double;
	SET lat1 = SPLIT_STRING($coord1,',',1);
	SET lon1 = SPLIT_STRING($coord1,',',2);
	SET lat2 = SPLIT_STRING($coord2,',',1);
	SET lon2 = SPLIT_STRING($coord2,',',2);
	SET R =  6371 * 0.62137;
	
	
	SET f1 = RADIANS(lat1);
	SET f2 = RADIANS(lat2);
	SET df = RADIANS(lat2-lat1);
	SET dg = RADIANS(lon2-lon1);
	
	
	SET a = SIN(df/2) * SIN(df/2) + COS(f1) * COS(f2) * SIN(dg/2) * SIN(dg/2);
        SET c = 2 * ATAN2(SQRT(a), SQRT(1-a));
	SET d = R * c;
            
	RETURN ROUND(d, 2);
    END$$

DELIMITER ;