--
-- Table structure for table `article`
--

CREATE TABLE `%dbprefix%article` (
  `article_id` int(10) unsigned  auto_increment,
  `key_id` int(10) unsigned  default '0',
  `author_id` int(10) unsigned  default '0',
  `article_rank` mediumint(8) unsigned  default '0',
  `article_title` varchar(255)  default '',
  `article_teaser` text ,
  `article_body` text ,
  `article_biblio` text ,
  `article_meta_desc` text ,
  `article_meta_keys` text ,
  PRIMARY KEY  (`article_id`),
  KEY `author_id` (`author_id`),
  KEY `key_id` (`key_id`)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

CREATE TABLE `%dbprefix%author` (
  `author_id` int(10) unsigned  auto_increment,
  `author_name` varchar(150)  default '',
  `author_url` varchar(255)  default '',
  PRIMARY KEY  (`author_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `domain`
--

CREATE TABLE `%dbprefix%domain` (
  `domain_id` char(1)  default '1',
  `domain_name` varchar(255) ,
  `domain_title` varchar(255)  default '',
  `domain_window_titles` enum('0','1')  default '1',
  `domain_home_text` text ,
  `domain_home_meta_desc` varchar(255)  default '',
  `hide_public_errors` enum('0','1')  default '0',
  `yahoo_app_id` varchar(150)  default '',
  `yahoo_auto_update` enum('1','0')  default '1',
  `yahoo_auto_update_frequency` tinyint(3) unsigned  default '7',
  `yahoo_news_update_frequency` mediumint(8) unsigned  default '4',
  `yahoo_news_display_total` mediumint(8) unsigned  default '50',
  `admin_wysiwyg` enum('1','0')  default '1',
  `preview_articles` mediumint(8) unsigned  default '3',
  `preview_y_images` tinyint(3) unsigned  default '5',
  `preview_y_qas` tinyint(3) unsigned  default '6',
  `preview_y_news` tinyint(5) unsigned  default '5',
  `google_search` enum('0','1')  default '1',
  `google_analytics` text ,
  `google_ad_client` varchar(80)  default '',
  `google_ad_channel` varchar(120)  default '',
  `amazon_id` VARCHAR( 120 )  default '',
  `amazon_keyword` VARCHAR( 255 )  default '',
  `ebay_id` VARCHAR( 120 )  default '',
  `ebay_keyword` VARCHAR( 120 )  default '',
  `forum_url` VARCHAR( 120 )  default '',
  `forum_blank` ENUM( '0', '1' )  DEFAULT '0',
  `region` VARCHAR( 120 )  default 'us',
  `bing_app_id` varchar(150)  default '',
  PRIMARY KEY  (`domain_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `domain`
--


-- --------------------------------------------------------

--
-- Table structure for table `keyword`
--

CREATE TABLE `%dbprefix%keyword` (
  `key_id` mediumint(8) unsigned  auto_increment,
  `top_key_id` mediumint(8) unsigned  default '0',
  `key_text` varchar(100)  default '',
  `key_rank` smallint(5) unsigned  default '0',
  PRIMARY KEY  (`key_id`)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table `keyword_update`
--

CREATE TABLE `%dbprefix%keyword_update` (
  `kup_id` int(10) unsigned  auto_increment,
  `key_id` mediumint(8) unsigned  default '0',
  `kup_table` varchar(80)  default '',
  `kup_time` int(10) unsigned  default '0',
  PRIMARY KEY  (`kup_id`),
  KEY `key_id` (`key_id`,`kup_table`)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table `page_insert`
--

CREATE TABLE `%dbprefix%page_insert` (
  `pi_id` mediumint(8) unsigned  auto_increment,
  `pi_page` varchar(255)  default '',
  `pi_html` text ,
  PRIMARY KEY  (`pi_id`)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table `static_page`
--

CREATE TABLE `%dbprefix%static_page` (
  `sp_id` mediumint(8) unsigned  auto_increment,
  `parent_sp_id` mediumint(8) unsigned  default '0',
  `sp_rank` tinyint(3) unsigned  default '0',
  `sp_win_title` varchar(255)  default '',
  `sp_meta_desc` varchar(255)  default '',
  `sp_meta_keys` varchar(255)  default '',
  `sp_nav_title` varchar(80)  default '',
  `sp_slug` varchar(150)  default '',
  `sp_html` text ,
  `sp_text` text ,
  `sp_rss_url` text ,
  PRIMARY KEY  (`sp_id`),
  KEY `parent_sp_id` (`parent_sp_id`)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table `yahoo_image`
--

CREATE TABLE `%dbprefix%yahoo_image` (
  `yim_id` mediumint(8) unsigned  auto_increment,
  `yim_url` varchar(255)  default '',
  `key_id` mediumint(8) unsigned  default '0',
  `yim_rank` smallint(5) unsigned  default '0',
  `yim_data` text ,
  PRIMARY KEY  (`yim_id`),
  KEY `yim_url` (`yim_url`,`key_id`)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table `yahoo_link`
--

CREATE TABLE `%dbprefix%yahoo_link` (
  `ylk_id` mediumint(8) unsigned  auto_increment,
  `ylk_url` varchar(255)  default '',
  `key_id` mediumint(8) unsigned  default '0',
  `ylk_rank` smallint(5) unsigned  default '0',
  `ylk_data` text ,
  PRIMARY KEY  (`ylk_id`),
  KEY `ylk_url` (`ylk_url`,`key_id`)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table `yahoo_news`
--

CREATE TABLE `%dbprefix%yahoo_news` (
  `ynw_id` mediumint(8) unsigned  auto_increment,
  `key_id` mediumint(8) unsigned  default '0',
  `ynw_time_p` int(10) unsigned  default '0',
  `ynw_title` varchar(255)  default '',
  `ynw_data` text ,
  PRIMARY KEY  (`ynw_id`)
) TYPE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table `yahoo_qa`
--

CREATE TABLE `%dbprefix%yahoo_qa` (
  `yqa_id` mediumint(8) unsigned  auto_increment,
  `y_q_id` varchar(30)  default '',
  `key_id` mediumint(8) unsigned  default '0',
  `yqa_rank` smallint(5) unsigned  default '0',
  `yqa_data` text ,
  `y_q_subj` text ,
  `y_q_cont` text ,
  PRIMARY KEY  (`yqa_id`),
  KEY `y_q_id` (`y_q_id`,`key_id`)
) TYPE=MyISAM ;

CREATE TABLE `%dbprefix%youtube_video` (
  `ytv_id` mediumint(8) unsigned  auto_increment,
  `ytv_videoid` varchar(255)  default '',
  `ytv_videotitle` varchar(255)  default '',
  `ytv_videodescription` text,
  `ytv_videothumburl` varchar(255)  default '',
  `key_id` mediumint(8) unsigned  default '0',
  `ytv_rank` smallint(5) unsigned  default '0',
  PRIMARY KEY  (`ytv_id`),
  KEY `ylk_url` (`ytv_id`,`ytv_videoid`)
) TYPE=MyISAM ;


CREATE TABLE `%dbprefix%bing_news` (
  `bn_id` mediumint(8) unsigned  auto_increment,
  `bn_url` varchar(255)  default '',
  `bn_title` varchar(255)  default '',
  `bn_snippet` text,
  `bn_source` varchar(255)  default '',
  `key_id` mediumint(8) unsigned  default '0',
  `bn_rank` smallint(5) unsigned  default '0',
  `bn_time_p` int(10) unsigned  default '0',
  PRIMARY KEY  (`bn_id`)

) TYPE=MyISAM ;

