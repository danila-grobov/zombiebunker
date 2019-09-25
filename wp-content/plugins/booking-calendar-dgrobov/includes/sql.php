<?php
/******************************************************************************
 * #                         BookingWizz v5.5
 * #******************************************************************************
 * #      Author:     Convergine (http://www.convergine.com)
 * #      Website:    http://www.convergine.com
 * #      Support:    http://support.convergine.com
 * #      Version:    5.5
 * #
 * #      Copyright:   (c) 2009 - 2014  Convergine.com
 * #
 * #******************************************************************************/

//Load the database file
@require_once("dbconnect.php");


$query = "CREATE TABLE IF NOT EXISTS `bs_reservations` (
			  `id` int(20) NOT NULL AUTO_INCREMENT,
			  `serviceID` int(11) NOT NULL DEFAULT '1',
			  `dateCreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `name` varchar(255) NOT NULL,
			  `email` varchar(255) NOT NULL,
			  `phone` varchar(255) NOT NULL,
			  `comments` mediumtext NOT NULL,
              `young` bit NOT NULL,
			  `status` tinyint(5) NOT NULL DEFAULT '2' COMMENT '1 - confirmed, 2 - not confirmed',
			  `eventID` int(20) DEFAULT NULL,
			  `interval` int(20) DEFAULT NULL,
			  `qty` int(20) NOT NULL DEFAULT '1',
              `date` DATE DEFAULT NULL,
              `coupon` VARCHAR( 100 ) NOT NULL DEFAULT  '',
              `reminder_sent` ENUM(  'y',  'n' ) NOT NULL DEFAULT  'n',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
if ($mysqli->query($query)) {
    $BWMessage .= "Created table 'bs_reservations' (1/7)<br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_reservations' (1/7)<br/><br /></div>";
    $BWContinue = false;
}

########################################################################################################################################

$query = "CREATE TABLE IF NOT EXISTS `bs_reservations_items` (
  	`id` int(20) NOT NULL auto_increment,
  	`reservationID` int(20) NOT NULL,
  	`dateCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  	`reserveDateFrom` datetime NOT NULL default '0000-00-00 00:00:00',
  	`reserveDateTo` datetime NOT NULL default '0000-00-00 00:00:00',
	`eventID` int(20) NULL,
	`qty` int(20) NOT NULL default '1',
  	PRIMARY KEY  (`id`)
	)";

if ($mysqli->query($query)) {
    $BWMessage .= "Created table 'bs_reservations_items' (2/7)<br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_reservations_items' (2/7)<br/><br /></div>";
    $BWContinue = false;
}
########################################################################################################################################

$query = "CREATE TABLE IF NOT EXISTS `bs_reserved_time` (
		  `id` int(20) NOT NULL AUTO_INCREMENT,
		  `serviceID` int(11) NOT NULL DEFAULT '1',
		  `reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
		  `dateCreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `reserveDateFrom` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `reserveDateTo` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `interval` int(20) DEFAULT NULL,
		  `qty` INT NOT NULL DEFAULT  '1',
		  `repeate` ENUM(  'year',  'month',  'week',  'day' ) NOT NULL ,
		  `repeate_interval` INT NOT NULL ,
		  `recurring` TINYINT NOT NULL DEFAULT  '0',
		  PRIMARY KEY (`id`)
		)";
if ($mysqli->query($query)) {
    $BWMessage .= "Created table 'bs_reserved_time' (3/7)<br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_reserved_time' (3/7)<br/><br /></div>";
    $BWContinue = false;
}
########################################################################################################################################

