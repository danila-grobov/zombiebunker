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
session_start();
require_once("includes/dbconnect.php"); //Load the settings
require_once("includes/config.php"); //Load the functions


$canonical = "//" . MAIN_URL;

$date = (!empty($_REQUEST["date"])) ? strip_tags(str_replace("'", "`", $_REQUEST["date"])) : '';

$dateFrom = (!empty($_REQUEST["dateFrom"])) ? strip_tags(str_replace("'", "`", $_REQUEST["dateFrom"])) : '';
$dateTo = (!empty($_REQUEST["dateTo"])) ? strip_tags(str_replace("'", "`", $_REQUEST["dateTo"])) : '';

$lb1 = (!empty($_REQUEST["lb1"])) ? strip_tags(str_replace("'", "`", $_REQUEST["lb1"])) : '';
$lb2 = (!empty($_REQUEST["lb2"])) ? strip_tags(str_replace("'", "`", $_REQUEST["lb2"])) : '';
$lb3 = (!empty($_REQUEST["lb3"])) ? strip_tags(str_replace("'", "`", $_REQUEST["lb3"])) : '';

$eventID = (!empty($_GET["eventID"])) ? $_GET["eventID"] : '';
$selEvent = (!empty($_GET["selEvent"])) ? $_GET["selEvent"] : '';
$name = (!empty($_REQUEST["name"])) ? strip_tags(str_replace("'", "`", $_REQUEST["name"])) : '';
$phone = (!empty($_REQUEST["phone"])) ? strip_tags(str_replace("'", "`", $_REQUEST["phone"])) : '';
$email = (!empty($_REQUEST["email"])) ? strip_tags(str_replace("'", "`", $_REQUEST["email"])) : '';
$comments = (!empty($_REQUEST["comments"])) ? strip_tags(str_replace("'", "`", $_REQUEST["comments"])) : '';
$qty = (!empty($_REQUEST["qty_" . $selEvent])) ? strip_tags(str_replace("'", "`", $_REQUEST["qty_" . $selEvent])) : '';
$time = (!empty($_GET["time"])) ? $_GET["time"] : '';
$couponCode = (!empty($_REQUEST["couponCode"])) ? strip_tags(str_replace("'", "`", $_REQUEST["couponCode"])) : '';

$serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();
############################## REQUEST CALENDAR DATE IF NAVIGATION USED ################################
$startDay = getServiceSettings($serviceID, 'startDay');
$iMonth = (!empty($_REQUEST["month"])) ? strip_tags(str_replace("'", "`", $_REQUEST["month"])) : date('n');
$iYear = (!empty($_REQUEST["year"])) ? strip_tags(str_replace("'", "`", $_REQUEST["year"])) : date('Y');
$calendar = "";
$calendar = setupCalendar($iMonth, $iYear, $serviceID);
list($iPrevMonth, $iPrevYear) = prevMonth($iMonth, $iYear);
list($iNextMonth, $iNextYear) = nextMonth($iMonth, $iYear);
$iCurrentMonth = date('n');
$iCurrentYear = date('Y');
$iCurrentDay = '';
if (($iMonth == $iCurrentMonth) && ($iYear == $iCurrentYear)) {
    $iCurrentDay = date('d');
    $thismonth = true;
}
$iNextMonth = mktime(0, 0, 0, $iNextMonth, 1, $iNextYear);
$iPrevMonth = mktime(0, 0, 0, $iPrevMonth, 1, $iPrevYear);
$iCurrentDay = $iCurrentDay;
$iCurrentMonth = mktime(0, 0, 0, $iMonth, 1, $iYear);
$title = _getDate(date('F Y', $iCurrentMonth));

