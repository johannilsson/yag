# Todo: Fix references

drop table photos;
CREATE TABLE `photos` (
  `id` int(11) NOT NULL auto_increment,
  `file` varchar(100) default NULL,
  `title` varchar(100) default NULL,
  `description` varchar(255) default NULL,
  `taken_on` DATETIME default NULL,
  `created_on` DATETIME default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table photo_details;
CREATE TABLE `photo_details` (
  `photo_id` varchar(100) default NULL,
  `camera` varchar(100) default NULL,
  `longitude` varchar(100) default NULL,
  `latitude` varchar(100) default NULL,
  PRIMARY KEY  (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table albums;
CREATE TABLE `albums` (
  `id` int(11) NOT NULL auto_increment,
  `photo_id` int(11) default NULL,
  `name` varchar(100) default NULL,
  `title` varchar(100) default NULL,
  `description` varchar(255) default NULL,
  `created_on` DATETIME default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table albums_photos;
CREATE TABLE `albums_photos` (
  `album_id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL,
  PRIMARY KEY  (`album_id`,`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;