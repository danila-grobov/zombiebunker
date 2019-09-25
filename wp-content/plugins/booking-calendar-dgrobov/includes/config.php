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

@session_start();
@ob_start();
define("MAIN_PATH", dirname(dirname(__FILE__))); //main path of BookingWizz directory
require_once(MAIN_PATH . "/includes/dbconnect.php"); //Load the db connect
require_once(MAIN_PATH . "/../../../wp-load.php");
define("MAIN_URL", home_url() . $baseDir);
define("CURT_VER", "v5.5");

$system_massage = array("error" => array(), "warning" => array(), "success" => array()); // arra of system messages

require_once(MAIN_PATH . "/includes/core.functions.php"); //Load the functions
require_once(MAIN_PATH . "/includes/plugin.functions.php"); //Load the functions
require_once(MAIN_PATH . "/includes/functions.php"); //Load the functions
require_once(MAIN_PATH . "/includes/dg_functions.php"); //Load the functions
require_once(MAIN_PATH . "/includes/multiday.functions.php"); //Load the functions for mul-day bookings

define("IS_WP_PLUGIN", getOption("is_word_press"));

$lang = (!empty($_REQUEST["lang"])) ? strip_tags(str_replace("'", "`", $_REQUEST["lang"])) : 'english';

if (!isset($_SESSION['curr_lang'])) {
    $_SESSION['curr_lang'] = getOption('lang');
}
if (!empty($_REQUEST["action"]) && $_REQUEST["action"] == "changelang") {
    $_SESSION['curr_lang'] = $lang;
}


if (strpos($_SERVER['SCRIPT_NAME'], "bs-settings")) {
    if (!empty($_REQUEST["edit_settings"]) && $_REQUEST["edit_settings"] == "yes") {

        updateOption('lang', $lang);
        $_SESSION['curr_lang'] = $lang;
        $timezone = (!empty($_REQUEST["timezone"])) ? strip_tags(str_replace("'", "`", $_REQUEST["timezone"])) : date_default_timezone_get();
        updateOption('timezone', $timezone);
    }
}
$timezone = getOption('timezone');
if ($timezone !== false) {
    date_default_timezone_set($timezone);
} else {
    date_default_timezone_set(date_default_timezone_get());
}

define('DATE', date("Y-m-d"));
define('DATETIME', date("Y-m-d H:i:s"));

$languagePath = MAIN_PATH . "/languages/" . $_SESSION['curr_lang'] . ".lang.php";
if (is_file($languagePath)) {

    include MAIN_PATH . "/languages/" . $_SESSION['curr_lang'] . ".lang.php";
} else {
    print "ERROR !!! Language file " . $_SESSION['curr_lang'] . ".lang.php not found";
    exit();
}

$monthList = array();
for ($i = 1; $i < 13; $i++) {
    $r = date("F", strtotime("2000-" . $i . "-01"));
    $monthList[date("F", strtotime("2000-" . $i . "-01"))] = constant($r);
}
for ($i = 1; $i < 13; $i++) {
    $r = date("M", strtotime("2000-" . $i . "-01"));
    $monthList[date("M", strtotime("2000-" . $i . "-01"))] = constant($r);
}
for ($i = 1; $i < 8; $i++) {
    $r = date("D", strtotime("22-01-2012 +$i days"));
    $monthList[date("D", strtotime("22-01-2012 +$i days"))] = constant($r);
}

define("BW_SELF", basename($_SERVER['SCRIPT_FILENAME']));

// options which connot be deleted
$coreOptionsList = array(

    "email",
    "username",
    "password",
    "pemail",
    "pcurrency",
    "currency",
    "tax",
    "enable_tax",
    "time_mode",
    "date_mode",
    "use_popup",
    "lang",
    "payment_methods"
);

$paymentMethods = array("invoice", "credit_card", "paypal");

