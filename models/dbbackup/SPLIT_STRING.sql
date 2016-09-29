--
-- split function
--    s   : string to split
--    del : delimiter
--    i   : index requested
--

DROP FUNCTION IF EXISTS SPLIT_STRING;

DELIMITER $

CREATE FUNCTION 
   SPLIT_STRING ( s VARCHAR(1024) , del CHAR(1) , i INT)
   RETURNS VARCHAR(1024)
   DETERMINISTIC -- always returns same results for same input parameters
    BEGIN

        DECLARE n INT ;

        -- get max number of items
        SET n = LENGTH(s) - LENGTH(REPLACE(s, del, '')) + 1;

        IF i > n THEN
            RETURN NULL ;
        ELSE
            RETURN SUBSTRING_INDEX(SUBSTRING_INDEX(s, del, i) , del , -1 ) ;        
        END IF;

    END
$

DELIMITER ;