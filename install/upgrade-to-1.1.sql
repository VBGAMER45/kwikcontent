CREATE TABLE `youtube_video` (
  `ytv_id` mediumint(8) unsigned  auto_increment,
  `ytv_videoid` varchar(255)  NULL default '',
  `ytv_videotitle` varchar(255)  NULL default '',
  `ytv_videodescription` text,
  `ytv_videothumburl` varchar(255)  NULL default '',
  `key_id` mediumint(8) unsigned  NULL default '0',
  `ytv_rank` smallint(5) unsigned  NULL default '0',
  PRIMARY KEY  (`ytv_id`),
  KEY `ylk_url` (`ytv_id`,`ytv_videoid`)
) ENGINE=MyISAM ;

CREATE TABLE `bing_news` (
  `bn_id` mediumint(8) unsigned  auto_increment,
  `bn_url` varchar(255)  default '',
  `bn_title` varchar(255)  default '',
  `bn_snippet` text,
  `bn_source` varchar(255)  default '',
  `key_id` mediumint(8) unsigned  default '0',
  `bn_rank` smallint(5) unsigned  default '0',
  `bn_time_p` int(10) unsigned  default '0',
  PRIMARY KEY  (`bn_id`)

) ENGINE=MyISAM ;

ALTER TABLE `domain` ADD  `bing_app_id` varchar(150)  default '';

CREATE TABLE `sites` (
  `id` int(11)  auto_increment,
  `domain` varchar(255)  default '',
  `date` int(11)  default '0',
  `description` longtext ,
  `hits` int(11)  default '0',
  removed tinyint(1) default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=MyISAM;