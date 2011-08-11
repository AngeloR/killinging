--
-- MySQL 5.5.8
-- Thu, 11 Aug 2011 22:00:18 +0000
--

CREATE DATABASE `killinging` DEFAULT CHARSET utf8;

USE `killinging`;

CREATE TABLE `building` (
   `id` int(11) not null auto_increment,
   `building_type` int(11),
   `owner` int(11),
   `owner_type` varchar(10) default 'player',
   `map_id` int(11) default '1',
   `loc_x` int(11),
   `loc_y` int(11),
   `name` varchar(50),
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3;

INSERT INTO `building` (`id`, `building_type`, `owner`, `owner_type`, `map_id`, `loc_x`, `loc_y`, `name`) VALUES 
('1', '1', '8', 'player', '1', '49', '50', 'General Store'),
('2', '1', '9', 'player', '1', '43', '44', 'Your Store');

CREATE TABLE `building_type` (
   `id` int(11) not null auto_increment,
   `name` varchar(100),
   `cost` int(11) default '1000000',
   `time` int(11) default '12',
   `description` text,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;

INSERT INTO `building_type` (`id`, `name`, `cost`, `time`, `description`) VALUES 
('1', 'Store', '150000000', '12', 'This is the standard store. You can add/manage items that are present in it. Once an item is sold from your store it is removed from your store. \n\nFor every item sold you get ALL the profits. \n\nBuildings are very strong, but eventually zombies will find it and will start attacking your building, slowly destroying it. Eventually your building could be destroyed. To prevent this you should build up your defences and visit back at least once a day to repair any damages. ');

CREATE TABLE `city` (
   `id` int(11) not null auto_increment,
   `name` varchar(150),
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;

INSERT INTO `city` (`id`, `name`) VALUES 
('1', 'New South San Fresno');

CREATE TABLE `class` (
   `id` int(11) not null auto_increment,
   `name` varchar(50),
   `hp` int(11),
   `mp` int(11),
   `str` int(11),
   `def` int(11),
   `agi` int(11),
   `luck` int(11),
   `description` text,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=5;

INSERT INTO `class` (`id`, `name`, `hp`, `mp`, `str`, `def`, `agi`, `luck`, `description`) VALUES 
('1', 'Wiccan', '', '', '', '', '', '', ''),
('2', 'Hobo', '16', '3', '4', '6', '6', '15', 'When the zombie apocalypse finally arrived, the Hobo\'s finally found their place.\n\nThey stopped their eternal wars and decided to start attacking the zombies. Years of living on the streets of Towneville had hardened them. Years of begging meant that they knew where the humans would be. They were the first choice of the CPP. \n\nA force that doesn\'t know hunger or sleep. That knows exactly where the humans are. '),
('3', 'Seal Clubber', '', '', '', '', '', '', ''),
('4', 'Nerd', '', '', '', '', '', '', '');

CREATE TABLE `item` (
   `id` int(11) not null auto_increment,
   `name` varchar(150),
   `cost` int(11),
   `level` int(11) default '1',
   `str` int(11),
   `def` int(11),
   `agi` int(11),
   `luck` int(11),
   `store_id` int(11) default '1',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;

INSERT INTO `item` (`id`, `name`, `cost`, `level`, `str`, `def`, `agi`, `luck`, `store_id`) VALUES 
('1', 'Old Clothes', '10', '1', '0', '2', '0', '0', '1');

CREATE TABLE `monster` (
   `id` int(11) not null auto_increment,
   `name` varchar(100),
   `level` int(11) default '1',
   `current_hp` int(11),
   `str` int(11) default '1',
   `def` int(11) default '1',
   `agi` int(11) default '1',
   `exp` int(11) default '5',
   `gold` int(11) default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;

INSERT INTO `monster` (`id`, `name`, `level`, `current_hp`, `str`, `def`, `agi`, `exp`, `gold`) VALUES 
('1', 'Monster', '1', '7', '1', '1', '1', '2', '2');

CREATE TABLE `player` (
   `id` int(11) unsigned not null auto_increment,
   `username` varchar(255),
   `password` varchar(255),
   `email` varchar(255),
   `class_id` tinyint(3) unsigned,
   `total_hp` tinyint(3) unsigned,
   `current_hp` double,
   `total_mp` tinyint(3) unsigned,
   `current_mp` tinyint(3) unsigned,
   `str` tinyint(3) unsigned,
   `def` tinyint(3) unsigned,
   `agi` tinyint(3) unsigned,
   `luck` tinyint(3) unsigned,
   `city` set('1'),
   `loc_x` tinyint(3) unsigned,
   `loc_y` tinyint(3) unsigned,
   `level` int(11) default '1',
   `current_exp` int(11) default '0',
   `gold` int(11) default '1000',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=9;

INSERT INTO `player` (`id`, `username`, `password`, `email`, `class_id`, `total_hp`, `current_hp`, `total_mp`, `current_mp`, `str`, `def`, `agi`, `luck`, `city`, `loc_x`, `loc_y`, `level`, `current_exp`, `gold`) VALUES 
('8', 'xangelo', '9d6f6dbca62962790cfca436a9c7c156436b2d46', 'xangelo@gmail.com', '2', '16', '16', '3', '3', '4', '6', '6', '15', '1', '51', '50', '3', '16', '804');