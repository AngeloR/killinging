--
-- MySQL 5.5.8
-- Fri, 26 Aug 2011 22:14:10 +0000
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
('1', 'Slubberdoffer', '3', '3', '2', '3', '6', '6', '1', '1', 'With the age of Zombies, the Slubberdoffers finally came into the spotlight. Their quick hands and extremely plucky outlook on life made them a rare commodity. Not to mention, they were the only ones able to make their own clothes.\n\nSlobberdoffers are very quick but not too strong. However, coupled with a high agility rating, they are definitely an integral part of the People Protectors. ', '0'),
('2', 'Hobo', '3', '4', '4', '3', '3', '1', '1', '1', 'When the zombie apocalypse finally arrived, the Hobo\'s finally found their place.\n\nThey stopped their eternal wars and decided to start attacking the zombies. Years of living on the streets of Towneville had hardened them. Years of begging meant that they knew where the humans would be. They were the first choice of the CPP. \n\nA force that doesn\'t know hunger or sleep. That knows exactly where the humans are. ', '0'),
('3', 'Snake Milker', '3', '4', '4', '6', '4', '1', '1', '1', 'Years of taking abuse from some of the worlds most venomous snakes, this zoo-hand-turned-people-protector is a force to be reckoned with. Years of very little walking and constantly being bitten by things has turned him into one of the toughest people protectors. \n\nOh yes. A Snake Milker tough. But not very lucky.', '0'),
('4', 'Barista', '3', '3', '6', '3', '3', '3', '1', '1', 'When the Zombies attacked, the Baristas attacked back. With reckless abandon.\n\nTired of dealing with uninfected humans they signed up in droves to become People Protectors. At first they were declined, but when the CPP caught them protecting people on their own time, they signed them up right away. \n\nThough they are not too fast and not too tough, the Barista\'s are very angry. And they gives them a strange strength. Rumor is, they also have no soul. ', '0');

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

CREATE TABLE `message` (
   `id` int(11) unsigned not null auto_increment,
   `fromuser` varchar(50),
   `touser` varchar(50),
   `text` varchar(255),
   `post_time` int(11) unsigned,
   `classification` tinyint(3) unsigned,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=38;

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
   `tin` int(11) default '1',
   `admin` int(11) default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=9;