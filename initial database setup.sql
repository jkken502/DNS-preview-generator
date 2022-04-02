DROP FUNCTION IF EXISTS `RandString`;
DELIMITER $$
CREATE FUNCTION `RandString`(length SMALLINT(3)) RETURNS varchar(100) CHARSET utf8
begin
    SET @returnStr = '';
    SET @allowedChars = 'abcdefghijklmnopqrstuvwxyz';
    SET @i = 0;

    WHILE (@i < length) DO
        SET @returnStr = CONCAT(@returnStr, substring(@allowedChars, FLOOR(RAND() * LENGTH(@allowedChars) + 1), 1));
        SET @i = @i + 1;
    END WHILE;

    RETURN @returnStr;
END;$$
DELIMITER ;

select randstring(3);

drop table if exists hosts;
create table hosts(id int not null auto_increment primary key, createdAt timestamp not null default CURRENT_TIMESTAMP, subdomain varchar(8) not null unique , url varchar(2048) not null, ip varchar(15) not null);

drop trigger if exists hosts_beforeInsert;
DELIMITER $$
CREATE TRIGGER hosts_beforeInsert
  BEFORE INSERT ON `hosts`
  FOR EACH ROW
  BEGIN
    SET @hostsId = 1;
    WHILE (@hostsId IS NOT NULL) DO 
      SET NEW.subdomain = RANDSTRING(8);
      SET @hostsId = (SELECT id FROM `hosts` WHERE `subdomain` = NEW.subdomain);
    END WHILE;
  END;$$
DELIMITER ;