$query = "CREATE TABLE IF NOT EXISTS `bs_settings` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `option_name` varchar(200) NOT NULL,
                      `option_value` text NOT NULL,
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `option_name` (`option_name`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
if ($mysqli->query($query)) {
    $BWMessage .= "Created table 'bs_settings' (4/7)<br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_settings' (4/7)<br/><br /></div>";
    $BWContinue = false;
}
$query = "INSERT INTO `bs_settings` (`id`, `option_name`, `option_value`) VALUES
                    (1, 'email', 'some@email.ca'),
                    (2, 'username', 'admin'),
                    (3, 'password', '1a1dc91c907325c69271ddf0c944bc72'),
                    (4, 'pemail', 'some@email.com'),
                    (5, 'pcurrency', 'USD'),
                    (6, 'currency', '$'),
                    (7, 'tax', ''),
                    (8, 'enable_tax', '0'),
                    (9, 'time_mode', '0'),
                    (10, 'date_mode', 'Y-m-d'),
                    (11, 'use_popup', '0'),
                    (12, 'lang', 'english'),
                    (13, 'active_plugins', ''),
                    (14, 'payment_methods','a:2:{s:7:\"invoice\";s:15:\"Offline Invoice\";s:6:\"paypal\";s:14:\"PayPal Gateway\";}'),
                    (15, 'currency_position', 'a'),
                    (16, 'multi_day_notification', '24'),
                    (17, 'single_day_notification', '12'),
                    (18, 'event_notification', '12'),
                    (19, 'cron_type', 'cron'),
                    (20, 'multi_day_notification_on','n'),
                    (21, 'single_day_notification_on','n'),
                    (22, 'event_notification_on','n'),
                    (23, 'language_switch','0');";
if ($mysqli->query($query)) {
    $BWMessage .= "Fill table 'bs_settings' (4/7)<br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_settings' (4/7)<br/><br /></div>";
    $BWContinue = false;
}

########################################################################################################################################



$query = "CREATE TABLE IF NOT EXISTS `bs_events` (
			  `id` int(20) NOT NULL AUTO_INCREMENT,
			  `eventDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `eventDateEnd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `serviceID` int(11) NOT NULL,
			  `eventTime` varchar(255) DEFAULT NULL,
			  `spaces` int(20) DEFAULT '10',
			  `title` varchar(255) DEFAULT NULL,
			  `entryFee` double NOT NULL DEFAULT '0',
                          `payment_method` VARCHAR( 100 ) NOT NULL DEFAULT 'invoice',
			  `payment_required` tinyint(5) NOT NULL DEFAULT '2',
			  `description` longtext,
			  `max_qty` int(20) NOT NULL DEFAULT '1',
			  `allow_multiple` int(20) NOT NULL DEFAULT '2' COMMENT '1- yes 2 - no',
			  `path` varchar(255) DEFAULT NULL,
                          `repeate` ENUM(  'year',  'month',  'week',  'day' ) NOT NULL ,
                          `repeate_interval` INT NOT NULL ,
                          `recurring` TINYINT NOT NULL DEFAULT  '0',
                          `recurringEndDate` DATE NOT NULL ,
                          `coupon` SMALLINT( 1 ) NOT NULL DEFAULT  '0',
                          `location` VARCHAR( 200 ) NULL DEFAULT NULL ,
                          `map_link` VARCHAR( 200 ) NULL DEFAULT NULL,
                          `color` VARCHAR( 20 ) NOT NULL,
                          `deposit` DECIMAL( 10, 2 ) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

if ($mysqli->query($query)) {
    $BWMessage .= "Created table 'bs_events' (5/7)<br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_events' (5/7)<br/><br /></div>";
    $BWContinue = false;
}
########################################################################################################################################


$query = "CREATE TABLE IF NOT EXISTS `bs_transactions` (
	  `id` int(11) NOT NULL auto_increment,
	  `reservationID` int(20)  NULL,
	  `eventID` int(20)  NULL,
	  `transactionID` varchar(50) NULL,
	  `dateCreated` datetime NOT NULL default '0000-00-00 00:00:00',
	  `currency` varchar(255) NULL,
	  `amount` double NULL,
	  `payment_status` varchar(255) NULL,
	  `payer_email` varchar(255) NULL,
	  `payer_name` varchar(255) NULL,
	  PRIMARY KEY  (`id`)
	)  ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

if ($mysqli->query($query)) {
    $BWMessage .= "Created table 'bs_transactions' (6/7)<br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_transactions' (6/7)<br/><br /></div>";
    $BWContinue = false;
}
########################################################################################################################################

$query = "CREATE TABLE `bs_reserved_time_items` (
			`id` INT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`dateCreated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			`reservedID` INT( 20 ) NOT NULL ,
			`dateFrom` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			`dateTo` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			`tinterval` INT( 20 ) NOT NULL,
			`qty` INT NOT NULL DEFAULT  '1' 
			)";

if ($mysqli->query($query)) {
    $BWMessage .= "Created table 'bs_reserved_time_items' <br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_reserved_time_items' <br/><br /></div>";
    $BWContinue = false;
}
########################################################################################################################################

$query = "CREATE TABLE IF NOT EXISTS `bs_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `date_created` date NOT NULL,
  `type` enum('t','d') NOT NULL DEFAULT 't',
  `autoconfirm` TINYINT NOT NULL DEFAULT  '0' COMMENT  '0- off, 1 - on',
  `fromName` VARCHAR( 200 ) NOT NULL DEFAULT  'Name',
  `fromEmail` VARCHAR( 200 ) NOT NULL DEFAULT  'noreply@email.com',
  `show_event_titles` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-show,0-not show',
  `show_event_image` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-show,0-not show',
  `show_available_seats` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1-show,0-not show',
  `default` ENUM('y','n') NOT NULL DEFAULT  'n',
  `deposit` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '1',
  `delBookings` ENUM(  'y',  'n' ) NOT NULL DEFAULT  'n',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

if ($mysqli->query($query)) {
    $BWMessage .= "Created table 'bs_services' <br/><br/>".$mysqli->error();
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_services' <br/><br /></div>";
    $BWContinue = false;
}
########################################################################################################################################	
$query = "INSERT INTO `bs_services` (`id`, `name`, `date_created`,`type`,`default`) VALUES
				(1, 'Default service', NOW(), 't','y')";