$serviceLink = "&serviceID={$serviceID}";
################### PREPARE LINKS FOR CALENDAR NAVIGATION ######################
$prev_month_href = "window.location.href='?month=" . date('m', $iPrevMonth) . "&year=" . date('Y', $iPrevMonth) . $serviceLink . "'";
$next_month_href = "window.location.href='?month=" . date('m', $iNextMonth) . "&year=" . date('Y', $iNextMonth) . $serviceLink . "'";
$prev_month_link = "<a class=\"previous_month\" rel=\"nofollow\">" . _getDate(date('M', $iPrevMonth)) . "</a>";
$next_month_link = "<a class=\"next_month\" rel=\"nofollow\">" . _getDate(date('M', $iNextMonth)) . "</a>";
################### PREPARE CALENDAR HEADER DEPENDING ON MON OR SUN AS FIRST DAY ######################
if ($startDay == "0") {
    $calendarHeader = '<th class="weekend dash_border">' . getShortWeek(0) . '</th><th class="dash_border">' . getShortWeek(1) . '</th><th class="dash_border">' . getShortWeek(2) . '</th><th class="dash_border">' . getShortWeek(3) . '</th><th class="dash_border">' . getShortWeek(4) . '</th><th class="dash_border">' . getShortWeek(5) . '</th><th class="weekend dash_border">' . getShortWeek(6) . '</th>';
} else if ($startDay == "1") {
    $calendarHeader = '<th class="dash_border">' . getShortWeek(1) . '</th><th class="dash_border">' . getShortWeek(2) . '</th><th class="dash_border">' . getShortWeek(3) . '</th><th class="dash_border">' . getShortWeek(4) . '</th><th class="dash_border">' . getShortWeek(5) . '</th><th class="weekend dash_border">' . getShortWeek(6) . '</th><th class="weekend dash_border">' . getShortWeek(0) . '</th>';
}
?>

<?php include "includes/header.php" ?>

