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

drop table geo_tags;
CREATE TABLE `geo_tags` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `longitude` float(10,6) default NULL,
  `latitude` float(10,6) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE uq_long_lati (`longitude`, `latitude`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

drop table photo_details;
CREATE TABLE `photo_details` (
  `photo_id` int(11) NOT NULL,
  `geo_tag_id` int(11) NULL,
  `file_name` varchar(100) default NULL,
  `file_date_time` varchar(100) default NULL,
  `file_size` varchar(100) default NULL,
  `mime_type` varchar(100) default NULL,
  `make` varchar(100) default NULL,
  `model` varchar(100) default NULL,
  `orientation` varchar(100) default NULL,
#  `x_resolution` varchar(100) default NULL,
#  `y_resolution` varchar(100) default NULL,
#  `resolution_unit` varchar(100) default NULL,
#  `y_cb_cr_positioning` varchar(100) default NULL,
  `exposure_time` varchar(100) default NULL,
  `f_number` varchar(100) default NULL,
  `iso_speed_ratings` varchar(100) default NULL,
  `date_time_original` varchar(100) default NULL,
  `date_time_digitized` varchar(100) default NULL,
  `shutter_speed_value` varchar(100) default NULL,
  `aperture_value` varchar(100) default NULL,
  `light_source` varchar(100) default NULL,
  `flash` varchar(100) default NULL,
#  `flash_pix_version` varchar(100) default NULL,
#  `color_space` varchar(100) default NULL,
  `image_width` varchar(100) default NULL,
  `image_length` varchar(100) default NULL,
#  `custom_rendered` varchar(100) default NULL,
  `exposure_mode` varchar(100) default NULL,
  `white_balance` varchar(100) default NULL,
  `digital_zoom_ratio` varchar(100) default NULL,
  `scene_capture_type` varchar(100) default NULL,
  `gain_control` varchar(100) default NULL,
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

#drop table geo_tagged_photos;
#CREATE TABLE `geo_tagged_photos` (
#  `geo_tag_id` int(11) NOT NULL,
#  `photo_id` int(11) NOT NULL,
#  PRIMARY KEY  (`geo_tag_id`,`photo_id`)
#) ENGINE=InnoDB DEFAULT CHARSET=utf8;