if ($mysqli->query($query)) {
    $BWMessage .= "Alter table 'bs_services' <br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_services' <br/><br /></div>";
    $BWContinue = false;
}
########################################################################################################################################	

$query = "CREATE TABLE IF NOT EXISTS `bs_service_settings` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `serviceId` int(11) NOT NULL,
                          `payment_method` varchar(255) NOT NULL DEFAULT 'invoice',
			  `allow_times` int(20) NOT NULL DEFAULT '1',
			  `allow_times_min` int(20) NOT NULL DEFAULT '1',
			  `interval` int(20) NOT NULL DEFAULT '60',
			  `spot_price` double NOT NULL DEFAULT '0',
			  `spot_invoice` tinyint(4) NOT NULL DEFAULT '0',
			  `startDay` tinyint(5) NOT NULL DEFAULT '0' COMMENT '0- sunday, 1 - monday',		  
			  `spaces_available` varchar(255) NOT NULL DEFAULT '1' COMMENT 'spaces available per each REGULAR timed slot',
			  `show_spaces_left` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-show,0-not show',
			  `show_multiple_spaces` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1-show,0-not show',   
			  `use_popup` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1-show,0-not show',
                          `coupon` SMALLINT( 1 ) NOT NULL DEFAULT  '0',
                          `time_before` INT NOT NULL DEFAULT  '0',
			  PRIMARY KEY (`id`)
			)  ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

if ($mysqli->query($query)) {
    $BWMessage .= "Create table 'bs_service_settings' <br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_service_settings' <br/><br /></div>";
    $BWContinue = false;
}
########################################################################################################################################
$query = "INSERT INTO `bs_service_settings` (`id`, `serviceId`,`payment_method`, `allow_times`, `allow_times_min`, `interval`, `spot_price`, `spot_invoice`, `startDay`, `spaces_available`, `show_spaces_left`,`show_multiple_spaces`,`use_popup`) VALUES
(1, 1, 'invoice', 99, 2, 30, 10, 0, 1, '1', 0, 0,0)";

if ($mysqli->query($query)) {
    $BWMessage .= "Alter table 'bs_service_settings' <br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't add 'Default settings' <br/><br /></div>";
    $BWContinue = false;
}
########################################################################################################################################
########################################################################################################################################
$query = "CREATE TABLE IF NOT EXISTS `bs_schedule` (
		  `idItem` int(11) NOT NULL AUTO_INCREMENT,
		  `idService` int(11) NOT NULL,
		  `week_num` int(11) NOT NULL,
		  `startTime` int(11) NOT NULL,
		  `endTime` int(11) NOT NULL,
		  PRIMARY KEY (`idItem`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";


if ($mysqli->query($query)) {
    $BWMessage .= "Create table 'bs_schedule' <br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'Default settings' <br/><br /></div>";
    $BWContinue = false;
}

########################################################################################################################################
$query = "CREATE TABLE IF NOT EXISTS `bs_schedule_days` (
  `idItem` int(11) NOT NULL AUTO_INCREMENT,
  `idService` int(11) NOT NULL,
  `dateFrom` date NOT NULL,
  `dateTo` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idItem`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";


if ($mysqli->query($query)) {
    $BWMessage .= "Create table 'bs_schedule_days' <br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_schedule_days' <br/><br /></div>";
    $BWContinue = false;
}

########################################################################################################################################
$query = "CREATE TABLE IF NOT EXISTS `bs_service_days_settings` (
  `idService` int(11) NOT NULL,
  `spots` int(1) NOT NULL,
  `description` text NOT NULL,
  `img` varchar(200) NOT NULL,
  `maxDays` int(10) NOT NULL,
  `minDays` int(10) NOT NULL,
  `daysBefore` int(11) NOT NULL,
  `startDay` TINYINT( 1 ) NOT NULL DEFAULT  '0' COMMENT  '0- sunday, 1 - monday',
  `payment_method` VARCHAR( 100 ) NOT NULL DEFAULT  'invoice',
  `coupon` SMALLINT( 1 ) NOT NULL DEFAULT  '0',
  `showPrice` TINYINT( 1 ) NOT NULL DEFAULT  '0',
  PRIMARY KEY (`idService`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


if ($mysqli->query($query)) {
    $BWMessage .= "Create table 'bs_service_days_settings' <br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_service_days_settings' <br/><br /></div>";
    $BWContinue = false;
}
########################################################################################################################################
$query="CREATE TABLE IF NOT EXISTS `bs_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `dateFrom` date NOT NULL,
  `dateTo` date NOT NULL,
  `value` int(11) NOT NULL,
  `type` enum('abs','rel') NOT NULL,
  `services` varchar(200) NOT NULL,
  `code` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;";

if ($mysqli->query($query)) {
    $BWMessage .= "Create table 'bs_service_days_settings' <br/><br/>";
} else {
    $BWMessage .= "<div class=error><b>ERROR!</b> can't create 'bs_service_days_settings' <br/><br /></div>";
    $BWContinue = false;
}
