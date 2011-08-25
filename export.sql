--
-- MySQL 5.5.8
-- Thu, 25 Aug 2011 21:57:37 +0000
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
   `mp` int(11),
   `vit` int(11),
   `str` int(11),
   `tough` int(11),
   `agi` int(11),
   `luck` int(11),
   `mining` int(11),
   `smithing` int(11),
   `description` text,
   `preform` int(11) default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=5;

INSERT INTO `class` (`id`, `name`, `mp`, `vit`, `str`, `tough`, `agi`, `luck`, `mining`, `smithing`, `description`, `preform`) VALUES 
('1', 'Wiccan', '', '', '', '', '', '', '', '', '', '0'),
('2', 'Warrior', '3', '4', '5', '6', '6', '15', '1', '1', 'When the zombie apocalypse finally arrived, the Hobo\'s finally found their place.\n\nThey stopped their eternal wars and decided to start attacking the zombies. Years of living on the streets of Towneville had hardened them. Years of begging meant that they knew where the humans would be. They were the first choice of the CPP. \n\nA force that doesn\'t know hunger or sleep. That knows exactly where the humans are. ', '0'),
('3', 'Seal Clubber', '', '', '', '', '', '', '', '', '', '0'),
('4', 'Nerd', '', '', '', '', '', '', '', '', '', '0');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=6;

INSERT INTO `message` (`id`, `from`, `text`, `post_time`, `classification`) VALUES 
('1', 'xangelo', 'heyo!', '1314225275', '1'),
('2', 'xangelo', 'test?', '1314225364', '1'),
('3', 'xangelo', 'test', '1314225368', '1'),
('4', 'xangelo', 'testing again', '1314225413', '1'),
('5', 'xangelo', 'test?', '1314294475', '1');

CREATE TABLE `monster` (
   `id` int(11) not null auto_increment,
   `name` varchar(100),
   `level` int(11) default '1',
   `current_hp` int(11),
   `vit` int(11),
   `str` int(11) default '1',
   `tough` int(11) default '1',
   `agi` int(11) default '1',
   `exp` int(11) default '5',
   `gold` int(11) default '0',
   `_method` varchar(255),
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=4;

INSERT INTO `monster` (`id`, `name`, `level`, `current_hp`, `vit`, `str`, `tough`, `agi`, `exp`, `gold`, `_method`) VALUES 
('1', 'Missingno', '10', '180', '12', '10', '10', '10', '200', '5', ''),
('2', 'Rat', '1', '16', '4', '2', '2', '4', '2', '1', ''),
('3', 'Vagabong', '3', '45', '5', '8', '6', '4', '12', '10', 'put');

CREATE TABLE `news` (
   `id` int(11) unsigned not null auto_increment,
   `title` text,
   `post_date` int(11) unsigned,
   `posted_by` varchar(255),
   `posted_by_id` tinyint(3) unsigned,
   `news` text,
   `approved` int(11) default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=5;

INSERT INTO `news` (`id`, `title`, `post_date`, `posted_by`, `posted_by_id`, `news`, `approved`) VALUES 
('4', 'Welcome', '1314304659', 'xangelo', '8', 'Welcome to the Rising Legends administration panel. From here you should be able to access most of the backend of the website. \n\nAt the moment the game is rather lacking in Monsters and Items. I would recommend that after you create a monster (assign it 0 gold and 0 exp) and then ask real players to test against it.\n\nAs for items, just keep in mind the price and the stat increases. \n\nAll your actions are logged, so if you do screw up... don\'t panic. Just email xangelo@gmail.com and let them know what happened.', '1');

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
   `tough` tinyint(3) unsigned,
   `agi` tinyint(3) unsigned,
   `luck` tinyint(3) unsigned,
   `vit` int(11),
   `mining` int(11),
   `smithing` int(11),
   `city` set('1'),
   `loc_x` tinyint(3) unsigned,
   `loc_y` tinyint(3) unsigned,
   `level` int(11) default '1',
   `current_exp` int(11) default '0',
   `skill_points` int(11) default '1',
   `gold` int(11) default '1000',
   `stone` int(11) default '5',
   `last_battled` tinyint(3) unsigned,
   `mining_exp` int(11) default '0',
   `copper` int(11) default '0',
   `admin` int(11) default '0',
   `coppyer` tinyint(3) unsigned,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=9;

INSERT INTO `player` (`id`, `username`, `password`, `email`, `class_id`, `total_hp`, `current_hp`, `total_mp`, `current_mp`, `str`, `tough`, `agi`, `luck`, `vit`, `mining`, `smithing`, `city`, `loc_x`, `loc_y`, `level`, `current_exp`, `skill_points`, `gold`, `stone`, `last_battled`, `mining_exp`, `copper`, `admin`, `coppyer`) VALUES 
('8', 'xangelo', '9d6f6dbca62962790cfca436a9c7c156436b2d46', 'xangelo@gmail.com', '2', '180', '180', '3', '3', '17', '10', '11', '15', '12', '2', '1', '1', '44', '53', '12', '378', '4', '1543', '181', '1', '59', '0', '1', '3');