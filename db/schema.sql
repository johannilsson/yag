CREATE DATABASE IF NOT EXISTS `yag` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE yag

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS _VERSION_1_0_1;
CREATE TABLE `_VERSION_1_0_1` (
  `dummy` varchar(1) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS photos;
CREATE TABLE `photos` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) default NULL,
  `clean_title` varchar(100) default NULL,
  `description` varchar(255) default NULL,
  `image` varchar(100) default NULL,
  `image_filesize` int default NULL,
  `image_mime_type` varchar(20) default NULL,
  `make` varchar(100) default NULL,
  `model` varchar(100) default NULL,
  `exposure` varchar(100) default NULL,
  `focal_length` varchar(100) default NULL,
  `shutter_speed` varchar(100) default NULL,
  `aperture` varchar(100) default NULL,
  `iso_speed` int default NULL,
  `white_balance` varchar(100) default NULL,
  `flash` int default NULL,
  `longitude` float(10,6) default NULL,
  `latitude` float(10,6) default NULL,
  `taken_on` DATETIME default NULL,
  `created_on` DATETIME default NULL,
  `updated_on` DATETIME default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS tags;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `clean_name` varchar(50) default NULL,
  `created_on` DATETIME default NULL,  
  `updated_on` DATETIME default NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS tagged_photos;
CREATE TABLE `tagged_photos` (
  `tag_id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL,
  PRIMARY KEY  (`tag_id`,`photo_id`),
  CONSTRAINT `fk_tags_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`),
  CONSTRAINT `fk_photos_photos_id` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS=1
