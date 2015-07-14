CREATE TABLE `translation` (
	`translation_id` INT(11) NOT NULL AUTO_INCREMENT,
	`language` VARCHAR(5) NOT NULL,
	`key_name` VARCHAR(48) NOT NULL,
	`value` TEXT NOT NULL,
	PRIMARY KEY (`translation_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
AUTO_INCREMENT=1;