<div id="index">
    <div class="calendar">
        <?php
        $sql = "SELECT * FROM bs_services";
        $res = $mysqli->query($sql);
        if ($res->num_rows > 1) {
            ?>
            <font color=red><b>Kambarys (Room):</b></font>
            <div class="servicesListCont">
                <form name="ff1" id="ff1" method="get">
                    <select name="serviceID" onchange="document.forms['ff1'].submit()">
                        <option>--pasirinkite--</option>

                        <?php while ($row = $res->fetch_assoc()) { ?>
                            <option value="<?php echo $row['id'] ?>" <?php echo ($serviceID == $row['id']) ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </select>
                </form>
            </div>
            <div style="clear:both"></div>
        <?php } ?>
        <!-- CALENDAR NAVIGATION -->
        <div class="navbar">
            <div class="month_button prev" onclick="<?php echo $prev_month_href ?>">
                <img src=" ./images/left.svg"> <?php echo $prev_month_link ?> </div> <span class="month_title">
                <?php echo $title ?>
            </span>
            <div class="month_button next" onclick="<?php echo $next_month_href ?>">
                <?php echo $next_month_link ?>
                <img src="./images/right.svg">
            </div>
        </div>
        <!-- CALENDAR NAVIGATION END -->
        <br />

        <table cellpadding="2" cellspacing="5" border="0" class="calendarTable">
            <tbody>
                <tr>
                    <?php echo $calendarHeader; ?>
                </tr>
            </tbody>
            <?php echo $calendar; ?>
        </table>
    </div>
</div>

<?php if ($demo === true) { ?><p class="copy">Link to <a href="admin.php">ADMIN AREA</a> (for DEMO purposes)</p><?php } ?>

<script language="javascript" type="text/javascript">
    function getLightbox(reserveDate, serviceID) {
        if ($(window).width() > 767) {
            $.fn.colorbox({
                href: 'booking_frame.php?date=' + reserveDate + "&serviceID=" + serviceID,
                innerWidth: '1056px'
            });
        } else {

            window.location.href = 'booking.php?date=' + reserveDate + "&serviceID=" + serviceID;
        }
        return false;
    }

    function getLightbox2(eventID, serviceID, date) {
        if ($(window).width() > 767) {
            $.fn.colorbox({
                href: 'event-booking_frame.php?eventID=' + eventID + "&serviceID=" + serviceID + "&date=" + date,
                innerWidth: '1060px'
            });
        } else {

            window.location.href = 'event-booking.php?eventID=' + eventID + "&serviceID=" + serviceID + "&date=" + date;
        }
        return false;
    }

    function getLightboxDays(date, serviceID) {
        if ($(window).width() > 767) {
            $.fn.colorbox({
                href: 'booking-days_frame.php?dateFrom=' + date + "&serviceID=" + serviceID,
                innerWidth: '1060px'
            });
        } else {

            window.location.href = 'booking-days.php?dateFrom=' + date + "&serviceID=" + serviceID;
        }
        return false;
    }

    $(document).ready(function() {
        <?php if (!empty($lb1) && $lb1 == "yes" && !empty($date)) { ?>
            if ($(window).width() > 767) {
                $.fn.colorbox({
                    href: "booking.php?date=<?php echo $date ?>&msg2=captcha&serviceID=<?php echo $serviceID ?>&name=<?php echo urlencode($name) ?>&phone=<?php echo urlencode($phone) ?>&email=<?php echo urlencode($email) ?>&comments=<?php echo urlencode($comments) ?>&<?php echo http_build_query(array('time' => $time)) ?>&couponCode=<?php echo urlencode($couponCode) ?>"
                });
            } else {

                window.location.href = "booking.php?date=<?php echo $date ?>&lb1=yes&serviceID=<?php echo $serviceID ?>&name=<?php echo urlencode($name) ?>&phone=<?php echo urlencode($phone) ?>&email=<?php echo urlencode($email) ?>&comments=<?php echo urlencode($comments) ?>&<?php echo http_build_query(array('time' => $time)) ?>&couponCode=<?php echo urlencode($couponCode) ?>";
            }
        <?php } ?>
        <?php if (!empty($lb2) && $lb2 == "yes" && !empty($eventID)) { ?>
            if ($(window).width() > 767) {
                $.fn.colorbox({
                    href: "event-booking_frame.php?eventID=<?php echo $eventID ?>&msg2=captcha&serviceID=<?php echo $serviceID ?>&name=<?php echo urlencode($name) ?>&phone=<?php echo urlencode($phone) ?>&email=<?php echo urlencode($email) ?>&qty_<?php echo $selEvent ?>=<?php echo urlencode($qty) ?>&comments=<?php echo urlencode($comments) ?>&selEvent=<?php echo $selEvent ?>&date=<?php echo $date ?>&couponCode=<?php echo urlencode($couponCode) ?>"
                });
            } else {

                window.location.href = "event-booking.php?eventID=<?php echo $eventID ?>&lb2=yes&serviceID=<?php echo $serviceID ?>&name=<?php echo urlencode($name) ?>&phone=<?php echo urlencode($phone) ?>&email=<?php echo urlencode($email) ?>&qty_<?php echo $selEvent ?>=<?php echo urlencode($qty) ?>&comments=<?php echo urlencode($comments) ?>&selEvent=<?php echo $selEvent ?>&date=<?php echo $date ?>&couponCode=<?php echo urlencode($couponCode) ?>";
            }
        <?php } ?>
        <?php if (!empty($lb3) && $lb3 == "yes" && !empty($dateFrom)) { ?>
            if ($(window).width() > 767) {
                $.fn.colorbox({
                    href: "booking-days_frame.php?lb3=yes&serviceID=<?php echo $serviceID ?>&name=<?php echo urlencode($name) ?>&phone=<?php echo urlencode($phone) ?>&email=<?php echo urlencode($email) ?>&comments=<?php echo urlencode($comments) ?>&dateFrom=<?php echo $dateFrom ?>&dateTo=<?php echo $dateTo ?>&couponCode=<?php echo urlencode($couponCode) ?>"
                });
            } else {

                window.location.href = "booking-days.php?lb3=yes&serviceID=<?php echo $serviceID ?>&name=<?php echo urlencode($name) ?>&phone=<?php echo urlencode($phone) ?>&email=<?php echo urlencode($email) ?>&comments=<?php echo urlencode($comments) ?>&dateFrom=<?php echo $dateFrom ?>&dateTo=<?php echo $dateTo ?>&couponCode=<?php echo urlencode($couponCode) ?>";
            }
        <?php } ?>
    });
</script>

<?php include "includes/footer.php" ?>