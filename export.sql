--
-- MySQL 5.5.8
-- Wed, 24 Aug 2011 22:37:26 +0000
--

CREATE TABLE `building` (
   `id` int(11) not null auto_increment,
   `building_type` int(11),
   `owner` int(11),
   `owner_type` varchar(10) default 'player',
   `map_id` int(11) default '1',
   `loc_x` int(11),
   `loc_y` int(11),
   `name` varchar(50),
   `level` int(11) default '1',
   `cost` int(11),
   `stone` int(11),
   PRIMARY KEY (`id`),
   KEY `map_id` (`map_id`,`loc_x`,`loc_y`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=4;

INSERT INTO `building` (`id`, `building_type`, `owner`, `owner_type`, `map_id`, `loc_x`, `loc_y`, `name`, `level`, `cost`, `stone`) VALUES 
('1', '1', '8', 'player', '1', '49', '50', 'General Store', '1', '150000000', '100000'),
('2', '1', '9', 'player', '1', '43', '44', 'Your Store', '1', '150000000', '100000'),
('3', '3', '8', 'player', '1', '47', '53', 'Angelo\'s Quarry', '1', '125000000', '0');

CREATE TABLE `building_type` (
   `id` int(11) not null auto_increment,
   `name` varchar(100),
   `cost` int(11) default '1000000',
   `time` int(11) default '12',
   `description` text,
   `stone` int(11),
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=4;

INSERT INTO `building_type` (`id`, `name`, `cost`, `time`, `description`, `stone`) VALUES 
('1', 'Store', '150000000', '12', 'This is the standard store. You can add/manage items that are present in it. Once an item is sold from your store it is removed from your store. \n\nFor every item sold you get ALL the profits. \n\nBuildings are very strong, but eventually zombies will find it and will start attacking your building, slowly destroying it. Eventually your building could be destroyed. To prevent this you should build up your defences and visit back at least once a day to repair any damages. ', '100000'),
('2', 'Crafting Hall', '25000000', '12', 'A crafting allows you to craft your own items. You can use a crafting hall you don\'t own, but your chances of random bonuses are close to zero. As well, selling crafted items in a crafting hall you don\'t own will result in  a portion of the sale being taken as a \"tax\", which can be set at each individual crafting hall.', '100000'),
('3', 'Quarry', '125000000', '6', 'A quarry will allow you to mine. While you can gain stone resources from just walking about, a mine will guarantee you stone resource every time you \"mine\" it. As well, depending on your mining skill level, you have a chance to find more precious metals that can be be used in crafting better weapons, armor and accessories.', '0');

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
   `mining` int(11),
   `smithing` int(11),
   `description` text,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=5;

INSERT INTO `class` (`id`, `name`, `hp`, `mp`, `str`, `def`, `agi`, `luck`, `mining`, `smithing`, `description`) VALUES 
('1', 'Wiccan', '', '', '', '', '', '', '', '', ''),
('2', 'Hobo', '16', '3', '4', '6', '6', '15', '1', '1', 'When the zombie apocalypse finally arrived, the Hobo\'s finally found their place.\n\nThey stopped their eternal wars and decided to start attacking the zombies. Years of living on the streets of Towneville had hardened them. Years of begging meant that they knew where the humans would be. They were the first choice of the CPP. \n\nA force that doesn\'t know hunger or sleep. That knows exactly where the humans are. '),
('3', 'Seal Clubber', '', '', '', '', '', '', '', '', ''),
('4', 'Nerd', '', '', '', '', '', '', '', '', '');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=7;

INSERT INTO `item` (`id`, `name`, `cost`, `level`, `str`, `def`, `agi`, `luck`, `store_id`) VALUES 
('4', 'Old Sword', '25', '1', '2', '0', '0', '0', '1'),
('5', 'Old Sword', '25', '1', '2', '0', '0', '0', '1'),
('6', 'Old Sword', '25', '1', '2', '0', '0', '0', '1');

CREATE TABLE `message` (
   `id` int(11) unsigned not null auto_increment,
   `from` varchar(50),
   `text` varchar(255),
   `post_time` int(11) unsigned,
   `classification` tinyint(3) unsigned,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=5;

INSERT INTO `message` (`id`, `from`, `text`, `post_time`, `classification`) VALUES 
('1', 'xangelo', 'heyo!', '1314225275', '1'),
('2', 'xangelo', 'test?', '1314225364', '1'),
('3', 'xangelo', 'test', '1314225368', '1'),
('4', 'xangelo', 'testing again', '1314225413', '1');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3;

INSERT INTO `monster` (`id`, `name`, `level`, `current_hp`, `str`, `def`, `agi`, `exp`, `gold`) VALUES 
('1', 'Monster', '1', '25', '1', '1', '1', '2', '2'),
('2', 'Rat', '1', '16', '2', '2', '4', '2', '1');

CREATE TABLE `owned_item` (
   `id` int(11) unsigned not null auto_increment,
   `name` varchar(255),
   `cost` double,
   `level` set('1'),
   `str` tinyint(3) unsigned,
   `def` tinyint(3) unsigned,
   `agi` tinyint(3) unsigned,
   `luck` tinyint(3) unsigned,
   `store_id` set('1'),
   `owner` tinyint(3) unsigned,
   `equipped` tinyint(3) unsigned,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- [Table `owned_item` is empty]

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
   `mining` int(11),
   `smithing` int(11),
   `city` set('1'),
   `loc_x` tinyint(3) unsigned,
   `loc_y` tinyint(3) unsigned,
   `level` int(11) default '1',
   `current_exp` int(11) default '0',
   `gold` int(11) default '1000',
   `stone` int(11) default '5',
   `last_battled` tinyint(3) unsigned,
   `mining_exp` int(11) default '0',
   `copper` set('1'),
   `coppyer` tinyint(3) unsigned,
   `admin` int(11) default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=9;

INSERT INTO `player` (`id`, `username`, `password`, `email`, `class_id`, `total_hp`, `current_hp`, `total_mp`, `current_mp`, `str`, `def`, `agi`, `luck`, `mining`, `smithing`, `city`, `loc_x`, `loc_y`, `level`, `current_exp`, `gold`, `stone`, `last_battled`, `mining_exp`, `copper`, `coppyer`, `admin`) VALUES 
('8', 'xangelo', '9d6f6dbca62962790cfca436a9c7c156436b2d46', 'xangelo@gmail.com', '2', '16', '16', '3', '3', '4', '6', '6', '15', '2', '1', '1', '47', '52', '4', '35', '1019', '164', '2', '45', '', '6', '1');