//hide some submenu items
$sql = "SELECT * FROM bs_services WHERE type='d'";
$res = $mysqli->query($sql);
if ($res->num_rows > 0) {
    $scheduleSubMenu = array(
        array(
            "menu_title" => MENU1_1,
            "menu_link" => "bs-schedule.php"
        ),
        array(
            "menu_title" => MENU1_2,
            "menu_link" => "bs-schedule-day.php"
        ),
        array(
            "menu_title" => MENU1_3,
            "menu_link" => "bs-schedule-events.php"
        )
    );
    $manualSubMenu = array(
        array(
            "menu_title" => MENU8,
            "menu_link" => "bs-reserve-view.php"
        ),
        array(
            "menu_title" => MENU7,
            "menu_link" => "bs-reserve.php"
        ),
        array(
            "menu_title" => MENU7_1,
            "menu_link" => "bs-reserve-day.php"
        ),
        array(
            "menu_title" => MENU2_4,
            "menu_link" => "bs-manual-attendees-export.php"
        )
    );
} else {
    $scheduleSubMenu = array(
        array(
            "menu_title" => MENU1_1,
            "menu_link" => "bs-schedule.php"
        ),
        array(
            "menu_title" => MENU1_3,
            "menu_link" => "bs-schedule-events.php"
        )
    );
    $manualSubMenu = array(
        array(
            "menu_title" => MENU8,
            "menu_link" => "bs-reserve-view.php"
        ),
        array(
            "menu_title" => MENU7,
            "menu_link" => "bs-reserve.php"
        ),
        array(
            "menu_title" => MENU2_4,
            "menu_link" => "bs-manual-attendees-export.php"
        )
    );
}
$menuList = array(
    array(
        "menu_title" => MENU1,
        "menu_link" => "bs-schedule.php",
        "sub_menu" => $scheduleSubMenu


    ),
    array(
        "menu_title" => MENU2,
        "menu_link" => "bs-bookings.php",
        "sub_menu" =>
        array(
            array(
                "menu_title" => MENU2,
                "menu_link" => "bs-bookings.php"
            ),
            array(
                "menu_title" => MENU2_4,
                "menu_link" => "bs-attendees-export.php"
            )
        )

    ),
    array(
        "menu_title" => MENU2_2,
        "menu_link" => "bs-reserve-view.php",
        "sub_menu" => $manualSubMenu

    ),
    array(
        "menu_title" => MENU9,
        "menu_link" => "bs-services.php",
        "sub_menu" =>
        array(
            array(
                "menu_title" => MENU10,
                "menu_link" => "bs-services.php"
            ),
            array(
                "menu_title" => MENU11,
                "menu_link" => "bs-services-add.php"
            ),
            array(
                "menu_title" => MENU11_1,
                "menu_link" => "bs-services_days-add.php"
            )
        )
    ),
    array(
        "menu_title" => MENU4,
        "menu_link" => "bs-events.php",
        "sub_menu" =>
        array(
            array(
                "menu_title" => MENU4_0,
                "menu_link" => "bs-events.php"
            ),
            array(
                "menu_title" => MENU4_1,
                "menu_link" => "bs-events-add.php"
            )
        )
    ),
    array(
        "menu_title" => MENU5,
        "menu_link" => "bs-coupons.php",
        "sub_menu" =>
        array(
            array(
                "menu_title" => MENU14,
                "menu_link" => "bs-coupons.php"
            ),
            array(
                "menu_title" => MENU5_1,
                "menu_link" => "bs-coupon-add.php"
            )
        )
    ),
    array(
        "menu_title" => MENU6,
        "menu_link" => "bs-reports.php",
        "sub_menu" =>
        array(
            array(
                "menu_title" => MENU6_1,
                "menu_link" => "bs-reports.php"
            ),
            array(
                "menu_title" => MENU6_2,
                "menu_link" => "bs-reports-app.php"
            ),
            array(
                "menu_title" => MENU6_3,
                "menu_link" => "bs-reports-mdb.php"
            )
        )
    )



);




bw_add_action("bw_load", "load_script");

if (getOption('cron_type') == 'alt') {
    cron('regular');
}
