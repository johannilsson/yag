CREATE TABLE `_VERSION_1_0_1` (
  `dummy` varchar(1) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE photos ADD clean_title VARCHAR(100) AFTER title;
UPDATE photos SET clean_title = title;

ALTER TABLE tags ADD clean_name VARCHAR(50) AFTER name;
UPDATE tags SET clean_name = name;
