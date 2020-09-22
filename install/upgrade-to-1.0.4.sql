ALTER TABLE `domain` ADD `domain_name` VARCHAR( 255 )  NULL AFTER `domain_id` ;
ALTER TABLE `domain` ADD `amazon_id` VARCHAR( 120 )  NULL ;
ALTER TABLE `domain` ADD `amazon_keyword` VARCHAR( 255 ) NULL ;
ALTER TABLE `domain` ADD `ebay_id` VARCHAR( 120 ) NULL ;
ALTER TABLE `domain` ADD `ebay_keyword` VARCHAR( 120 ) NULL ;
ALTER TABLE `domain` ADD `forum_url` VARCHAR( 120 ) NULL ;
ALTER TABLE `domain` ADD `forum_blank` ENUM( '0', '1' ) NOT NULL DEFAULT '0';
