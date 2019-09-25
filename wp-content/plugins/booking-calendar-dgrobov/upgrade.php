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
//
//Load the database file
require_once("includes/dbconnect.php");


$tt = "";
$continue = true;
$success = false;
$license = (!empty($_REQUEST['license'])) ? strip_tags(str_replace("'", "`", $_REQUEST['license'])) : '';
$username = (!empty($_REQUEST['username'])) ? strip_tags(str_replace("'", "`", $_REQUEST['username'])) : '';

$_domain = $_SERVER['HTTP_HOST'];
$domain = $_domain;

if (!empty($_REQUEST["install"]) && $_REQUEST['install'] == "yes") {
    $l = $license;

    require_once("includes/core.functions.php");
    require_once("includes/grid.functions.php");
    if ($continue) {

        $query = "CREATE TABLE IF NOT EXISTS `bs_coupons` (
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
                                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ";
        if ($mysqli->query($query)) {
            $tt .= "Added bs_coupons  <br />";
        }

        $query = "ALTER TABLE  `bs_events` ADD  `repeate` ENUM(  'year',  'month',  'week',  'day' ) NOT NULL ,
                                ADD  `repeate_interval` INT( 11 ) NOT NULL ,
                                ADD  `recurring` TINYINT( 4 ) NOT NULL DEFAULT  '0',
                                ADD  `recurringEndDate` DATE NOT NULL ,
                                ADD  `coupon` SMALLINT NOT NULL DEFAULT  '0',
                                ADD  `location` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
                                ADD  `map_link` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL";
        if ($mysqli->query($query)) {
            $tt .= "Update bs_event <br />";
        }

        $query = "ALTER TABLE  `bs_reservations` ADD  `date` DATE NOT NULL ,
                                ADD  `coupon` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
        if ($mysqli->query($query)) {
            $tt .= "Update bs_reservations <br />";
        }

        $query = "CREATE TABLE IF NOT EXISTS `bs_schedule_days` (
                              `idItem` int(11) NOT NULL AUTO_INCREMENT,
                              `idService` int(11) NOT NULL,
                              `dateFrom` date NOT NULL,
                              `dateTo` date NOT NULL,
                              `price` decimal(10,2) NOT NULL,
                              PRIMARY KEY (`idItem`)
                            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
        if ($mysqli->query($query)) {
            $tt .= "Added  bs_schedule_days <br />";
        }

        $query = "ALTER TABLE  `bs_services` ADD  `type` ENUM(  't',  'd' ) NOT NULL DEFAULT  't',
                            ADD  `autoconfirm` TINYINT( 4 ) NOT NULL DEFAULT  '0' COMMENT  '0-off;1-on',
                            ADD  `fromName` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
                            ADD  `fromEmail` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
                            ADD  `show_event_titles` TINYINT( 1 ) NOT NULL DEFAULT  '0' COMMENT  '1-show; 0-not show',
                            ADD  `show_event_image` TINYINT( 1 ) NOT NULL DEFAULT  '0' COMMENT  '1-show; 0-not show',
                            ADD  `show_available_seats` TINYINT( 1 ) NOT NULL DEFAULT  '0' COMMENT  '1-show; 0-not show'";
        if ($mysqli->query($query)) {
            $tt .= "Update  bs_services <br />";
        }

        $query = "SELECT * FROM  bs_service_settings";
        $res = $mysqli->query($query);
        while($row = $res->fetch_assoc()){
            $sql = "UPDATE bs_services SET
                            `show_event_titles`='{$row['show_event_titles']}',
                            `show_event_image`='{$row['show_event_image']}'
                            WHERE id = {$row['id']}";

            $mysqli->query($sql) or die($sql." ".$mysqli->error());
        }

        $query = "ALTER TABLE  `bs_service_settings` DROP  `1_from` ,
                                DROP  `1_to` ,
                                DROP  `2_from` ,
                                DROP  `2_to` ,
                                DROP  `3_from` ,
                                DROP  `3_to` ,
                                DROP  `4_from` ,
                                DROP  `4_to` ,
                                DROP  `5_from` ,
                                DROP  `5_to` ,
                                DROP  `6_from` ,
                                DROP  `6_to` ,
                                DROP  `0_from` ,
                                DROP  `0_to` ,
                                DROP  `show_event_titles` ,
                                DROP  `show_event_image` ";
        if ($mysqli->query($query)) {
            $tt .= "Clear  bs_service_settings <br />";
        }

        $query = "ALTER TABLE  `bs_service_settings` ADD  `coupon` SMALLINT( 1 ) NOT NULL DEFAULT  '0',
                                ADD  `time_before` INT NOT NULL DEFAULT  '0'";
        if ($mysqli->query($query)) {
            $tt .= "Update  bs_service_settings <br />";
        }

        $query = "CREATE TABLE IF NOT EXISTS `bs_service_days_settings` (
                              `idService` int(11) NOT NULL,
                              `spots` int(1) NOT NULL,
                              `description` text NOT NULL,
                              `img` varchar(200) NOT NULL,
                              `maxDays` int(1) NOT NULL,
                              `daysBefore` int(11) NOT NULL,
                              `startDay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0- sunday, 1 - monday',
                              `payment_method` varchar(100) NOT NULL DEFAULT 'invoice',
                              `coupon` smallint(1) NOT NULL DEFAULT '0',
                              PRIMARY KEY (`idService`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
        if ($mysqli->query($query)) {
            $tt .= "Added   bs_service_days_settings <br />";
        }

        ### version 5.3.1

        $query = "ALTER TABLE  `bs_events` ADD  `color` VARCHAR( 20 ) NOT NULL,
                                ADD  `deposit` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '1'";
        if ($mysqli->query($query)) {
            $tt .= "Update bs_event <br />";
        }

        $query = "ALTER TABLE  `bs_services` ADD  `deposit` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '1',
                                             ADD  `default` ENUM(  'y',  'n' ) NOT NULL DEFAULT  'n' AFTER  `id`";
        if ($mysqli->query($query)) {
            $tt .= "Update  bs_services <br />";
        }

        $query = "ALTER TABLE  `bs_services` ADD  `delBookings` ENUM(  'y',  'n' ) NOT NULL DEFAULT  'n'";
        if ($mysqli->query($query)) {
            $tt .= "Update  bs_services <br />";
        }

        $query = "ALTER TABLE  `bs_service_days_settings` ADD  `showPrice` TINYINT( 1 ) NOT NULL DEFAULT  '0'";

        if ($mysqli->query($query)) {
            $tt .= "Update  bs_service_days_settings <br />";
        }

        $query = "ALTER TABLE  `bs_reservations` ADD  `reminder_sent` ENUM(  'y',  'n' ) NOT NULL DEFAULT  'n'";
        if ($mysqli->query($query)) {
            $tt .= "Update  bs_reservations <br />";
        }

        $query = "INSERT INTO `bs_settings` (`id`, `option_name`, `option_value`) VALUES
                                                        (NULL, 'multi_day_notification', '24'),
                                                        (NULL, 'single_day_notification', '12'),
                                                        (NULL, 'event_notification', '12'),
                                                        (NULL, 'cron_type', 'cron'),
                                                        (NULL, 'multi_day_notification_on','n'),
                                                        (NULL, 'single_day_notification_on','n'),
                                                        (NULL, 'event_notification_on','n');";
        if ($mysqli->query($query)) {
            $tt .= "Insert settings <br />";
        }
        $query = "INSERT INTO `bs_settings` (`id`, `option_name`, `option_value`) VALUES
                                                        (NULL, 'language_switch', '0');";
        if ($mysqli->query($query)) {
            $tt .= "Insert settings <br />";
        }

        $query = "ALTER TABLE  `bs_service_days_settings` ADD  `minDays` INT NOT NULL AFTER  `maxDays`";
        if ($mysqli->query($query)) {
            $tt .= "Add min days settings <br />";
        }
    }

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>BookingWizz v5.5</title>
    <link rel="stylesheet" href="css/installation-page-styles.css" type="text/css"/>
</head>
<body>
<div id="header">
    <div class="scriptname left">BookingWizz v5.5 - Upgrade Wizard</div>

    <br class="clear"/>
</div>

<div id="content">

    <div class="install_container">
        <div class="login">
            <?php if (!empty($tt)) {
                echo $tt;
            }
            if ($success) {
            } else {
                ?><br/>
                <form method="post" action="upgrade.php" enctype="multipart/form-data" name="ff1">


                    <p>Please enter your CodeCanyon license key (located in the license text file in your
                        purchase confirmation email from Envato, or login to your account and go to downloads,
                        you will see red link "License Certificate" next to our product (<a
                            href="http://screencast.com/t/mI0BCJxSK0w" target="_blank">screenshot</a>)). </p>

                    <label>License Key:</label> <input type="text" id="license" name="license"
                                                       value="<?php echo $license ?>" size="100"
                                                       style="width: 250px"/><br class="clear"/>
                    <label>Username: </label><input type="text" id="username" name="username"
                                                    value="<?php echo $username ?>" size="30"/><br class="clear"/>

                    <br/>


                    <div class="text_center">

                        <input type="image" name="submit" src="images/new/btn_submit.jpg"
                               value="<?php echo ADM_BTN_SUBMIT; ?>" tabindex="2"/>
                    </div>
                    <input type="hidden" value="yes" name="install"/>
                </form>
            <?php } ?>
        </div>
    </div>
</div>

<div class="footer">
    <a href="http://www.convergine.com" target="_blank"><img src="images/convergine.png" border="0"></a>
</div>
</body>
</html>