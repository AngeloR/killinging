--
-- MySQL 5.5.8
-- Fri, 14 Oct 2011 22:12:38 +0000
--

CREATE TABLE `building` (
   `id` int(11) not null auto_increment,
   `building_type` int(11),
   `owner` int(11),
   `owner_type` varchar(10) default 'player',
   `zone_id` int(11) default '1',
   `loc_x` int(11),
   `loc_y` int(11),
   `name` varchar(50),
   `level` int(11) default '1',
   `cost` int(11),
   `stone` int(11),
   PRIMARY KEY (`id`),
   KEY `map_id` (`zone_id`,`loc_x`,`loc_y`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=7;

INSERT INTO `building` (`id`, `building_type`, `owner`, `owner_type`, `zone_id`, `loc_x`, `loc_y`, `name`, `level`, `cost`, `stone`) VALUES 
('1', '1', '1', 'player', '1', '49', '50', 'General Store', '1', '150000000', '100000'),
('2', '1', '1', 'player', '1', '43', '44', 'Your Store', '1', '150000000', '100000'),
('3', '3', '1', 'player', '1', '47', '53', 'Angelo\'s Quarry', '1', '125000000', '0'),
('4', '5', '1', 'player', '1', '46', '50', 'Bank by xangelo', '1', '2000000000', '250000000'),
('5', '4', '1', 'player', '1', '47', '50', 'Tavern by xangelo', '1', '10000000', '100000'),
('6', '2', '1', 'player', '1', '48', '50', 'General', '1', '25000000', '10000000');

CREATE TABLE `building_type` (
   `id` int(11) not null auto_increment,
   `name` varchar(100),
   `cost` int(11) default '1000000',
   `time` int(11) default '12',
   `description` text,
   `stone` int(11),
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=6;

INSERT INTO `building_type` (`id`, `name`, `cost`, `time`, `description`, `stone`) VALUES 
('1', 'Store', '150000000', '12', 'This is the standard store. You can add/manage items that are present in it. Once an item is sold from your store it is removed from your store. \n\nFor every item sold you get ALL the profits. \n\nBuildings are very strong, but eventually zombies will find it and will start attacking your building, slowly destroying it. Eventually your building could be destroyed. To prevent this you should build up your defences and visit back at least once a day to repair any damages. ', '100000'),
('2', 'Crafting Hall', '25000000', '12', 'A crafting allows you to craft your own items. You can use a crafting hall you don\'t own, but your chances of random bonuses are close to zero. As well, selling crafted items in a crafting hall you don\'t own will result in  a portion of the sale being taken as a \"tax\", which can be set at each individual crafting hall.', '100000'),
('3', 'Quarry', '125000000', '6', 'A quarry will allow you to mine. While you can gain stone resources from just walking about, a mine will guarantee you stone resource every time you \"mine\" it. As well, depending on your mining skill level, you have a chance to find more precious metals that can be be used in crafting better weapons, armor and accessories.', '0'),
('4', 'Tavern', '10000000', '6', 'Taverns allow you to heal a percentage of your health for a set cost. When you build it, it will heal you for 50% of your health. For each level, it heals you for an additional 5%. ', '100000'),
('5', 'Bank', '2000000000', '12', 'Banks allow users to store their money. Once built you can set a daily \"tax\" limit. This tax rate will apply the lowest tax-rate of the day to all money stored in the bank.\n\nThis will allow you to make some daily money from the banks.', '250000000');

CREATE TABLE `city` (
   `id` int(11) not null auto_increment,
   `name` varchar(150),
   `min_x` int(11),
   `min_y` int(11),
   `max_x` int(11),
   `max_y` int(11),
   `zone` int(11) default '1',
   `buildings` set('1'),
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3;

INSERT INTO `city` (`id`, `name`, `min_x`, `min_y`, `max_x`, `max_y`, `zone`, `buildings`) VALUES 
('1', 'Wilderness', '0', '0', '0', '0', '1', ''),
('2', 'Aberfoyle', '43', '43', '53', '53', '1', '');

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

CREATE TABLE `crafting_recipe` (
   `id` int(11) not null auto_increment,
   `level` int(11),
   `name` varchar(25),
   `copper` int(11),
   `tin` int(11),
   `bronze` int(11),
   `iron` int(11),
   `cast_iron` int(11),
   `exp` int(11) default '1',
   `vary` tinyint(4) default '1',
   `total_hp` int(11) default '0',
   `total_mp` int(11) default '0',
   `str` int(11) default '0',
   `tough` int(11) default '0',
   `agi` int(11) default '0',
   `luck` int(11) default '0',
   `vit` int(11) default '0',
   `cost` int(11) default '0',
   `type` varchar(10),
   `icon` varchar(50),
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=8;

INSERT INTO `crafting_recipe` (`id`, `level`, `name`, `copper`, `tin`, `bronze`, `iron`, `cast_iron`, `exp`, `vary`, `total_hp`, `total_mp`, `str`, `tough`, `agi`, `luck`, `vit`, `cost`, `type`, `icon`) VALUES 
('1', '1', 'Bronze Bar', '2', '2', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'bar_bronze', 'bronze_bar.png'),
('2', '1', 'Bronze Dagger', '0', '0', '2', '0', '0', '5', '1', '0', '0', '2', '0', '0', '0', '0', '0', 'weapon', 'bronze_dagger.png'),
('3', '1', 'Bronze Longsword', '0', '0', '10', '0', '0', '10', '1', '0', '0', '5', '0', '0', '0', '0', '0', 'weapon', 'bronze_sword.png'),
('4', '2', 'Bronze Sabre', '0', '0', '6', '0', '0', '12', '1', '0', '0', '7', '0', '0', '0', '0', '0', 'weapon', ''),
('5', '1', 'Bronze Sallet', '0', '0', '3', '0', '0', '3', '1', '0', '0', '0', '1', '0', '0', '0', '0', 'helm', ''),
('6', '1', 'Bronze Greathelm', '0', '0', '4', '0', '0', '5', '1', '0', '0', '0', '3', '0', '0', '0', '0', 'helm', ''),
('7', '2', 'Bronze Burgonet', '0', '0', '5', '0', '0', '8', '1', '0', '0', '0', '5', '0', '0', '0', '0', 'helm', '');

CREATE TABLE `helm` (
   `id` int(11) unsigned not null auto_increment,
   `str` tinyint(3) unsigned,
   `tough` tinyint(3) unsigned,
   `agi` tinyint(3) unsigned,
   `luck` tinyint(3) unsigned,
   `vit` tinyint(3) unsigned,
   `name` varchar(255),
   `owner` set('1'),
   `cost` tinyint(3) unsigned,
   `level` tinyint(3) unsigned,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=6;

INSERT INTO `helm` (`id`, `str`, `tough`, `agi`, `luck`, `vit`, `name`, `owner`, `cost`, `level`) VALUES 
('3', '0', '3', '0', '0', '0', 'Regular Bronze Greathelm', '1', '0', '1'),
('4', '0', '3', '0', '0', '0', 'Regular Bronze Greathelm', '1', '0', '1'),
('5', '0', '3', '0', '0', '0', 'Excellent Bronze Greathelm', '1', '0', '1');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- [Table `item` is empty]

CREATE TABLE `message` (
   `id` int(11) unsigned not null auto_increment,
   `fromuser` varchar(50),
   `touser` varchar(50),
   `text` varchar(255),
   `post_time` int(11) unsigned,
   `classification` tinyint(3) unsigned,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=29;

INSERT INTO `message` (`id`, `fromuser`, `touser`, `text`, `post_time`, `classification`) VALUES 
('1', 'Server', '', 'xangelo has logged in.', '1316718864', '2'),
('2', 'Server', 'xangelo', 'Your message could not be sent to Server', '1316721257', '2'),
('3', 'Server', 'xangelo', 'User \'Server\' does nto exist.', '1316721657', '2'),
('4', 'Server', 'xangelo', 'User \'Server\' does noo exist.', '1316721677', '2'),
('5', 'Server', 'xangelo', 'User \'Server\' does not exist.', '1316721785', '2'),
('6', 'Server', 'xangelo', 'A report has been filed! Your case is: #1', '1316721831', '2'),
('7', 'Server', 'xangelo', 'A report has been filed! Your case is: #2', '1316721965', '2'),
('8', 'Server', '', 'xangelo has logged in.', '1316721993', '2'),
('9', 'xangelo', 'xangelo', 'test?', '1316722840', '0'),
('10', 'Server', '', 'xangelo has logged in.', '1317155052', '2'),
('11', 'Server', '', 'xangelo has logged in.', '1317156174', '2'),
('12', 'Server', '', 'xangelo has logged in.', '1318350599', '2'),
('13', 'Server', '', 'xangelo has logged in.', '1318358945', '2'),
('14', 'xangelo', '', 'ahoi', '1318361497', '1'),
('15', 'xangelo', '', 'yeah, I\'m adding some monsters to the game right now.. trying to balance everything', '1318362929', '1'),
('16', 'Server', 'xangelo', 'A report has been filed! Your case is: #3', '1318363622', '2'),
('17', 'xangelo', '', 'huzah!', '1318363654', '1'),
('18', 'xangelo', '', 'Hey, just testing', '1318363654', '2'),
('19', 'Server', '', 'xangelo has logged in.', '1318604709', '2'),
('20', 'xangelo', '', 'hey?', '1318607324', '1'),
('21', 'xangelo', '', 'just testing to see if the chat feature works properly...', '1318607335', '1'),
('22', 'xangelo', '', '/pm xangelo yes!', '1318607339', '1'),
('23', 'xangelo', 'xangelo', 'yes?', '1318607343', '0'),
('24', 'xangelo', 'xangelo', 'worked!', '1318607349', '0'),
('25', 'Server', '', 'xangelo has reached mining level 5!', '1318608858', '2'),
('26', 'Server', '', 'xangelo has logged in.', '1318618714', '2'),
('27', 'Server', '', 'xangelo has logged in.', '1318619106', '2'),
('28', 'Server', '', 'xangelo has logged in.', '1318619128', '2');

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
   `city` int(11) default '1',
   `min_x` int(11),
   `min_y` int(11),
   `max_x` int(11),
   `max_y` int(11),
   `message` set('1'),
   `luck` tinyint(3) unsigned,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=9;

INSERT INTO `monster` (`id`, `name`, `level`, `current_hp`, `vit`, `str`, `tough`, `agi`, `exp`, `gold`, `_method`, `city`, `min_x`, `min_y`, `max_x`, `max_y`, `message`, `luck`) VALUES 
('1', 'Rat', '1', '12', '4', '2', '2', '4', '2', '1', '', '2', '40', '40', '50', '55', '', '1'),
('2', 'Vagabond', '2', '18', '3', '3', '4', '4', '12', '10', 'put', '2', '45', '45', '60', '60', '', '1'),
('3', 'Ruffian', '3', '30', '4', '4', '5', '6', '22', '15', 'put', '2', '35', '35', '60', '60', '', '1'),
('4', 'Bandit', '4', '38', '5', '6', '5', '6', '30', '17', 'put', '1', '50', '50', '70', '70', '', '2'),
('5', 'Wolf', '5', '45', '5', '6', '6', '6', '30', '27', '', '1', '30', '30', '65', '65', '', '2'),
('6', 'Bear yearling', '5', '54', '6', '6', '6', '7', '35', '12', '', '1', '30', '30', '65', '65', '', '1'),
('7', 'Black Bear', '6', '63', '7', '7', '6', '7', '50', '6', '', '1', '43', '43', '53', '53', '', '7'),
('8', 'Stray Dog', '3', '9', '3', '3', '2', '4', '28', '10', '', '2', '43', '43', '53', '53', '', '2');

CREATE TABLE `news` (
   `id` int(11) unsigned not null auto_increment,
   `title` text,
   `post_date` int(11) unsigned,
   `posted_by` varchar(255),
   `posted_by_id` tinyint(3) unsigned,
   `news` text,
   `approved` int(11) default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- [Table `news` is empty]

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
   `crafting` int(11),
   `zone` int(11) default '1',
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
   `bronze` int(11),
   `admin` int(11) default '0',
   `class_name` varchar(25),
   `city` tinyint(3) unsigned,
   `iron` tinyint(3) unsigned,
   `cast_iron` tinyint(3) unsigned,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;

INSERT INTO `player` (`id`, `username`, `password`, `email`, `class_id`, `total_hp`, `current_hp`, `total_mp`, `current_mp`, `str`, `tough`, `agi`, `luck`, `vit`, `mining`, `crafting`, `zone`, `loc_x`, `loc_y`, `level`, `current_exp`, `skill_points`, `gold`, `stone`, `last_battled`, `mining_exp`, `copper`, `tin`, `bronze`, `admin`, `class_name`, `city`, `iron`, `cast_iron`) VALUES 
('1', 'xangelo', '9d6f6dbca62962790cfca436a9c7c156436b2d46', 'xangelo@gmail.com', '4', '36', '36', '3', '3', '10', '8', '4', '3', '3', '6', '1', '1', '48', '50', '10', '59', '0', '423', '2463', '1', '17', '599', '565', '22', '1', 'Barista', '2', '0', '0');

CREATE TABLE `report` (
   `id` int(11) unsigned not null auto_increment,
   `fromuser` varchar(255),
   `message` varchar(255),
   `post_time` int(11) unsigned,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=4;

INSERT INTO `report` (`id`, `fromuser`, `message`, `post_time`) VALUES 
('1', 'xangelo', '', '1316721831'),
('2', 'xangelo', 'Man.. another botter', '1316721965'),
('3', 'xangelo', 'hey, there\'s something weird going on :S ', '1318363622');

CREATE TABLE `transaction` (
   `id` int(11) unsigned not null auto_increment,
   `player` tinyint(3) unsigned,
   `gold` int(11) unsigned,
   `bank_id` tinyint(3) unsigned,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;

INSERT INTO `transaction` (`id`, `player`, `gold`, `bank_id`) VALUES 
('1', '1', '1271', '4');

CREATE TABLE `weapon` (
   `id` int(11) unsigned not null auto_increment,
   `str` tinyint(3) unsigned,
   `tough` tinyint(3) unsigned,
   `agi` tinyint(3) unsigned,
   `luck` tinyint(3) unsigned,
   `vit` tinyint(3) unsigned,
   `name` varchar(255),
   `owner` set('1'),
   `cost` tinyint(3) unsigned,
   `level` set('1'),
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;

INSERT INTO `weapon` (`id`, `str`, `tough`, `agi`, `luck`, `vit`, `name`, `owner`, `cost`, `level`) VALUES 
('1', '2', '0', '0', '0', '0', 'Excellent Bronze Dagger', '1', '0', '1');