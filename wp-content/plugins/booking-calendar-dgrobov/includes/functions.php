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

function setupCalendarWP($iMonth, $iYear, $serviceID = 1, $baseDir = '/')
{


    $thismonth = false;
    $calendar = '';

    $iMonth2 = date('m', strtotime(date("Y") . "-" . $iMonth . "-01"));
    if (!$iMonth || !$iYear) {
        $iMonth = date('n');
        $iYear = date('Y');
    }

    ############################## BUILD BASE CALENDAR ################################
    $aCalendar = buildCalendar($iMonth, $iYear, $serviceID);
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

    ############################ PREPARE CALENDAR DATA #############################
    foreach ($aCalendar as $aWeek) {
        $calendar .= "<tr class='wp'>";
        foreach ($aWeek as $iDay => $mDay) {
            if ($iDay == '') {
                $calendar .= "<td colspan=\"" . $mDay . "\"  class=\"cal_reg_off\">&nbsp;</td>";
            } else {
                if (strlen($iDay) == 1) {
                    $iDay = '0' . $iDay;
                }
                $datetocheck = $iYear . "-" . $iMonth2 . "-" . $iDay;



                if ($datetocheck < date("Y-m-d")) {
                    $calendar .= "<td id=\"" . $iDay . "\" class='cal_reg_off past'><div class='day_number'>" . $iDay . "</div></td>";
                } else {


                    //we need to check reserved time by admin, in case this day is booked off by him.
                    ######################### EVENT CHECKER ###################################################################################################
                    $events = getEventsByDate($datetocheck, $serviceID);
                    $bgClass = "cal_reg_off";
                    $text = "";
                    $textTime = "";
                    if (count($events) > 0) {
                        //we have events for this day!


                        $bgClass = "cal_reg_on";
                        $event_num = count($events);
                        //we need to check if at least one event has spaces. if yes then { $bgClass="cal_reg_on";  } else { $bgClass="cal_reg_off"; }
                        $event_available = false;
                        $event_count = 0;





                        foreach ($events as $evt) {
                            $row = $evt['event'];
                            $spaces_left = $evt['qty'];

                            $click = getOption('use_popup') ? "getLightbox2('" . $row['id'] . "'," . $serviceID . ",'" . date("Y-m-d", strtotime($row['eventDate'])) . "');" : "location.href='{$baseDir}event-booking.php?eventID=" . urlencode($row['id']) . "&serviceID=" . $serviceID . "&date=" . date("Y-m-d", strtotime($row['eventDate'])) . "'";
                            if ($spaces_left > 0) {
                                $event_available = true;
                                $event_count++;
                            }
                            $style = empty($row['color']) ? "background-color:#fff;color:#666" : "background-color:{$row['color']}";
                            $styleDIV = empty($row['color']) ? "color:#666" : "color:#eee";
                            $text .= "<div onclick=\"{$click};event.stopPropagation();\" class='eventConteiner ' style='{$style}'>";
                            if (getServiceSettings($serviceID, 'show_event_titles')) {
                                $text .= "<div style='{$styleDIV}'>{$row['title']}</div>";
                            } else {
                                $text .= "<div style='{$styleDIV}'>" . TXT_EVENT2 . "</div>";
                            }
                            if (getServiceSettings($serviceID, 'show_event_image') && !empty($row['path'])) {

                                $text .= "<div><img src='{$baseDir}{$row['path']}' width='40'></div>";
                            }
                            if (getServiceSettings($serviceID, 'show_available_seats')) {

                                $text .= "<div>{$spaces_left} " . SEATS_AVAIL . "</div>";
                            }
                            $text .= "</div>";
                        }
                    }
                    //we dont have events for this day, lets check bookings.
                    // end EVENT CHECKER.
                    ########################################################################################################################################
                    if (getServiceSettings($serviceID, 'type') == 't') {

                        $cur_spots = checkSpotsLeft($datetocheck, $serviceID);

                        $showSpaces = getServiceSettings($serviceID, 'show_spaces_left');

                        $isAvailable = getScheduleService($serviceID, $datetocheck); //bw_dump($isAvailable);
                        $isAvailable = isset($isAvailable['availability'][$datetocheck]) ? true : false;


                        if ($cur_spots > 0) {
                            $bgClass = "cal_reg_on";
                            $clickTime = getOption('use_popup') ? "getLightbox('" . $datetocheck . "'," . $serviceID . ");" : "location.href='{$baseDir}booking.php?date=" . urlencode($datetocheck) . "&serviceID=" . $serviceID . "'";
                            $spotsText = ($showSpaces) ?  $cur_spots . SPC_AVAIL : BOOK_NOW;
                            $spotsText = '<div class="cal_text hide-me-for-nojs">' . $spotsText . "</div>";
                            //$text .="<div class='eventConteiner' onclick=\"{$clickTime}\">{$spotsText}</div>";
                            $textTime .= $spotsText;
                        } else {
                            $spotsText = "";
                            $textTime .= $spotsText;
                            $clickTime = '';
                        }



                        $calendar .= "<td id=\"" . $iDay . "\"  onclick=\"" . $clickTime . "\"";
                        if ($iCurrentDay != $iDay) {
                            $var = "";
                        } else {
                            $var = "_today";
                        }

                        if ($iCurrentDay != $iDay && $bgClass != "cal_reg_off") {
                            $calendar .= "onmouseover=\"this.className='mainmenu5';\" onmouseout=\"this.className='" . $bgClass . "';\" ";
                        } else if ($iCurrentDay == $iDay && $bgClass != "cal_reg_off") {
                            $calendar .= "onmouseover=\"this.className='mainmenu5';\" onmouseout=\"this.className='" . $bgClass . $var . "' \"";
                        }
                        $calendar .= "class=\"" . $bgClass . $var . "\"><div class='day_number'>" . $iDay;
                        if (!empty($textTime) || !empty($text)) {
                            $calendar .= "<div class='_showInfo'>" . $textTime . $text . "</div>";
                        }

                        $calendar .= "</div></td>";
                    } else {
                        $availability = checkAvailableDay($datetocheck, $serviceID);
                        if ($availability['res']) {
                            if (checkSpotsForDay($datetocheck, $serviceID) === true) {
                                $bgClass = "cal_reg_on";
                                //$clickTime = "getLightboxDays('" . $datetocheck . "'," . $serviceID . ");";
                                $clickTime = getOption('use_popup') ? "getLightboxDays('" . $datetocheck . "'," . $serviceID . ");" : "location.href='{$baseDir}booking-days.php?dateFrom=" . urlencode($datetocheck) . "&serviceID=" . $serviceID . "'";
                                $spotsText = '<div class="cal_text hide-me-for-nojs" >' . DAY_AVAIL . "</div>";
                                $textTime .= $spotsText;
                            } else {
                                $spotsText = "<span class='hide-me-for-nojs rightAlign'><br/>"  . DAY_BOOKED . "</span>";
                                if (checkForCheckoutDate($datetocheck, $serviceID)) {
                                    $bgClass .= " checking";
                                    $spotsText .= "<span class='checkoutSpan'>" . CHECKOUT_AVAILABLE . "</span>";
                                }
                                $textTime .= $spotsText;
                                $clickTime = '';
                            }
                        }
                        $calendar .= "<td id=\"" . $iDay . "\"  onclick=\"" . $clickTime . "\"";
                        if ($iCurrentDay != $iDay) {
                            $var = "";
                        } else {
                            $var = "_today";
                        }

                        if ($iCurrentDay != $iDay && $bgClass != "cal_reg_off" && $bgClass != "cal_reg_off checking") {
                            $calendar .= "onmouseover=\"this.className='mainmenu5';\" onmouseout=\"this.className='" . $bgClass . "';\" ";
                        } else if ($iCurrentDay == $iDay && $bgClass != "cal_reg_off") {
                            $calendar .= "onmouseover=\"this.className='mainmenu5';\" onmouseout=\"this.className='" . $bgClass . $var . "' \"";
                        }
                        $calendar .= "class=\"" . $bgClass . $var . "\"><div class='day_number'>" . $iDay;
                        if (!empty($textTime) || !empty($text)) {
                            $calendar .= "<div class='_showInfo'>" . $textTime . $text . "</div>";
                        }
                        //check if this day available for booking or not.

                        $calendar .= "</td>";
                    }
                } //end if iDay
            }
        }
        $calendar .= "</tr>";
    } //end foreach 
    ############################## END PREPARE CALENDAR DATA ################################

    return $calendar;
}

function setupSmallCalendar($iMonth, $iYear, $serviceID = 1, $baseDir = '/')
{

    $thismonth = false;

    $iMonth2 = date('m', strtotime(date("Y") . "-" . $iMonth . "-01"));
    if (!$iMonth || !$iYear) {
        $iMonth = date('n');
        $iYear = date('Y');
    }

    ############################## BUILD BASE CALENDAR ################################
    $aCalendar = buildCalendar($iMonth, $iYear, $serviceID);
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
    $calendar = "";
    ############################ PREPARE CALENDAR DATA #############################
    foreach ($aCalendar as $aWeek) {
        $calendar .= "<tr>";
        foreach ($aWeek as $iDay => $mDay) {
            if ($iDay == '') {
                $calendar .= "<td colspan=\"" . $mDay . "\"  class=\"cal_reg_off\">&nbsp;</td>";
            } else {
                if (strlen($iDay) == 1) {
                    $iDay = '0' . $iDay;
                }
                $datetocheck = $iYear . "-" . $iMonth2 . "-" . $iDay;



                if ($datetocheck < date("Y-m-d")) {
                    $calendar .= "<td id=\"" . $iDay . "\" class='cal_reg_off past'><div class='day_number'>" . $iDay . "</div></td>";
                } else {


                    //we need to check reserved time by admin, in case this day is booked off by him.
                    ######################### EVENT CHECKER ###################################################################################################
                    $events = getEventsByDate($datetocheck, $serviceID);
                    $bgClass = "cal_reg_off";
                    $text = "";
                    $textTime = "";
                    if (count($events) > 0) {
                        //we have events for this day!


                        $bgClass = "cal_reg_on";
                        $event_num = count($events);
                        //we need to check if at least one event has spaces. if yes then { $bgClass="cal_reg_on";  } else { $bgClass="cal_reg_off"; }
                        $event_available = false;
                        $event_count = 0;





                        foreach ($events as $evt) {
                            $row = $evt['event'];
                            $spaces_left = $evt['qty'];
                            $click = getOption('use_popup') ? "getLightbox2('" . $row['id'] . "'," . $serviceID . ",'" . date("Y-m-d", strtotime($row['eventDate'])) . "');" : "location.href='{$baseDir}event-booking.php?eventID=" . urlencode($row['id']) . "&serviceID=" . $serviceID . "&date=" . date("Y-m-d", strtotime($row['eventDate'])) . "'";
                            if ($spaces_left > 0) {
                                $event_available = true;
                                $event_count++;
                            }
                            $style = empty($row['color']) ? "background-color:#fff;color:#666" : "background-color:{$row['color']}";
                            $styleDIV = empty($row['color']) ? "color:#666" : "color:#eee";
                            $text .= "<div onclick=\"{$click};return false;\" class='eventConteiner'  style='{$style}'>";
                            if (getServiceSettings($serviceID, 'show_event_titles')) {
                                $text .= "<div style='{$styleDIV}'>{$row['title']}</div>";
                            } else {
                                $text .= "<div style='{$styleDIV}'>" . TXT_EVENT2 . "</div>";
                            }
                            if (getServiceSettings($serviceID, 'show_event_image') && !empty($row['path'])) {

                                $text .= "<div><img src='{$baseDir}{$row['path']}' width='40'></div>";
                            }
                            if (getServiceSettings($serviceID, 'show_available_seats')) {

                                $text .= "<div>{$spaces_left} " . SEATS_AVAIL . "</div>";
                            }
                            $text .= "</div>";
                        }
                    }
                    //we dont have events for this day, lets check bookings.
                    // end EVENT CHECKER.
                    ########################################################################################################################################
                    if (getServiceSettings($serviceID, 'type') == 't') {

                        $cur_spots = checkSpotsLeft($datetocheck, $serviceID);

                        $showSpaces = getServiceSettings($serviceID, 'show_spaces_left');


                        if ($cur_spots > 0) {
                            $bgClass = "cal_reg_on";
                            $clickTime = getOption('use_popup') ? "getLightbox('" . $datetocheck . "'," . $serviceID . ");" : "location.href='{$baseDir}booking.php?date=" . urlencode($datetocheck) . "&serviceID=" . $serviceID . "'";
                            $spotsText = ($showSpaces) ?  $cur_spots . SPC_AVAIL : BOOK_NOW;
                            $spotsText = '<div class="cal_text hide-me-for-nojs" onclick="' . $clickTime . '">' . $spotsText . "</div>";
                            $textTime .= $spotsText;
                        } else {
                            $spotsText = "";
                            $textTime .= $spotsText;
                            $clickTime = '';
                        }



                        $calendar .= "<td id=\"" . $iDay . "\"";
                        if ($iCurrentDay != $iDay) {
                            $var = "";
                        } else {
                            $var = "_today";
                        }

                        if ($iCurrentDay != $iDay && $bgClass != "cal_reg_off") {
                            $calendar .= "onmouseover=\"this.className='mainmenu5';\" onmouseout=\"this.className='" . $bgClass . "';\" ";
                        } else if ($iCurrentDay == $iDay && $bgClass != "cal_reg_off") {
                            $calendar .= "onmouseover=\"this.className='mainmenu5';\" onmouseout=\"this.className='" . $bgClass . $var . "' \"";
                        }
                        $calendar .= "class=\"" . $bgClass . $var . "\"><div class='day_number'>" . $iDay;
                        if (!empty($textTime) || !empty($text)) {
                            $calendar .= "<div class='showInfo'>" . $textTime . $text . "<b></b></div>";
                        }
                        //check if this day available for booking or not.
                        /* if(Empty($text)){
                          $calendar .= "<span class='hide-me-for-nojs'><br/>0 spaces available</span><noscript><br/>0 spaces available</noscript>";
                          } else {
                          $calendar .= "<div class='cal_text hide-me-for-nojs'>".$text."</div><noscript><br/><a href='event-booking-nojs.php?date=".$datetocheck."'>".$text."</a></noscript>";
                          } */
                        $calendar .= "</div></td>";
                    } else {
                        $availability = checkAvailableDay($datetocheck, $serviceID);
                        if ($availability['res']) {
                            if (checkSpotsForDay($datetocheck, $serviceID) === true) {
                                $bgClass = "cal_reg_on";
                                //$clickTime = "getLightboxDays('" . $datetocheck . "'," . $serviceID . ");";
                                $clickTime = getOption('use_popup') ? "getLightboxDays('" . $datetocheck . "'," . $serviceID . ");" : "location.href='{$baseDir}booking-days.php?dateFrom=" . urlencode($datetocheck) . "&serviceID=" . $serviceID . "'";
                                $spotsText = '<div class="cal_text hide-me-for-nojs"  onclick="' . $clickTime . '">' . DAY_AVAIL . "</div>";
                                $textTime .= $spotsText;
                            } else {
                                $spotsText = "<span class='hide-me-for-nojs'><br/>"  . DAY_BOOKED . "</span>";
                                $textTime .= $spotsText;
                                $clickTime = '';
                            }
                        }
                        $calendar .= "<td id=\"" . $iDay . "\"";
                        if ($iCurrentDay != $iDay) {
                            $var = "";
                        } else {
                            $var = "_today";
                        }

                        if ($iCurrentDay != $iDay && $bgClass != "cal_reg_off") {
                            $calendar .= "onmouseover=\"this.className='mainmenu5';\" onmouseout=\"this.className='" . $bgClass . "';\" ";
                        } else if ($iCurrentDay == $iDay && $bgClass != "cal_reg_off") {
                            $calendar .= "onmouseover=\"this.className='mainmenu5';\" onmouseout=\"this.className='" . $bgClass . $var . "' \"";
                        }
                        $calendar .= "class=\"" . $bgClass . $var . "\"><div class='day_number'>" . $iDay;
                        if (!empty($textTime) || !empty($text)) {
                            $calendar .= "<div class='showInfo'>" . $textTime . $text . "<b></b></div>";
                        }
                        //check if this day available for booking or not.

                        $calendar .= "</td>";
                    }
                } //end if iDay
            }
        }
        $calendar .= "</tr>";
    } //end foreach 
    ############################## END PREPARE CALENDAR DATA ################################

    return $calendar;
}

function getDefaultService()
{
    global $mysqli;
    $sql = "SELECT * FROM bs_services WHERE `default`='y'";
    $res = $mysqli->query($sql);
    if (!is_bool($res)) {
        if ($res->num_rows) {
            $res = $res->fetch_assoc();
        } else {

            $sql = "SELECT * FROM bs_services ORDER BY id ASC LIMIT 1";
            $res = $mysqli->query($sql);
            $res = $res->fetch_assoc();
        }
    } else {

        $sql = "SELECT * FROM bs_services ORDER BY id ASC LIMIT 1";
        $res = $mysqli->query($sql);
        $res = $res->fetch_assoc();
    }
    return $res['id'];
}

function setupCalendar($iMonth, $iYear, $serviceID = 1)
{
    $calendar = "";
    global $baseDir;
    $startDay = getServiceSettings($serviceID, 'startDay');
    $thismonth = false;

    $iMonth2 = date('m', strtotime(date("Y") . "-" . $iMonth . "-01"));
    if (!$iMonth || !$iYear) {
        $iMonth = date('n');
        $iYear = date('Y');
    }

    ############################## BUILD BASE CALENDAR ################################
    $aCalendar = buildCalendar($iMonth, $iYear, $serviceID);
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

    ############################ PREPARE CALENDAR DATA #############################
    foreach ($aCalendar as $aWeek) {
        $calendar .= "<tr>";
        foreach ($aWeek as $iDay => $mDay) {
            if ($iDay == '') {
                $calendar .= "<td colspan=\"" . $mDay . "\"  class=\"cal_reg_off\">&nbsp;</td>";
            } else {
                if (strlen($iDay) == 1) {
                    $iDay = '0' . $iDay;
                }
                $datetocheck = $iYear . "-" . $iMonth2 . "-" . $iDay;



                if ($datetocheck < date("Y-m-d")) {
                    $calendar .= "<td id=\"" . $iDay . "\" class='cal_reg_off past'>" . $iDay . "</td>";
                } else {


                    //we need to check reserved time by admin, in case this day is booked off by him.
                    ######################### EVENT CHECKER ###################################################################################################

                    $events = getEventsByDate($datetocheck, $serviceID);
                    $bgClass = "cal_reg_off";
                    $text = "";
                    $textTime = "";
                    if (count($events) > 0) {
                        //we have events for this day!


                        $bgClass = "cal_reg_on";
                        $event_num = count($events);
                        //we need to check if at least one event has spaces. if yes then { $bgClass="cal_reg_on";  } else { $bgClass="cal_reg_off"; }
                        $event_available = false;
                        $event_count = 0;





                        foreach ($events as $evt) {
                            $row = $evt['event'];
                            $spaces_left = $evt['qty'];
                            $click = getOption('use_popup') ? "getLightbox2('" . $row['id'] . "'," . $serviceID . ",'" . date("Y-m-d", strtotime($row['eventDate'])) . "');" : "window.location.href='event-booking.php?eventID=" . urlencode($row['id']) . "&serviceID=" . $serviceID . "&date=" . date("Y-m-d", strtotime($row['eventDate'])) . "'";

                            if ($spaces_left > 0) {
                                $event_available = true;
                                $event_count++;
                            } else {
                                $click = "javascript:;";
                            }
                            $style = empty($row['color']) ? "background-color:#fff;color:#666" : "background-color:{$row['color']}";
                            $styleDIV = empty($row['color']) ? "color:#666" : "color:#eee";
                            $text .= "<div onclick=\"{$click};event.stopPropagation()\" class='eventConteiner " . ($spaces_left < 1 ? "disabled" : "") . "'  style='{$style}'>";
                            if (getServiceSettings($serviceID, 'show_event_titles')) {
                                $text .= "<div style='{$styleDIV}'>{$row['title']}</div>";
                            } else {
                                $text .= "<div style='{$styleDIV}'>" . TXT_EVENT . "</div>";
                            }
                            if (getServiceSettings($serviceID, 'show_event_image') && !empty($row['path'])) {

                                $text .= "<div><img src='{$baseDir}{$row['path']}' width='40'></div>";
                            }
                            if (getServiceSettings($serviceID, 'show_available_seats')) {

                                $text .= "<div>{$spaces_left} " . SEATS_AVAIL . "</div>";
                            }
                            $text .= "</div>";
                        }
                    }

                    // end EVENT CHECKER.
                    ########################################################################################################################################

                    ##############  TIME BOOKING  ###################################
                    if (getServiceSettings($serviceID, 'type') == 't') {

                        $cur_spots = checkSpotsLeft($datetocheck, $serviceID); // THE NUMBER OF SESSIONS LEFT

                        $showSpaces = getServiceSettings($serviceID, 'show_spaces_left');
                        if ($cur_spots > 0) {
                            $bgClass = "cal_reg_on";
                            $clickTime = "window.location.href='booking.php?date=" . urlencode($datetocheck) . "&serviceID=" . $serviceID . "'";
                            $spotsText = ($showSpaces) ? '<div class="cal_text hide-me-for-nojs">' . $cur_spots . SPC_AVAIL . "</div>" : '<div class="cal_text hide-me-for-nojs" >' . BOOK_NOW . "</div>";
                            //$text .="<div class='eventConteiner' onclick=\"{$clickTime}\">{$spotsText}</div>";
                            $textTime .= $spotsText;
                        } else {
                            $spotsText = ""; //($showSpaces) ? "<span class='hide-me-for-nojs'><br/>" . $cur_spots . SPC_AVAIL."</span>" : "";
                            //$text .="<div class='eventConteiner' onclick=\"{$clickTime}\">{$spotsText}</div>";
                            $textTime .= $spotsText;
                            $clickTime = '';
                        }

                        $calendar .= "<td id=\"" . $iDay . "\"  onclick=\"" . $clickTime . "\"";
                        if ($iCurrentDay != $iDay) {
                            $var = "";
                        } else {
                            $var = "_today";
                        }

                        if ($iCurrentDay != $iDay && $bgClass != "cal_reg_off") {
                            $calendar .= "onmouseover=\"getElementById('" . $iDay . "').className='mainmenu5';\" onmouseout=\"getElementById('" . $iDay . "').className='" . $bgClass . "';\" ";
                        } else if ($iCurrentDay == $iDay && $bgClass != "cal_reg_off") {
                            $calendar .= "onmouseover=\"getElementById('" . $iDay . "').className='mainmenu5';\" onmouseout=\"getElementById('" . $iDay . "').className='" . $bgClass . $var . "' \"";
                        }
                        $calendar .= "class=\"" . $bgClass . $var . "\">" . $iDay;
                        $calendar .= $textTime . $text;

                        $calendar .= "</td>";
                    } else {

                        ######################  MULTI_DAY BOOKING  #########################################
                        $availability = checkAvailableDay($datetocheck, $serviceID);
                        if ($availability['res']) {

                            if (checkSpotsForDay($datetocheck, $serviceID) === true) {
                                $bgClass = "cal_reg_on";
                                $clickTime = getOption('use_popup') ? "getLightboxDays('" . $datetocheck . "'," . $serviceID . ");" : "window.location.href='booking-days.php?dateFrom=" . urlencode($datetocheck) . "&serviceID=" . $serviceID . "'";
                                $spotsText = '<div><div class="cal_text hide-me-for-nojs" >' . DAY_AVAIL . "</div>";

                                $spotsText .= !getServiceSettings($serviceID, 'showPrice') ? '<div class="cal_text hide-me-for-nojs" >' . getCurrencyText($availability['price']) . "</div></div>" : "";

                                $textTime .= $spotsText;
                            } else {
                                $spotsText = "<span class='hide-me-for-nojs rightAlign'>"  . DAY_BOOKED . "</span>";

                                if (checkForCheckoutDate($datetocheck, $serviceID)) {
                                    $bgClass .= " checking";
                                    $spotsText .= "<span class='checkoutSpan'>" . CHECKOUT_AVAILABLE . "</span>";
                                }
                                $textTime .= $spotsText;
                                $clickTime = '';
                            }
                        }
                        $calendar .= "<td id=\"" . $iDay . "\"  onclick=\"" . $clickTime . "\"";
                        if ($iCurrentDay != $iDay) {
                            $var = "";
                        } else {
                            $var = "_today";
                        }

                        if ($iCurrentDay != $iDay && $bgClass != "cal_reg_off" && $bgClass != "cal_reg_off checking") {
                            $calendar .= "onmouseover=\"getElementById('" . $iDay . "').className='mainmenu5 cursor';\" onmouseout=\"getElementById('" . $iDay . "').className='" . $bgClass . "';\" ";
                        } else if ($iCurrentDay == $iDay && $bgClass != "cal_reg_off") {
                            $calendar .= "onmouseover=\"getElementById('" . $iDay . "').className='mainmenu5 cursor';\" onmouseout=\"getElementById('" . $iDay . "').className='" . $bgClass . $var . "' \"";
                        }
                        $calendar .= "class=\"" . $bgClass . $var . "\" >" . $iDay;
                        $calendar .= $textTime . $text;
                        //check if this day available for booking or not.

                        $calendar .= "</td>";
                    }
                } //end if iDay
            }
        }
        $calendar .= "</tr>";
    } //end foreach 
    ############################## END PREPARE CALENDAR DATA ################################

    return $calendar;
}

function buildCalendar($iMonth, $iYear, $serviceID = 1)
{
    $myFirstDay = getServiceSettings($serviceID, 'startDay');
    $iFirstDayTimeStamp = mktime(0, 0, 0, $iMonth, 1, $iYear);
    $iFirstDayNum = date('w', $iFirstDayTimeStamp);
    $iFirstDayNum++;
    $iDayCount = date('t', $iFirstDayTimeStamp);
    $aCalendar = array();
    if ($myFirstDay == "0") {
        //SUNDAY
        if ($iFirstDayNum > 1) {
            $aCalendar[1][''] = $iFirstDayNum - 1; // how many empty squares before actual day 1.
        }
        $i = 1;
        $j = 1;

        while ($j <= $iDayCount) {
            $aCalendar[$i][$j] = $j;
            if (floor(($j + $iFirstDayNum - 1) / 7) >= $i) {
                $i++;
            }
            $j++;
        }
        if ((isset($aCalendar[$i])) && ($iM = count($aCalendar[$i])) < 7) {
            $aCalendar[$i][''] = 7 - $iM;
        }
    } else if ($myFirstDay == "1") {
        //MONDAY
        $iFirstDayNum--;
        if ($iFirstDayNum > 1 && $iFirstDayNum < 6) {
            //echo "off1";
            $tmp = 1;
            $aCalendar[1][''] = $iFirstDayNum - $tmp;
            $i = 1;
            $j = 1;

            while ($j <= $iDayCount) {
                $aCalendar[$i][$j] = $j;
                if (floor(($j + $iFirstDayNum - $tmp) / 7) >= $i) {
                    $i++;
                }
                $j++;
            }
            if ((isset($aCalendar[$i])) && ($iM = count($aCalendar[$i])) < 7) {
                $aCalendar[$i][''] = 7 - $iM; //last row - how many empty squares.
            }
        } else if ($iFirstDayNum == 0) {

            //echo "off2";
            $tmp = 1;
            $aCalendar[1][''] = 6;
            $i = 1;
            $j = 1;

            while ($j <= $iDayCount) {
                $aCalendar[$i][$j] = $j;
                if (floor(($j + $iFirstDayNum + 6) / 7) >= $i) {
                    $i++;
                }
                $j++;
            }
            if ((isset($aCalendar[$i])) && ($iM = count($aCalendar[$i])) < 7) {
                $aCalendar[$i][''] = 7 - $iM; //last row - how many empty squares.
            }
        } else if ($iFirstDayNum == 6) {

            //echo "off2";
            $tmp = 1;
            $aCalendar[1][''] = 5;
            $i = 1;
            $j = 1;

            while ($j <= $iDayCount) {
                $aCalendar[$i][$j] = $j;
                if (floor(($j + $iFirstDayNum - 1) / 7) >= $i) {
                    $i++;
                }
                $j++;
            }
            if ((isset($aCalendar[$i])) && ($iM = count($aCalendar[$i])) < 7) {
                $aCalendar[$i][''] = 7 - $iM; //last row - how many empty squares.
            }
        } else {
            //echo "off3";
            //echo $iFirstDayNum;
            $tmp = 1;
            $i = 1;
            $j = 1;

            while ($j <= $iDayCount) {
                $aCalendar[$i][$j] = $j;
                if (floor(($j + $iFirstDayNum - $tmp) / 7) >= $i) {
                    $i++;
                }
                $j++;
            }
            if ((isset($aCalendar[$i])) && ($iM = count($aCalendar[$i])) < 7) {
                $aCalendar[$i][''] = 7 - $iM; //last row - how many empty squares.
            }
        }
    }
    return $aCalendar;
}

function nextMonth($iMonth, $iYear)
{
    if ($iMonth == 12) {
        $iMonth = 1;
        $iYear++;
    } else {
        $iMonth++;
    }
    return array($iMonth, $iYear);
}

function nextDay($iDay, $iMonth, $iYear)
{
    $iDayTimestamp = mktime(0, 0, 0, $iMonth, $iDay, $iYear);
    $iNextDayTimestamp = strtotime('+1 day', $iDayTimestamp);
    return $iNextDayTimestamp;
}

function prevDay($iDay, $iMonth, $iYear)
{
    $iDayTimestamp = mktime(0, 0, 0, $iMonth, $iDay, $iYear);
    $iPrevDayTimestamp = strtotime('-1 day', $iDayTimestamp);
    return $iPrevDayTimestamp;
}

function prevMonth($iMonth, $iYear)
{
    if ($iMonth == 1) {
        $iMonth = 12;
        $iYear--;
    } else {
        $iMonth--;
    }
    return array($iMonth, $iYear);
}

function getMaxSecondsForThisDay($day)
{
    global $mysqli;
    $tt = 0;
    $q = "SELECT * FROM bs_settings WHERE id='1'";
    $res = $mysqli->query($q);
    $rr = $res->fetch_assoc();
    /* if(!empty($rr[$day."_from"]) && $rr[$day."_from"]!="N/A"){ $from = explode(":",$rr[$day."_from"]); } else { $from[0]=0; }
      if(!empty($rr[$day."_to"]) && $rr[$day."_to"]!="N/A"){ $to = explode(":",$rr[$day."_to"]);} else { $to[0]=0; } */ //LEFTOVERS FROM V2
    if (!empty($rr[$day . "_from"]) && $rr[$day . "_from"] != "N/A" && $rr[$day . "_from"] != "0") {
        $from = $rr[$day . "_from"] / 60;
    } else {
        $from = 0;
    }
    if (!empty($rr[$day . "_to"]) && $rr[$day . "_to"] != "N/A" && $rr[$day . "_to"] != "0") {
        $to = $rr[$day . "_to"] / 60;
    } else {
        $to = 0;
    }
    $tt = (($to - $from) * 60) * 60;
    return $tt;
}

function getStartEndTime($day, $serviceID = 1)
{
    global $mysqli;
    $tt = array();
    $tt[0] = 0;
    $tt[1] = 0;
    $q = "SELECT * FROM bs_service_settings WHERE serviceId='{$serviceID}'";
    $res = $mysqli->query($q);
    $rr = $res->fetch_assoc();

    if (!empty($rr[$day . "_from"]) && $rr[$day . "_from"] != "N/A" && $rr[$day . "_from"] != "0") {
        $from = $rr[$day . "_from"];
    } else {
        $from = 0;
    }
    if (!empty($rr[$day . "_to"]) && $rr[$day . "_to"] != "N/A" && $rr[$day . "_to"] != "0") {
        $to = $rr[$day . "_to"];
    } else {
        $to = 0;
    }

    $tt[0] = ($from - ($from % 60)) / 60;
    $tt[1] = ($to - ($to % 60)) / 60;
    $tt[2] = $from;
    $tt[3] = $to;
    //print var_dump($tt);
    return $tt;
}




function getSpotsLeftForEvent($id, $date = null)
{
    global $mysqli;
    $q = "SELECT payment_required,spaces FROM bs_events WHERE id='" . $id . "'";
    $res = $mysqli->query($q);
    $rr = $res->fetch_assoc();
    $space = $rr["spaces"];
    //if($rr["payment_required"]=="1"){ $status = "4";} else { $status = "1"; }
    $where = !empty($date) ? "AND date = '" . date("Y-m-d", strtotime($date)) . "'" : "";
    $q = "SELECT SUM(qty) as num FROM bs_reservations WHERE eventID='" . $id . "' AND (status='1' OR status='4') {$where}";
    $res = $mysqli->query($q);
    $rr = $res->fetch_assoc();

    return $space - $rr["num"];
}

function getMaxBooking($serviceID = 1)
{
    global $mysqli;
    $q = "SELECT * FROM bs_service_settings WHERE serviceId ='{$serviceID}'";
    $res = $mysqli->query($q);
    $rr = $res->fetch_assoc();
    return $rr["allow_times"];
}

function getMinBooking($serviceID = 1)
{
    global $mysqli;
    $q = "SELECT * FROM bs_service_settings WHERE serviceId ='{$serviceID}'";
    $res = $mysqli->query($q);
    $rr = $res->fetch_assoc();
    return $rr["allow_times_min"];
}

function getInterval($serviceID = 1)
{
    global $mysqli;
    $q = "SELECT `interval` FROM bs_service_settings WHERE serviceId ='{$serviceID}'";
    $res = $mysqli->query($q);
    $rr = $res->fetch_assoc(); //print $rr["interval"];
    return $rr["interval"];
}

function getBookings($date, $time, $serviceID = 1)
{
    global $mysqli;
    $text = "";
    //if($time<10){ $time = "0".$time; }
    $q = "SELECT a.*, b.reason FROM bs_reserved_time_items a, bs_reserved_time b WHERE a.dateFrom LIKE '" . $date . " " . $time . "%' AND a.reservedID=b.id AND b.serviceID={$serviceID} ORDER BY a.dateFrom ASC LIMIT 1";
    $res = $mysqli->query($q);
    if ($res->num_rows > 0) {
        while ($rr = $res->fetch_assoc()) {
            $text .= TXT_RESERVED . $rr["reason"] . "<br/>";
        }
    }
    $q = "SELECT bs_reservations.* FROM `bs_reservations` INNER JOIN bs_reservations_items  on bs_reservations_items.reservationID = bs_reservations.id  WHERE (bs_reservations.status='1' OR bs_reservations.status='4') AND bs_reservations_items.reserveDateFrom LIKE '" . $date . " " . $time . "%' AND `bs_reservations`.serviceID={$serviceID} ORDER BY bs_reservations_items.reserveDateFrom ASC  LIMIT 1";
    $res = $mysqli->query($q);
    if ($res->num_rows > 0) {
        while ($rr = $res->fetch_assoc()) {
            $text .= "<a href='bs-bookings-edit.php?id=" . $rr["id"] . "'>" . $rr["name"] . " (" . $rr["phone"] . ")</a><br/>";
        }
    }
    return $text;
}

function getInfoByReservID($reservID)
{
    global $mysqli;
    $q = "SELECT * FROM bs_reservations WHERE id='" . $reservID . "'";
    $res = $mysqli->query($q);
    $rr = $res->fetch_assoc();
    $t = array();
    $t[0] = $rr["name"];
    $t[1] = $rr["email"];
    $t[2] = $rr["qty"];
    $t[3] = $rr["serviceID"];
    $t[4] = $rr["date"];

    return $t;
}
function getEventStartEndDate($id, $date, $type = 'text')
{
    global $mysqli;
    $text = "";
    $q = "SELECT * FROM bs_events WHERE id='" . $id . "'";
    $res = $mysqli->query($q);
    if ($res->num_rows < 1)
        return false;
    $rr = $res->fetch_assoc();
    $date = empty($date) ? $rr["eventDate"] : $date;
    $startTime = date("H:i", strtotime($rr["eventDate"]));
    $startDate = date("Y-m-d", strtotime($date));
    $endTime = date("H:i", strtotime($rr["eventDateEnd"]));
    $endDate = date("Y-m-d", strtotime($date));
    if (date("d-m-Y", strtotime($rr["eventDate"])) == date("d-m-Y", strtotime($rr["eventDateEnd"]))) {
        $text  .= getDateFormat($date) . " " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDate"])) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDateEnd"]));
    } else {
        $interval = strtotime(_date($rr["eventDateEnd"])) - strtotime(_date($rr["eventDate"]));
        $start = getDateFormat(date("Y-m-d", strtotime($date))) . " " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDate"]));


        $end = getDateFormat(date("Y-m-d", strtotime($date . " " . date("H:i", strtotime($rr["eventDate"])) . " +$interval seconds"))) . " " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDateEnd"]));

        $endDate = date("Y-m-d", strtotime("$date  +$interval seconds"));
        $text = "From: $start  To: $end";
    }
    if ($type == 'text') {
        return $text;
    } else {
        return array("from" => "$startDate $startTime", "to" => "$endDate $endTime", 'fromDate' => $startDate, 'toDate' => $endDate);
    }
}
function getEventInfo($id)
{
    global $mysqli;
    $t = array();
    $q = "SELECT * FROM bs_events WHERE id='" . $id . "'";
    $res = $mysqli->query($q);
    if ($res->num_rows < 1)
        return false;
    $rr = $res->fetch_assoc();

    $t = $rr;
    $t[0] = $rr["title"];
    $t[1] = $rr["description"];
    if (date("d-m-Y", strtotime($rr["eventDate"])) == date("d-m-Y", strtotime($rr["eventDateEnd"]))) {
        $t[2] = getDateFormat($rr["eventDate"]) . " " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDate"])) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDateEnd"]));
    } else {
        $t[2] = "from: " . getDateFormat($rr["eventDate"]) . " " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDate"]));
        $t[2] .= " to: " . getDateFormat($rr["eventDateEnd"]) . " " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDateEnd"]));
    }
    $t[3] = $rr["payment_required"];
    $t[4] = $rr["entryFee"];
    $t[5] = $rr["payment_method"];
    $t[6] = $rr["serviceID"];
    $t[7] = date("Y-m-d", strtotime($rr["eventDate"]));




    return $t;
}

function getOrderSummery($orderId, $date = null)
{
    $info = '';
    global $mysqli;
    bw_apply_filter("pre_order_summery", $info, $orderId);

    $info .= "<div class='orderSummery'>";

    $currency = getOption('currency');
    $currencyPos = getOption('currency_position');

    $paid = false;
    $deposit = 1;

    $orderInfo = getBooking($orderId);

    $serviceSettings = getServiceSettings($orderInfo['serviceID']);

    if (!empty($orderInfo['eventID'])) {
        $eventInfo = getEventInfo($orderInfo['eventID']);
        $eventDates =  getEventStartEndDate($orderInfo['eventID'], $date, "array");
        $info .= "<h2>" . ORDER_EVENT_INF . "</h2>";
        $info .= "<ul class='summery'>";
        $info .= "<li><label>" . EVENT_TTL . ":</label>{$eventInfo['title']}</li>";
        $info .= "<li><label>" . EVENT_DISCRP . ":</label><span>" . nl2br($eventInfo['description']) . "</span></li>";
        $info .= "<li><label>" . TXT_EVENT_START . ":</label>" . getDateFormat($eventDates['from']) . " " . _time($eventDates['from']) . "</li>";
        $info .= "<li><label>" . TXT_EVENT_ENDS . ":</label>" . getDateFormat($eventDates['to']) . " " . _time($eventDates['to']) . "</li>";
        $info .= "<li><label>" . ORDER_BOOKING_QTY . ":</label>{$orderInfo['qty']}</li>";
        $info .= "</ul><div style='clear:both'></div>";
        if ($eventInfo['payment_required'] == 1 && $eventInfo['entryFee'] > 0) {
            $paid = true;
        }
        $deposit = $eventInfo['deposit'];
    } else {

        if ($serviceSettings['type'] == 't') {
            $info .= "<h2>" . ORDER_BOOKING_INF . "</h2>";
            $info .= "<ul class='summery'>";
            $booking_times = '';
            $booking_date = '';
            $bookint_times_count = 0;
            $sSQL = "SELECT * FROM bs_reservations_items WHERE reservationID='" . $orderId . "' ORDER BY reserveDateFrom ASC";
            $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
            while ($row = $result->fetch_assoc()) {

                $booking_times .= _time($row["reserveDateFrom"]) . " - " .
                    _time($row["reserveDateTo"]) . "<br/>";
                $booking_date = getDateFormat($row["reserveDateTo"]);
                $bookint_times_count++;
            }
            $info .= "<li><label>" . ORDER_BOOKING_DATE . ":</label>{$booking_date}</li>";
            $info .= "<li><label>" . ORDER_BOOKING_TIME . ":</label><span>{$booking_times}</span></li>";
            $info .= "<li><label>" . ORDER_BOOKING_QTY . ":</label>{$orderInfo['qty']}</li>";
            $price = getServiceSettings($orderInfo['serviceID'], 'spot_price');
            $deposit = getService($orderInfo['serviceID'], 'deposit');
            if ($price > 0) {

                $price = number_format($price, 2);
                $paid = true;
                $info .= "<li><label>" . PRICE . ":</label>" . ($currencyPos == 'b' ? $currency : "") . " {$price} " . ($currencyPos == 'a' ? $currency : "") . "</li>";
            }
            $info .= "</ul><div style='clear:both'></div>";
        } else {
            $info .= "<h2>" . ORDER_BOOKING_INF . "</h2>";
            $info .= "<ul class='summery'>";
            /*$booking_times = '';
            $booking_date = '';
            $bookint_times_count = 0;*/
            $sSQL = "SELECT * FROM bs_reservations_items WHERE reservationID='" . $orderId . "' ORDER BY reserveDateFrom ASC";
            $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
            $bookInfo = $result->fetch_assoc();
            $bookingSummery = _checkForAvailability($bookInfo['reserveDateFrom'], $bookInfo['reserveDateTo'], $orderInfo['serviceID']);
            $deposit = getService($orderInfo['serviceID'], 'deposit');
            $paid = $bookingSummery['totalPrice'] > 0 ? true : false;
            foreach ($bookingSummery['info'] as $k => $v) {
                $info .= "<li><label>" . ORDER_DATE_FROM . ":</label>" .  getDateFormat($v['from']) . "</li>";
                $info .= "<li><label>" . ORDER_DATE_TO . ":</label>" .  getDateFormat($v['to']) . "</li>";
                $info .= "<li><label>" . ORDER_DAYS . ":</label>" . (getDaysInterval($v['from'], $v['to'])) . "</li>";
                $price = number_format($v['_price'], 2);

                $info .= "<li><label>" . PRICE . ":</label>" . ($currencyPos == 'b' ? "$currency " : "") . "{$price} " . ($currencyPos == 'a' ? $currency : "") . "</li>";
                $info .= "<li>&nbsp;</li>";
            }
            $info .= "</ul><div style='clear:both'></div>";
        }
    }
    if ($paid) {
        $orderPymentInfo = get_payment_info($orderId);
        $amount = number_format($orderPymentInfo['amount'], 2);
        $subTotal = number_format($orderPymentInfo['subAmount'], 2);
        $_subTotal = number_format($orderPymentInfo['_subAmount'], 2);
        $tax = number_format($orderPymentInfo['tax'], 2);
        $taxRate = $orderPymentInfo['taxRate'];
        $discount = $orderPymentInfo['discount'];

        $info .= "<h2>" . ORDER_SUMMERY . "</h2>";
        $info .= "<ul class='summery'>";

        if (!empty($tax) && $tax > 0) {
            $info .= "<li><label>" . ORDER_SUBTOTAL . ":</label>" . getCurrencyText($subTotal) . " " . (!empty($discount) ? "(<del>" . getCurrencyText($_subTotal) . "</del>)" : "") . "</li>";
            if (!empty($discount)) {

                $info .= "<li><label>" . ORDER_DISCOUNT . ":</label>" . ($discount) . " </li>";
            }
            $info .= "<li><label>" . ORDER_TAX . ":</label>" . getCurrencyText($tax) . " ( $taxRate % )</li>";
            $info .= "<li class='total'><label>" . ORDER_TOTAL . ":</label>" . getCurrencyText($amount) . "</li>";
            if ($deposit < 1) {
                $info .= "<li class='_total'><label>" . ORDER_TO_PAY . ":</label>" . getCurrencyText(number_format($orderPymentInfo['amount'] * $deposit, 2)) . " <small>( " . ($deposit * 100) . "% )</small> </li>";
            }
        } else {
            if (!empty($discount)) {
                $info .= "<li><label>" . ORDER_SUBTOTAL . ":</label>" . getCurrencyText($_subTotal) . " </li>";
                $info .= "<li><label>" . ORDER_DISCOUNT . ":</label>" . ($discount) . " </li>";
            }
            $info .= "<li class='total'><label>" . ORDER_TOTAL . ":</label>" . getCurrencyText($amount) . "</li>";
            if ($deposit < 1) {
                $info .= "<li class='_total'><label>" . ORDER_TO_PAY . ":</label>" . getCurrencyText(number_format($orderPymentInfo['amount'] * $deposit, 2)) . " <small>( " . ($deposit * 100) . "% )</small> </li>";
            }
        }
        $info .= "</ul>";
    }


    $info .= "<div style='clear:both'></div></div>";
    //print $info;
    return bw_apply_filter("order_summery", $info, $orderId);
}

function getAdminMail()
{
    return getOption("email");
}

function getTimeMode()
{

    return getOption("time_mode");
}

function getAdminPaypal()
{

    $tt = array();
    $tt[0] = getOption("pemail");
    $tt[1] = getOption("pcurrency");
    return $tt;
}

function checkSpotsLeft($date, $serviceID = 1)
{
    $spots = 0; //print $serviceID;
    $serviceSettings = getServiceSettings($serviceID);
    $show_multiple_spaces = $serviceSettings['show_multiple_spaces']; //check option for multiple timeBooking
    $availebleSpaces = $show_multiple_spaces ? $serviceSettings['spaces_available'] : 1;
    $timeBefore = $serviceSettings['time_before'];

    ##########################################################################################################################
    ##########################################################################################################################
    # PREPARE AVAILABILITY ARRAY 
    $schedule = getScheduleService($serviceID, $date);
    $availabilityArr = $schedule['availability'];
    $events = $schedule['events'];
    $admins = $schedule['admins'];
    $users = $schedule['users'];
    $n = $schedule['countItems'];
    $currTime = strtotime(date("Y-m-d H:i"));


    foreach ($availabilityArr as $k => $v) { //$v= date  (  2010-10-05 )
        foreach ($v as $kk => $vv) { //$vv = time slot in above date 
            //echo $vv;
            $spotTimeStart = strtotime(date("Y-m-d", strtotime($k)) . " $vv:00 -5 minutes"); //5-minutes befo select interval in past

            $spotTimeStart = $timeBefore > 0 ? strtotime(_date($date) . " $vv:00  -{$timeBefore} hours") : $spotTimeStart;

            if (isset($events[$k]) && in_array($vv, $events[$k])) { } elseif (isset($admins[$k]) && array_key_exists($vv, $admins[$k])) {

                //current timestamp
                $spacesBooked = $admins[$k][$vv];
                $spacesAllowed = $availebleSpaces - $spacesBooked;

                //timestamp on start time interval


                if (isset($users[$k]) && array_key_exists($vv, $users[$k])) {

                    //current timestamp
                    $spacesBooked = $users[$k][$vv];
                    $spacesAllowed = $spacesAllowed - $spacesBooked;

                    //timestamp on start time interval
                }
                if ($spotTimeStart <= $currTime) {
                    //this interval passed already.
                } elseif ($spacesAllowed >= 1) {
                    $spots += $spacesAllowed;
                }
            } elseif (isset($users[$k]) && array_key_exists($vv, $users[$k])) {

                //current timestamp
                $spacesBooked = $users[$k][$vv];
                $spacesAllowed = $availebleSpaces - $spacesBooked;

                //timestamp on start time interval

                if ($spotTimeStart < $currTime) {
                    //this interval passed already.
                } elseif ($spacesAllowed >= 1) {
                    $spots += $spacesAllowed;
                }
            } else {
                if ($spotTimeStart > $currTime) {
                    $spots += $availebleSpaces;
                }
            }
        }
    }

    return $spots;
}

function checkForEvents($from, $to, $serviceID)
{

    $date = date("Y-m-d", strtotime($from));
    $eventsList = getEventsByDate($date, $serviceID);
    $to = strtotime($to . ":00");
    $from = strtotime($from . ":00");
    //print "$from $to<br>";
    //bw_dump($event);
    if (count($eventsList) > 0) {
        foreach ($eventsList as $event) {

            $event['eventDate'] = strtotime($event['event']['eventDate']);
            $event['eventDateEnd'] = strtotime($event['event']['eventDateEnd']);

            if (($event['eventDate'] < $to and $event['eventDateEnd'] >= $to) or ($event['eventDateEnd'] > $from and $event['eventDate'] <= $from) or ($event['eventDate'] <= $from and $event['eventDateEnd'] >= $to) or ($event['eventDate'] >= $from and $event['eventDateEnd'] <= $to)
            ) {
                return true;
            }
        }
    }
    return false;
}
function getAdminReserveData($from, $to, $serviceID)
{
    global $mysqli;
    $qty = 0;
    $qtyTmp = 0;
    $data = array();
    $recurring = array();
    $date = date("Y-m-d", strtotime($from)); //print $date;
    $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=1 AND reserveDateTo>='{$to}'"; //print $sSQL;
    $res = $mysqli->query($sSQL);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {

            $startDate = date("Y-m-d", strtotime($row['reserveDateFrom']));
            $endDate = date("Y-m-d", strtotime($row['reserveDateTo']));
            $startTime = date("H:i", strtotime($row['reserveDateFrom']));
            $endTime = date("H:i", strtotime($row['reserveDateTo']));
            $st = $startDate;
            $en = $endDate;
            $j = 0;
            for ($i = $st; $i <= $date . " 23:59:59"; $i = date("Y-m-d", strtotime($i . " +{$row['repeate_interval']} {$row['repeate']}"))) {
                //print $i;
                $reserveDateFrom = $date . " " . $startTime;
                $reserveDateTo = $date . " " . $endTime;
                if ($date == date("Y-m-d", strtotime($i))) {

                    if (($reserveDateFrom < $to and $reserveDateTo >= $to) or ($reserveDateTo > $from and $reserveDateFrom <= $from) or ($reserveDateFrom <= $from and $reserveDateTo >= $to) or ($reserveDateFrom > $from and $reserveDateTo <= $to)
                    ) {
                        $recurring[$row['qty']] = array("start" => $reserveDateFrom, "end" => $reserveDateTo);
                        $qtyTmp += intval($row['qty']);
                        $data[$row['id']]['reason'] = $row['reason'];
                        $data[$row['id']]['qty'] = $qtyTmp;
                    }
                }

                //$i=$b;
                $j++;
                if ($j > 1000) {
                    $message = "error to match iterations 'function checkForAdminReserv 
                         from=$from
                         to=$to
                         serviceID=$serviceID'";
                    _error_log($message);
                    break;
                }
            }
        }
    }
    //bw_dump($recurring);
    //print $qtyTmp."-";
    $qty = $qtyTmp;
    $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=0 AND(
				(reserveDateFrom < '{$to}' AND reserveDateTo >= '{$to}') OR
				(reserveDateTo > '{$from}' AND reserveDateFrom <= '{$from}') OR
				(reserveDateFrom <= '{$from}' AND reserveDateTo >= '{$to}') OR
				(reserveDateFrom >= '{$from}' AND reserveDateTo <= '{$to}'))";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $qty = $row['qty'];
            $data[$row['id']]['reason'] = $row['reason'];
            $data[$row['id']]['qty'] = $qty;
        }
    } else {
        //return false;
    }

    return $data;
}
function checkForAdminReserv($from, $to, $serviceID)
{
    global $mysqli;
    //print $from." - ".$to."<br>";
    $qty = 0;
    $qtyTmp = 0;
    $recurring = array();
    $date = date("Y-m-d", strtotime($from)); //print $date;
    $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=1 AND reserveDateTo>='{$to}'"; //print $sSQL;
    $res = $mysqli->query($sSQL);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {

            $startDate = date("Y-m-d", strtotime($row['reserveDateFrom']));
            $endDate = date("Y-m-d", strtotime($row['reserveDateTo']));
            $startTime = date("H:i", strtotime($row['reserveDateFrom']));
            $endTime = date("H:i", strtotime($row['reserveDateTo']));
            $st = $startDate;
            $en = $endDate;
            $j = 0;
            for ($i = $st; $i <= $date . " 23:59:59"; $i = date("Y-m-d", strtotime($i . " +{$row['repeate_interval']} {$row['repeate']}"))) {
                //print $i;
                $reserveDateFrom = $date . " " . $startTime;
                $reserveDateTo = $date . " " . $endTime;
                if ($date == date("Y-m-d", strtotime($i))) {

                    if (($reserveDateFrom < $to and $reserveDateTo >= $to) or ($reserveDateTo > $from and $reserveDateFrom <= $from) or ($reserveDateFrom <= $from and $reserveDateTo >= $to) or ($reserveDateFrom > $from and $reserveDateTo <= $to)
                    ) {
                        $recurring[$row['qty']] = array("start" => $reserveDateFrom, "end" => $reserveDateTo);
                        $qtyTmp += intval($row['qty']);
                    }
                }

                //$i=$b;
                $j++;
                if ($j > 1000) {
                    $message = "error to match iterations 'function checkForAdminReserv 
                         from=$from
                         to=$to
                         serviceID=$serviceID'";
                    _error_log($message);
                    break;
                }
            }
        }
    }
    //bw_dump($recurring);
    //print $qtyTmp."-";
    $qty = $qtyTmp;
    $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=0 AND(
				(reserveDateFrom < '{$to}' AND reserveDateTo >= '{$to}') OR
				(reserveDateTo > '{$from}' AND reserveDateFrom <= '{$from}') OR
				(reserveDateFrom <= '{$from}' AND reserveDateTo >= '{$to}') OR
				(reserveDateFrom >= '{$from}' AND reserveDateTo <= '{$to}'))";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $qty += $row['qty'];
        }
    } else {
        //return false;
    }

    return $qty;
}
function getEventsByDate($datetocheck, $serviceID = null)
{
    global $mysqli;
    $where = "";
    if (!empty($serviceID)) {
        $where = " AND serviceID='{$serviceID}'";
    }
    $query = "SELECT * FROM bs_events WHERE eventDate <= '" . $datetocheck . " 23:59' AND recurringEndDate>={$datetocheck} $where AND recurring=1 ORDER BY eventDate ASC ";
    $result = $mysqli->query($query);
    $events = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $startDate = date("Y-m-d", strtotime($row['eventDate']));
            $startTime = date("H:i", strtotime($row['eventDate']));
            $endDate = date("Y-m-d", strtotime($row['eventDateEnd']));
            $endTime = date("H:i", strtotime($row['eventDateEnd']));
            $interval = strtotime($row['eventDateEnd']) - strtotime($row['eventDate']);
            $st = $startDate;
            $j = 0;

            for ($i = $st; $i <= $row['recurringEndDate'] . " 23:59:59"; $i = date("Y-m-d", strtotime($i . " +{$row['repeate_interval']} {$row['repeate']}"))) {
                //print $i;
                $reserveDateFrom = $i;
                $reserveDateTo = date("Y-m-d", strtotime("$i +$interval seconds"));


                if (strtotime($datetocheck) <= strtotime($reserveDateTo) && strtotime($datetocheck) >= strtotime($reserveDateFrom)) {
                    $row['eventDate'] = "$reserveDateFrom $startTime";
                    $row['eventDateEnd'] = "$reserveDateTo $endTime";
                    $events[] = array("event" => $row, "qty" => getSpotsLeftForEvent($row['id'], $reserveDateFrom));
                }

                //$i=$b;
                $j++;
                if ($j > 1000) {
                    $message = "error to match iterations 'function getEventsByDate 
                         from=$datetocheck
                         serviceID=$serviceID'
                         startDate=$startDate";
                    _error_log($message);
                    break;
                }
            }
        }
    }
    $query = "SELECT * FROM bs_events WHERE eventDate <= '" . $datetocheck . " 23:59' AND eventDateEnd >= '" . $datetocheck . " 00:00' $where  AND recurring=0 ORDER BY eventDate ASC ";
    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $events[] = array("event" => $row, "qty" => getSpotsLeftForEvent($row['id']));
        }
    }
    return $events;
}
function checkForUserReserv($from, $to, $serviceID)
{
    global $mysqli;
    $qty = 0;
    $sSQL = "SELECT bri.* FROM `bs_reservations_items` bri
			INNER JOIN bs_reservations br on bri.reservationID = br.id
				WHERE br.serviceID='{$serviceID}' AND (
				(bri.reserveDateFrom < '{$to}' AND bri.reserveDateTo >= '{$to}') OR
				(bri.reserveDateTo > '{$from}' AND bri.reserveDateFrom <= '{$from}') OR
				(bri.reserveDateFrom <= '{$from}' AND bri.reserveDateTo >= '{$to}') OR
				(bri.reserveDateFrom >= '{$from}' AND bri.reserveDateTo <= '{$to}'))
				AND (br.status='1' OR br.status='4')  
				ORDER BY bri.reserveDateFrom ASC";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $qty += $row['qty'];
        }
        return $qty;
    } else {
        return false;
    }
}

function checkTimesIntervals($serviceID, $date, $from, $to)
{
    $responce = array("res" => true, "message" => "");
    $message = "";
    $availability = getScheduleService($serviceID, _date($date));
    //bw_dump($availability);
    $availableTimesFrom = $availability['availability'][_date($date)];
    if (is_array($availableTimesFrom)) {
        $_availableTimesFrom = array_flip($availableTimesFrom);
    } else {
        $message .= "Start time '$from' out of availability. Check your input";
        $responce['res'] = false;
        $responce['message'] = $message;
        return $responce;
    }
    $availableTimesTo = array();
    $interval = getInterval($serviceID);
    foreach ($availableTimesFrom as $k => $v) {
        $availableTimesTo[$k] = date("H:i", strtotime("2000-01-01 $v +$interval minutes"));
    }

    //bw_dump($availableTimesFrom);
    //bw_dump($availableTimesTo);
    if (!in_array($from, $availableTimesFrom)) {
        $message .= "Start time '$from' out of availability. Check your input";
        $responce['res'] = false;
    } elseif ($to != $availableTimesTo[$_availableTimesFrom[$from]]) {
        $message .= "Incorrect end time for start time '$from', should be a '{$availableTimesTo[$_availableTimesFrom[$from]]}'. Check your input.";
        $responce['res'] = false;
    }
    $responce['message'] = $message;
    return $responce;
}

function checkSpotsForTimeInterval($serviceID, $from, $to, $qty, $id)
{

    global $mysqli;
    $spots = getServiceSettings($serviceID, 'spaces_available');

    $sql = "SELECT SUM(qty) as sum FROM bs_reservations_items WHERE reservationID<>'" . $id . "' AND reserveDateFrom='{$from}' AND reserveDateTo='{$to}'";
    $res = $mysqli->query($sql);
    $reserved = $res->fetch_assoc()['sum'];


    return $spots - $reserved;
}

function getScheduleService($idService, $date)
{
    global $mysqli;
    $availabilityArr = array();
    $events = array();
    $admins = array();
    $users = array();
    $int = getInterval($idService);

    $dayOfWeek = date("w", strtotime($date));
    $sql = "SELECT * FROM bs_schedule
			WHERE idService='{$idService}' AND week_num='{$dayOfWeek}' ORDER BY startTime ASC"; //print $sql;
    $res = $mysqli->query($sql) or die($mysqli->error() . "<br>" . $sql);
    $n = 0;
    while ($row = $res->fetch_assoc()) {
        //$schedule[]=array("start"=>$row['startTime'],"end"=>$row['endTime']);

        $st = date("Y-m-d H:i", strtotime($date . " +" . $row['startTime'] . " minutes"));
        //TODO 
        //for afternight bookings
        //$row['endTime'] = ($row['startTime']<$row['endTime'])?$row['endTime']+720:$row['endTime'];
        $et = date("Y-m-d H:i", strtotime($date . " +" . $row['endTime'] . " minutes"));
        $a = $st;

        //layout counter
        $b = date("Y-m-d H:i", strtotime($a . " +" . $int . " minutes")); //default value for B is start time.
        $j = 0;
        for ($a = $st; $b <= $et; $b = date("Y-m-d H:i", strtotime($a . " +" . $int . " minutes"))) {
            //echo "a: ".$a." // "."b: ".$b."<br />";
            if (checkForEvents($a, $b, $idService)) {
                $events[date("Y-m-d", strtotime($a))][] = date("H:i", strtotime($a));
            }
            $qtyAdminReservation = checkForAdminReserv($a, $b, $idService); //print "<br>".$qtyAdminReservation."<br>";
            if ($qtyAdminReservation > 0) {
                $admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] = isset($admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]) ? $admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] += $qtyAdminReservation : $admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] += $qtyAdminReservation;
            }
            $qtyUserReservation = checkForUserReserv($a, $b, $idService);
            if ($qtyUserReservation !== false) {
                //$users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] = $qtyUserReservation;
                $users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] = isset($users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]) ? $users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] += $qtyUserReservation : $users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] += $qtyUserReservation;
            }
            $availabilityArr[date("Y-m-d", strtotime($a))][] = date("H:i", strtotime($a));
            $a = $b;
            $n++;
            $j++;
            if ($j > 1000) {
                $message = "error to match iterations 'function getScheduleService 
                         date=$date
                         idService=$idService'
                         st=$st";
                _error_log($message);
                break;
            }
        }
    }
    return array("availability" => $availabilityArr, "events" => $events, "admins" => $admins, "users" => $users, "countItems" => $n);
}

function getAvailableBookingsTable($date, $serviceID = 1, $time = null, $qty = 1, $couponCode = '')
{
    ####################################### PREPARE AVAILABILITY TABLE ##############################################
    $int = getInterval($serviceID); //interval in minutes.
    $serviceSettings = getServiceSettings($serviceID);
    $coupons = $serviceSettings['coupon'];
    $couponCode = urldecode($couponCode);
    $timeBefore = $serviceSettings['time_before'];
    $show_multiple_spaces = $serviceSettings['show_multiple_spaces']; //check option for multiple timeBooking
    $availebleSpaces = $show_multiple_spaces ? $serviceSettings['spaces_available'] : 1;
    $spot_price = $serviceSettings['spot_price'];
    $seconds = 0;
    $availability = "";

    ##########################################################################################################################
    # PREPARE AVAILABILITY ARRAY 

    $schedule = getScheduleService($serviceID, $date);
    $availabilityArr = $schedule['availability'];
    $events = $schedule['events'];
    $admins = $schedule['admins'];
    $users = $schedule['users'];
    $n = $schedule['countItems'];
    //print bw_dump($availabilityArr);
    //bw_dump($admins);
    //bw_dump($users);
    //bw_dump($events);
    //print $n;

    $availability .= "<div class='timeEvCont'><table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\"><tr><td valign='top' width='270' style='text-align:center;'>";

    $n = round($n / 2);
    $count = 0;
    //current timestamp
    $currTime = strtotime(date("Y-m-d H:i"));

    foreach ($availabilityArr as $k => $v) { //$v= date  (  2010-10-05 )
        //var_dump($availabilityArr);
        foreach ($v as $kk => $vv) { //$vv = time slot in above date 
            if ($time == null) {
                $time = array();
            }

            //timestamp on start time interval
            $spotTimeStart = strtotime(date("Y-m-d", strtotime($k)) . " $vv:00 -5 minutes"); //5-minutes befo select interval in past

            $spotTimeStart = $timeBefore > 0 ? strtotime(_date($date) . " $vv:00  -{$timeBefore} hours") : $spotTimeStart;

            if ($count == $n) {
                $availability .= "</td><td align='center' valign='top' width='270'>";
                $count = 0;
            }
            $availability .= "<div class='timeItem'>";
            //select intervat to past
            if (isset($events[$k]) && in_array($vv, $events[$k])) {
                $availability .= date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . " - " . TXT_EVENT . ".<br />";
            } elseif ($spotTimeStart <= $currTime) {
                $availability .= date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . " - " . TXT_PAST . ".<br />";
            } elseif ((isset($admins[$k]) && array_key_exists($vv, $admins[$k]))) {
                $spacesBookedUser = isset($users[$k][$vv]) ? $users[$k][$vv] : 0;
                $spacesBooked = $admins[$k][$vv];
                $spacesAllowed = $availebleSpaces - $spacesBooked - $spacesBookedUser;
                if ($spacesAllowed >= 1) {
                    $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                    $txt = $show_multiple_spaces ? "&nbsp;-&nbsp;<span class='spaces'>({$spacesAllowed} " . SPACES . ")</span>" : "";
                    $availability .= "<input type=\"checkbox\"" . (in_array($msm, $time) ? "checked" : "") . " value=\"" . $msm . "\" name=\"time[]\" rel=\"$spacesAllowed\"> - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "{$txt}<br />";
                } else {
                    $txt = $show_multiple_spaces ? '&nbsp;-&nbsp;<span class="spaces">(' . ZERO_SPACES2 . ')</span>' : "";
                    $availability .= "<input type='checkbox' disabled><span style='color:#ccc'> - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "{$txt}</span><br />";
                }
            } elseif ((isset($users[$k]) && array_key_exists($vv, $users[$k]))) {

                $spacesBooked = $users[$k][$vv];

                $spacesAllowed = $availebleSpaces - $spacesBooked;

                if ($spacesAllowed >= 1) {
                    $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                    $txt = $show_multiple_spaces ? "&nbsp;-&nbsp;<span class='spaces'>({$spacesAllowed} " . SPACES . ")</span>" : "";
                    $availability .= "<input type=\"checkbox\"" . (in_array($msm, $time) ? "checked" : "") . " value=\"" . $msm . "\" name=\"time[]\" rel=\"$availebleSpaces\"> - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "{$txt}<br />";
                } else {
                    $txt = $show_multiple_spaces ? '&nbsp;-&nbsp;<span class="spaces">(' . ZERO_SPACES2 . ')</span>' : "";
                    $availability .= "<input type='checkbox' disabled><span style='color:#ccc'> - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "{$txt}</span><br />";
                }
            } else {
                $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                $txt = $show_multiple_spaces ? "&nbsp;-&nbsp;<span class='spaces'>(" . $availebleSpaces . SPACES . ")</span>" : "";
                $availability .= "<input type=\"checkbox\"" . (in_array($msm, $time) ? "checked" : "") . " value=\"" . $msm . "\" name=\"time[]\" rel=\"$availebleSpaces\"> - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "{$txt}<br />";
            }
            $availability .= "</div>";
            $count++;
        }
    };
    $currencyPos = getOption('currency_position');
    $cuurency = getOption('currency');
    $availability .= "</td></tr></table><div class='qtyCont'>";
    $availability .= $show_multiple_spaces ? "<span>" . TXT_QTY . " <span class='spinner'><input type='text' name='qty' id='qty' value='$qty' style='width:40px'></span></span>" : "";
    $availability .= $spot_price ? "&nbsp;<span id='feeValue'>" . $cuurency . "&nbsp;</span>&nbsp;<del id='feeValueOld'></del>" : "";
    $availability .= "</div>";
    if ($coupons && $spot_price > 0) {
        $availability .= "<div id='coupon_conteiner'>";
        $availability .= "<label>" . TXT_COUPON_CODE . ":</label><input type='text' name='couponCode' id='couponCode' value='{$couponCode}' class='small'>&nbsp;<span id='discountDetails'></span>";
        $availability .= "</div>";
    }
    $availability .= "</div>";
    ##########################################################################################################################

    return $availability;
}

function checkQtyForTimeBooking($serviceID, $time, $date, $interval, $qty)
{
    //print "$serviceID<br>$date<br>$interval<br>$qty";
    global $mysqli;
    $availebleSpaces = getServiceSettings($serviceID, 'spaces_available');
    $error = false;

    if ($date < date("Y-m-d") || !is_array($time)) return true;

    $sumQty = 0;
    foreach ($time as $k => $v) {
        $qtyTmp = 0;
        $from = date("Y-m-d H:i:s", strtotime($date . " +" . $v . " minutes"));
        $to = date("Y-m-d H:i:s", strtotime($from . " +" . $interval . " minutes"));
        $adminQTY = checkForAdminReserv($from, $to, $serviceID);
        //print gettype($qtyTmp)."<br>";
        $sumQty = $adminQTY;

        $sSQL = "SELECT bri.* FROM `bs_reservations_items` bri
			INNER JOIN bs_reservations br on bri.reservationID = br.id
				WHERE br.serviceID='{$serviceID}' AND (
				(bri.reserveDateFrom < '{$to}' AND bri.reserveDateTo >= '{$to}') OR
				(bri.reserveDateTo > '{$from}' AND bri.reserveDateFrom <= '{$from}') OR
				(bri.reserveDateFrom <= '{$from}' AND bri.reserveDateTo >= '{$to}') OR
				(bri.reserveDateFrom >= '{$from}' AND bri.reserveDateTo <= '{$to}'))
				AND (br.status='1' OR br.status='4')  
				ORDER BY bri.reserveDateFrom ASC";

        $result = $mysqli->query($sSQL);
        if ($result->num_rows > 0) {
            if ($result->num_rows > 1) {

                while ($row = $result->fetch_assoc()) {
                    $qtyTmp += $row['qty'];
                }
                $sumQty += $qtyTmp + $qty;
                if ($sumQty > $availebleSpaces) {
                    $error = true;
                }
            } else {
                $qtyTmp = $result->fetch_assoc();
                $sumQty += $qtyTmp['qty'] + $qty;
                if ($sumQty > $availebleSpaces) {
                    $error = true;
                }
            }
        }
    }

    return $error;
}
function getManualBookingsByDate($dateFrom, $serviceID, $dateTo = null)
{
    global $mysqli;
    //print $from." - ".$to."<br>";
    $dateTo = empty($dateTo) ? $dateFrom : $dateTo;

    $bookings = array();
    //$date = date("Y-m-d", strtotime($from)); //print $date;
    $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=1 AND reserveDateTo>='{$dateTo}'"; //print $sSQL;
    $res = $mysqli->query($sSQL);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {

            $startDate = date("Y-m-d", strtotime($row['reserveDateFrom']));
            $endDate = date("Y-m-d", strtotime($row['reserveDateTo']));
            $startTime = date("H:i", strtotime($row['reserveDateFrom']));
            $endTime = date("H:i", strtotime($row['reserveDateTo']));
            $st = $startDate;
            $en = $endDate;
            $j = 0;
            for ($i = $st; $i <= $dateTo . " 23:59:59"; $i = date("Y-m-d", strtotime($i . " +{$row['repeate_interval']} {$row['repeate']}"))) {
                //print $i;
                /*$reserveDateFrom = $date . " " . $startTime;
                $reserveDateTo = $date . " " . $endTime;*/
                if ($dateFrom <= date("Y-m-d", strtotime($i)) || $dateTo > date("Y-m-d", strtotime($i))) {

                    $bookings[$row['id']] = $row;
                }

                //$i=$b;
                $j++;
                if ($j > 1000) {
                    $message = "error to match iterations 'function checkForAdminReserv 
                         from=$from
                         to=$to
                         serviceID=$serviceID'";
                    _error_log($message);
                    break;
                }
            }
        }
    }
    //bw_dump($recurring);
    //print $qtyTmp."-";
    $to = "$dateTo 23:59:00";
    $from = "$dateFrom 00:00:00";
    $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=0 AND(
				(reserveDateFrom < '{$to}' AND reserveDateTo >= '{$to}') OR
				(reserveDateTo > '{$from}' AND reserveDateFrom <= '{$from}') OR
				(reserveDateFrom <= '{$from}' AND reserveDateTo >= '{$to}') OR
				(reserveDateFrom >= '{$from}' AND reserveDateTo <= '{$to}'))";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $bookings[$row['id']] = $row;
        }
    } else {
        //return false;
    }

    return $bookings;
}

function getScheduleEventsTable($date, $serviceID = 1)
{
    global $mysqli;
    $availability = "";
    $availability .= "<table  border=\"0\" class=\"dataTable schedule\" align=\"left\" cellpadding=\"0\" cellspacing=\"0\">";
    $availability .= "<thead>
                            <tr class=\"topRow\">
                            <th>Event</th>
                            <th>Spots</th>
                            <th>Time From</th>
                            <th>Time To</th>
                            <th>Entry Fee</th>

                            </tr></thead>";

    $eventsList = getEventsByDate($date, $serviceID);
    //bw_dump($eventsList);
    $i = 0;
    foreach ($eventsList as $event) {
        $i = $i ? 0 : 1;
        $class = $i ? "odd" : "even";
        $_event = $event['event'];
        $timeFrom = getDateFormat($_event['eventDate']) . " " . _time($_event['eventDate']);
        $timeTo = getDateFormat($_event['eventDateEnd']) . " " . _time($_event['eventDateEnd']);
        $spaces = $event['qty'] . " out of " . $_event['spaces'];
        $fee = $_event['entryFee'] > 0 ? getCurrencyText($_event['entryFee']) : "Free";

        $hasBookings = $_event['spaces'] != $event['qty'] ? true : false;
        if ($hasBookings) {
            $spaces = "<a href='javascript:;' data-event=\"{$_event['id']}\" class=\"bookingsList\">{$spaces}</a>";
        }
        $availability .= "
        <tr class=\"{$class}\">
            <td><a href=\"bs-events-add.php?id={$_event['id']}\">{$_event['title']}</a></td>
            <td><span class=\"space\">{$spaces}</span></td>
            <td>{$timeFrom}</td>
            <td>{$timeTo}</td>
            <td>{$fee}</td>


        </tr>
        ";
        if ($hasBookings) {
            //$availability.="<tr><td>&nbsp;</td><td colspan=\"4\">";
            /*$availability.="<table  border=\"0\" class=\"dataTable bookings\" align=\"left\" cellpadding=\"0\" cellspacing=\"0\">
                            <thead>
                            <tr class=\"topRow\">
                                <th>Spots</th>
                                <th>Customer Name</th>
                                <th>Customer Phone</th>
                                <th>Customer Email</th>
                            </tr>
                            </thead>";*/
            $availability .= "
                            <tr class=\"topRow header\" data-row=\"{$_event['id']}\">
                                <td>&nbsp;</td>
                                <td>Spots</td>
                                <td>Customer Name</td>
                                <td>Customer Phone</td>
                                <td>Customer Email</td>
                            </tr>
                            ";
            $sql = "SELECT * FROM bs_reservations WHERE eventID='{$_event['id']}' AND status IN ('1','4')";
            $res = $mysqli->query($sql);
            $j = 0;
            $_class = '';
            while ($row = $res->fetch_assoc()) {
                //bw_dump($row);
                if ($j == 0) {
                    $j = 1;
                    $_class = "odd";
                } else {
                    $j = 0;
                    $_class = "even";
                }
                $availability .= "<tr class=\"{$_class} bookings\" data-row=\"{$_event['id']}\">
                                <td>&nbsp;</td>
                                <td>{$row['qty']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['phone']}</td>
                                <td><a href=\"bs-bookings_event-edit.php?id={$row['id']}\">{$row['email']}</a></td>
                                </tr>";
            }
            //$availability.="</table></td></tr>";
        }
    }
    $availability .= "</table>";
    return $availability;
}
function getScheduleTable($date, $serviceID = 1)
{
    global $baseDir;
    ####################################### PREPARE AVAILABILITY TABLE ##############################################
    global $mysqli;

    $adminReserveData = "";
    $seconds = 0;
    $availability = "";
    $availability .= "<table  border=\"0\" class=\"dataTable schedule\" align=\"left\" cellpadding=\"0\" cellspacing=\"0\">";
    $availability .= "<thead><tr class=\"topRow\"><th>Time</th><th>Spots Left</th><th>Customer Name</th><th>Customer Phone</th><th  class=\"noBorderRight\">Customer Email</th><th></th>&nbsp;</tr></thead>";



    $manualBookings = getManualBookingsByDate($date, $serviceID);

    $reservedArray = array();
    $reservationData = array();
    $int = getInterval($serviceID); //interval in minutes.

    $availebleSpaces = getServiceSettings($serviceID, 'spaces_available');
    $show_multiple_spaces = getServiceSettings($serviceID, 'show_multiple_spaces');
    //bw_dump($manualBookings);
    $J = 1;
    //ACTUAL CUSTOMER BOOKINGS

    $query = "SELECT bs_reservations_items.*,bs_reservations.email,bs_reservations.name,bs_reservations.phone,bs_reservations.id as rid FROM `bs_reservations_items`
	INNER JOIN bs_reservations on bs_reservations_items.reservationID = bs_reservations.id
	WHERE (bs_reservations.status='1' OR bs_reservations.status='4') AND
	bs_reservations_items.reserveDateFrom LIKE '" . $date . "%' AND
	bs_reservations.serviceID={$serviceID} ORDER BY bs_reservations_items.reserveDateFrom ASC ";
    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {

        while ($rr = $result->fetch_assoc()) {

            $tFrom = date("H:i", strtotime($rr["reserveDateFrom"]));
            $dFrom = date("Y-m-d", strtotime($rr["reserveDateFrom"]));
            if (isset($reservedArray[$dFrom][$tFrom])) {
                $reservedArray[$dFrom][$tFrom] = $rr["qty"] + $reservedArray[$dFrom][$tFrom];
            } else {
                $reservedArray[$dFrom][$tFrom] = $rr["qty"];
            }
            //$reservationInfo = "<div><a href='bs-bookings-edit.php?id=" . $rr["rid"] . "'>" . $rr["name"] . "&nbsp; (phone:" . $rr["phone"] . "; qty=" . $rr['qty'] . ")</a></div>";
            $classRow = $classRow == 'even1' ? "odd1" : "even1";
            $reservationInfo = "<tr class='{$classRow} rr_{$tFrom}'><td></td><td>{$rr['qty']}</td><td>{$rr["name"]}</td><td>{$rr["phone"]}</td><td><a 'bs-bookings-edit.php?id=" . $rr["rid"] . "'>{$rr['email']}</a></td><td></td></tr>";

            $reservationInfoArray = array("qty" => $rr['qty'], 'name' => $rr['name'], 'phone' => $rr['phone'], 'email' => $rr['email'], "rid" => $rr['rid']);

            if (isset($reservationData[$dFrom][$tFrom])) {
                $reservationData[$dFrom][$tFrom] = $reservationData[$dFrom][$tFrom] . $reservationInfo;
            } else {
                $reservationData[$dFrom][$tFrom] = $reservationInfo;
            }
            $reservationData1[$dFrom][$tFrom][] = $reservationInfoArray;
        }
    }
    //bw_dump($reservationData1);
    //bw_dump($reservedArray);
    ##########################################################################################################################
    ##########################################################################################################################
    # PREPARE AVAILABILITY ARRAY
    $schedule = getScheduleService($serviceID, $date);
    $availabilityArr = $schedule['availability'];
    $events = $schedule['events'];
    $n = $schedule['countItems'];
    $admins = $schedule['admins'];
    $users = $schedule['users'];


    //bw_dump($events);
    //$ww= date("w",strtotime($date));
    //$tt = getStartEndTime($ww,$serviceID);
    if (!count($availabilityArr)) {
        //$availability .= ADM_NONWORKING;
    } else {

        $n = ($n - ($n % 2)) / 2;
        $count = 0;
        $i = 0;

        foreach ($availabilityArr as $k => $v) { //$v= date  (  2010-10-05 )
            foreach ($v as $kk => $vv) { //$vv = time slot in above date
                $i = $i ? 0 : 1;
                $class = $i ? "odd" : "even";

                $time = _time($vv) . "-" . _time(date("Y-m-d H:i:m", strtotime($vv . " +" . $int . " minutes")));
                $bookLink = "<a class='greedButton' href='bs-reserve.php?serviceID={$serviceID}&reserveDateFrom={$date}&reserveDateTo={$date}&1_from_h=" . date("H", strtotime($vv)) . "&1_from_m=" . date("i", strtotime($vv)) . "&2_from_h=" . date("H", strtotime($vv . " +" . $int . " minutes")) . "&2_from_m=" . date("i", strtotime($vv . " +" . $int . " minutes")) . "' >Book</a>";
                if (isset($events[$k]) && in_array($vv, $events[$k])) {
                    $availability .= "<tr class=\"$class\"><td><span class=\"time\">" . $time . "</span></td><td colspan=\"4\" class=\"noBorderRight\">Event<td></tr>";
                } elseif (isset($admins[$k]) && array_key_exists($vv, $admins[$k])) {
                    $classRow = "odd1";

                    $adminData = getAdminReserveData("$k $vv", date("Y-m-d H:i", strtotime("$k $vv +$int minutes")), $serviceID);

                    $spacesBookedUser = isset($users[$k][$vv]) ? $users[$k][$vv] : 0;
                    $spacesBooked = $admins[$k][$vv];
                    $adminReserveData = "";
                    foreach ($adminData as $key => $data) {
                        $classRow = $classRow == 'even1' ? "odd1" : "even1";
                        $viewLink = "<a href=\"bs-reserve.php?id={$key}\" class=\"greedButton grey\">Edit</a>";
                        //$adminReserveData .= "<br><a href='bs-reserve.php?id={$key}'>Manual Reservation<br/> (Reason: {$data['reason']}; Quantity: {$data['qty']})</a>";
                        $adminReserveData .= "<tr class='{$classRow} rr_{$J} hide'><td></td><td>{$data['qty']}</td><td>Manual Booking <img src='images/info_small.png' border=\"0\" class=\" tipTip imgCenter\"  title=\"{$data['reason']} \"/></td><td></td><td  class=\"noBorderRight\"></td><td>{$viewLink}</td></tr>";
                    }
                    $spacesAllowed = $availebleSpaces - $spacesBooked - $spacesBookedUser;
                    $userBookings = "";
                    if (($availebleSpaces - $spacesBooked) > 0) {

                        if (isset($reservationData1[$k][$vv])) {
                            foreach ($reservationData1[$k][$vv] as $rr) {
                                $classRow = $classRow == 'even1' ? "odd1" : "even1";
                                $viewLink = "<a href=\"bs-bookings-edit.php?id=" . $rr["rid"] . "\" class=\"greedButton grey\">Edit</a>";
                                $userBookings .= "<tr class='{$classRow} rr_{$J} hide'><td></td><td>{$rr['qty']}</td><td>{$rr["name"]}</td><td>{$rr["phone"]}</td><td class=\"noBorderRight\"><a 'bs-bookings-edit.php?id=" . $rr["rid"] . "'>{$rr['email']}</a></td><td>{$viewLink}</td></tr>";
                            }
                        }
                    } else {
                        $spacesAllowed = 0;
                    }
                    if ($show_multiple_spaces) {
                        $availability .= "<tr  class=\"$class\"><td><span class=\"time\">" . $time . "</span></td><td><span class='space'>{$spacesAllowed} out of {$availebleSpaces}</span></td><td><a href='javascript:;' onclick='collapseRows(this,\"rr_{$J}\")'>Expand to view all</a></td><td>&nbsp;</td><td class=\"noBorderRight\">&nbsp;</td><td>{$bookLink}</td></tr>";
                    } else {
                        $availability .= "<tr  class=\"$class\"><td><span class=\"time\">" . $time . "</span></td><td><span class='space'>0 out of 1</span></td><td><a href='javascript:;' onclick='collapseRows(this,\"rr_{$J}\")'>Expand to view all</a></td><td>&nbsp;</td><td class=\"noBorderRight\">&nbsp;</td><td>{$bookLink}</td></tr>";
                    }

                    $availability .= $adminReserveData . $userBookings;
                } elseif (isset($users[$k][$vv])/* || (isset($users[$k]) && array_key_exists($vv, $users[$k]))*/) {
                    $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                    //$availebleSpaces;
                    $spacesBooked = $users[$k][$vv];
                    $spacesAllowed = $availebleSpaces - $spacesBooked;
                    $userBookings = '';
                    if ($show_multiple_spaces) {

                        if (isset($reservationData1[$k][$vv])) {

                            foreach ($reservationData1[$k][$vv] as $rr) {
                                $classRow = $classRow == 'even1' ? "odd1" : "even1";
                                $viewLink = "<a href=\"bs-bookings-edit.php?id=" . $rr["rid"] . "\" class=\"greedButton grey\">Edit</a>";
                                $userBookings .= "<tr class='{$classRow} rr_{$J} hide'><td></td><td>{$rr['qty']}</td><td>{$rr["name"]}</td><td>{$rr["phone"]}</td><td class=\"noBorderRight\"><a 'bs-bookings-edit.php?id=" . $rr["rid"] . "'>{$rr['email']}</a></td><td>{$viewLink}</td></tr>";
                            }
                        }
                        $bookLink = $spacesAllowed > 0 ? $bookLink : "";
                        $availability .= "<tr  class=\"$class\"><td><span class=\"time\">" . $time . "</span></td><td><span class='space'>{$spacesAllowed} out of {$availebleSpaces}</span></td><td><a href='javascript:;' onclick='collapseRows(this,\"rr_{$J}\")'>Expand to view all</a></td><td>&nbsp;</td><td class=\"noBorderRight\">&nbsp;</td><td>{$bookLink}</td></tr>";
                        $availability .= $userBookings;
                        //$availability .="<tr class='schedule_av  class=\"$class\"".($spacesAllowed==0?"empty":"")."'><td width='100' valign='top' class='time'><div>" . $time . "</div></td><td valign='top'><span class='space'>{$spacesAllowed}</span>".($spacesAllowed?$bookLink:"") .SPC_LEFT . $reservationData[$k][$vv] . "</td></tr>";
                    } else {
                        $rr = $reservationData1[$k][$vv][0];

                        $viewLink = "<a href=\"bs-bookings-edit.php?id=" . $rr["rid"] . "\" class=\"greedButton grey\">Edit</a>";
                        $availability .= "<tr  class=\"$class\"><td><span class=\"time\">" . $time . "</span></td><td><span class='space'>0 out of 1</span></td><td>{$rr["name"]}</td><td>{$rr["phone"]}</td><td class=\"noBorderRight\"><a 'bs-bookings-edit.php?id=" . $rr["rid"] . "'>{$rr['email']}</a></td><td>{$viewLink}</td></tr>";
                    }
                } else {
                    $availebleSpaces = $show_multiple_spaces ? $availebleSpaces : 1;
                    //$availability .= "<tr class='schedule_av'><td width='100' valign='top' class='time'><div>" . $time . "</div></td><td valign='top'><span class='space'>{$availebleSpaces}</span>". SPC_LEFT. $reservationData[$k][$vv] . "{$bookLink}</td></tr>";
                    $availability .= "<tr  class=\"$class\"><td><span class=\"time\">" . $time . "</span></td><td><span class='space'>{$availebleSpaces} out of {$availebleSpaces}</span></td><td>N/A</td><td>N/A</td><td class=\"noBorderRight\">N/A</td><td>{$bookLink}</td></tr>";
                }


                $count++;
                $J++;
            }
        }
    }

    $availability .= "</table>";
    ##########################################################################################################################

    return $availability;
}
function _getScheduleTable($date, $serviceID = 1)
{
    global $mysqli;
    global $baseDir;
    ####################################### PREPARE AVAILABILITY TABLE ##############################################
    $int = getInterval($serviceID); //interval in minutes.
    $reservedArray = array();
    $reservationData = array();
    $adminReserveData = "";
    $seconds = 0;
    $availability = "";
    $availebleSpaces = getServiceSettings($serviceID, 'spaces_available');
    $show_multiple_spaces = getServiceSettings($serviceID, 'show_multiple_spaces');


    $manualBookings = getManualBookingsByDate($date, $serviceID);
    //bw_dump($manualBookings);

    //ACTUAL CUSTOMER BOOKINGS

    $query = "SELECT bs_reservations_items.*,bs_reservations.name,bs_reservations.phone,bs_reservations.id as rid FROM `bs_reservations_items` 
	INNER JOIN bs_reservations on bs_reservations_items.reservationID = bs_reservations.id 
	WHERE (bs_reservations.status='1' OR bs_reservations.status='4') AND 
	bs_reservations_items.reserveDateFrom LIKE '" . $date . "%' AND 
	bs_reservations.serviceID={$serviceID} ORDER BY bs_reservations_items.reserveDateFrom ASC ";
    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {
        while ($rr = $result->fetch_assoc()) {
            if (isset($reservedArray[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))])) {
                $reservedArray[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))] = $rr["qty"] + $reservedArray[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))];
            } else {
                $reservedArray[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))] = $rr["qty"];
            }
            $reservationInfo = "<div><a href='bs-bookings-edit.php?id=" . $rr["rid"] . "'>" . $rr["name"] . "&nbsp; (phone:" . $rr["phone"] . "; qty=" . $rr['qty'] . ")</a></div>";
            if (isset($reservationData[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))])) {
                $reservationData[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))] =
                    $reservationData[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))] . $reservationInfo;
            } else {
                $reservationData[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][date("H:i", strtotime($rr["reserveDateFrom"]))] = $reservationInfo;
            }
        }
    }
    //bw_dump($reservationData);
    //bw_dump($reservedArray);
    ##########################################################################################################################
    ##########################################################################################################################
    # PREPARE AVAILABILITY ARRAY 
    $schedule = getScheduleService($serviceID, $date);
    $availabilityArr = $schedule['availability'];
    $events = $schedule['events'];
    $n = $schedule['countItems'];
    $admins = $schedule['admins'];
    $users = $schedule['users'];


    if (!count($availabilityArr)) {
        $availability .= ADM_NONWORKING;
    } else {
        $availability .= "<table width=\"500\" border=\"0\" align=\"left\" cellpadding=\"0\" cellspacing=\"0\">";
        $n = ($n - ($n % 2)) / 2;
        $count = 0;

        $availability .= "<tr><td valign='top'>";
        foreach ($availabilityArr as $k => $v) { //$v= date  (  2010-10-05 )
            foreach ($v as $kk => $vv) { //$vv = time slot in above date 
                if ($count == $n) {
                    $availability .= "</td><td align='left' valign='top'>";
                    $count = 0;
                }
                $bookLink = "<a class='book' href='bs-reserve.php?serviceID={$serviceID}&reserveDateFrom={$date}&reserveDateTo={$date}&1_from_h=" . date("H", strtotime($vv)) . "&1_from_m=" . date("i", strtotime($vv)) . "&2_from_h=" . date("H", strtotime($vv . " +" . $int . " minutes")) . "&2_from_m=" . date("i", strtotime($vv . " +" . $int . " minutes")) . "' ></a>";
                if (isset($events[$k]) && in_array($vv, $events[$k])) {
                    $availability .= "<tr class='schedule_na'><td width='100' valign='top' class='time'><div>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "</div></td><td valign='top'>" . TXT_EVENT2 . "</td></tr>";
                } elseif (isset($admins[$k]) && array_key_exists($vv, $admins[$k])) {
                    $adminData = getAdminReserveData("$k $vv", date("Y-m-d H:m", strtotime("$k $vv +$int minutes")), $serviceID);
                    $spacesBookedUser = isset($users[$k][$vv]) ? $users[$k][$vv] : 0;
                    $spacesBooked = $admins[$k][$vv];
                    $adminReserveData = "";
                    foreach ($adminData as $key => $data) {
                        $adminReserveData .= "<br><a href='bs-reserve.php?id={$key}'>Manual Reservation<br/> (Reason: {$data['reason']}; Quantity: {$data['qty']})</a>";
                    }
                    $spacesAllowed = $availebleSpaces - $spacesBooked - $spacesBookedUser;
                    if ($spacesAllowed >= 1) {
                        $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                        $spacesAllowed = $show_multiple_spaces ? $spacesAllowed : 1;
                        $availability .= "<tr class='schedule_av'><td width='100' valign='top' class='time'><div>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "</div></td><td valign='top'><span class='space'>{$spacesAllowed}</span> {$bookLink}" . SPC_LEFT . $adminReserveData . (isset($reservationData[$k][$vv]) ? $reservationData[$k][$vv] : "") . "</td></tr>";
                    } else {

                        $availability .= "<tr class='schedule_av empty'><td width='100' valign='top' class='time'><div>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "</div></td><td valign='top'><span class='space'>{$spacesAllowed}</span>" . SPC_LEFT . $adminReserveData . (isset($reservationData[$k][$vv]) ? $reservationData[$k][$vv] : "") . "</td></tr>";
                    }
                } elseif (isset($users[$k]) || (isset($users[$k]) && !array_key_exists($vv, $users[$k]))) {
                    $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                    //$availebleSpaces;
                    $spacesBooked = $users[$k][$vv];
                    $spacesAllowed = $availebleSpaces - $spacesBooked;
                    $availability .= "<tr class='schedule_av " . ($spacesAllowed == 0 ? "empty" : "") . "'><td width='100' valign='top' class='time'><div>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "</div></td><td valign='top'><span class='space'>{$spacesAllowed}</span>" . ($spacesAllowed ? $bookLink : "") . SPC_LEFT . $reservationData[$k][$vv] . "</td></tr>";
                } else {
                    $availebleSpaces = $show_multiple_spaces ? $availebleSpaces : 1;
                    $availability .= "<tr class='schedule_av'><td width='100' valign='top' class='time'><div>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv)) . " - " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($vv . " +" . $int . " minutes")) . "</div></td><td valign='top'><span class='space'>{$availebleSpaces}</span>" . SPC_LEFT . $reservationData[$k][$vv] . "{$bookLink}</td></tr>";
                }


                $count++;
            }
        }
        if (count($manualBookings)) {
            $availability .= "<tr><td colspan=2><h3>Manual Bookings For Current Day</h3></td></tr>";
            foreach ($manualBookings as $mbook) {
                $availability .= "<tr><td colspan=2><div class='mBooking'><a class=\"naw\" href=\"javascript:;\">▾</a>";
                $availability .= "Manual Booking: <a href='bs-reserve.php?id={$mbook['id']}'>{$mbook['reason']}</a> " . ($mbook['recurring'] ? "( <b>recurring</b> )" : "") . "<br>";
                $availability .= "<div class='info'><label>From</label>: <b>" . getDateFormat($mbook['reserveDateFrom']) . "</b> " . _time($mbook['reserveDateFrom']) . "<br>";
                $availability .= "<label>To</label>: <b>" . getDateFormat($mbook['reserveDateTo']) . "</b> " . _time($mbook['reserveDateTo']) . "<br>";
                if ($mbook['recurring']) {
                    $availability .= "<label>Interval</label>: every {$mbook['repeate_interval']} <b>{$mbook['repeate']}</b> <br>";
                }
                $availability .= "<label>Qty</label>: {$mbook['qty']}<br>";
                $availability .= "</div></div></td></tr>";
            }
            $availability .= "<tr><td colspan=2>&nbsp;</td></tr>";
        }
        $availability .= "</td></tr></table>";
    }
    ##########################################################################################################################

    return $availability;
}

function getService($id, $field = null)
{
    global $mysqli;
    $sql = "SELECT * FROM bs_services WHERE id='{$id}'";
    $res = $mysqli->query($sql);
    if ($field == null) {
        return $res->fetch_assoc();
    } else {
        $row = $res->fetch_assoc();
        return $row[$field];
    }
}

function getServiceSettings($id, $field = null)
{
    global $mysqli;
    $serviceType = getService($id, 'type');
    if ($serviceType == 't') {
        $sql = "SELECT * FROM bs_service_settings bss
                INNER JOIN bs_services bs ON bss.serviceId  = bs.id
                WHERE bss.serviceID='{$id}'";
    } else {
        $sql = "SELECT * FROM  bs_service_days_settings bsds
                INNER JOIN bs_services bs ON bsds.idService  = bs.id
                WHERE bsds.idService='{$id}'";
    }
    $res = $mysqli->query($sql);
    $row = $res->fetch_assoc();
    $row['type'] = $serviceType;
    if ($field == null) {
        return $row;
    } else {

        return $row[$field];
    }
}

function getBookingText($serviceID = 1)
{
    $tt = array();
    $maximumBookings = getMaxBooking($serviceID);
    $inter = getInterval($serviceID);
    $intervalConverted = $inter * $maximumBookings;
    //interval 15*X / 30*X / 45*X / 60*X /
    // example with 2 maximum bookings - 30 / 60 / 90 / 120
    if ($intervalConverted < 60) {
        //minutes
        if ($maximumBookings != 0 && $maximumBookings != 99) {
            $tt[0] = $intervalConverted . TXT_MINUTES_MAX;
        } else {
            $tt[0] = "";
        }
    } else {
        //hours
        $fullHours = ($intervalConverted - $intervalConverted % 60) / 60;
        $fullMinutes = $intervalConverted - ($fullHours * 60);
        if ($maximumBookings != 0 && $maximumBookings != 99) {
            $tt[0] = $fullHours . TXT_HOURSS . ($fullMinutes > 0 ? TXT_AND . $fullMinutes . TXT_MINUTES : "") . TXT_MAX;
        } else {
            $tt[0] = "";
        }
    }


    $minimumBookings = getMinBooking($serviceID);
    $intervalConverted = $inter * $minimumBookings;
    if ($intervalConverted < 60) {
        //minutes
        if ($minimumBookings != 0 && $minimumBookings != 99) {
            $tt[1] = $intervalConverted . TXT_MINUTES_MIN;
        } else {
            $tt[1] = "";
        }
    } else {
        //hours
        $fullHours = ($intervalConverted - $intervalConverted % 60) / 60;
        $fullMinutes = $intervalConverted - ($fullHours * 60);
        if ($minimumBookings != 0 && $minimumBookings != 99) {
            $tt[1] = $fullHours . TXT_HOURSS . ($fullMinutes > 0 ? TXT_AND . $fullMinutes . TXT_MINUTES : "") . TXT_MIN;
        } else {
            $tt[1] = "";
        }
    }
    return $tt;
}

function getBooking($id, $field = null)
{
    global $mysqli;
    $q = "SELECT bs_reservations.*,bs_services.name as sname FROM `bs_reservations` 
		INNER JOIN bs_services ON bs_services.id=bs_reservations.serviceID
		WHERE bs_reservations.id='{$id}'";
    $res = $mysqli->query($q);
    if ($res->num_rows > 0) {
        $rr = $res->fetch_assoc();
        if (empty($field)) {
            return $rr;
        } else {
            return $rr[$field];
        }
    }
    return false;
}
function getEventRecurringSpots($eventID, $spacesAvl)
{
    global $mysqli;
    $text = "";
    $sql = "SELECT * FROM bs_reservations WHERE eventID={$eventID} ORDER BY date DESC ";
    $result = $mysqli->query($sql) or die("error getting events from db");
    $spaces = array();
    while ($row = $result->fetch_assoc()) {
        isset($spaces[$row['date']]) ? $spaces[$row['date']] = $spaces[$row['date']] + $row['qty'] : $spaces[$row['date']] = $row['qty'];
    }
    foreach ($spaces as $k => $v) {
        $text .= getDateFormat($k) . ": <b>" . ($spacesAvl - $v) . "</b> " . SYL_LEFT . " <b>" . $spacesAvl . "</b> " . SYL_TOTAL . "<br/>";
    }
    return $text;
}
function getPricePerSpot($serviceID = 1)
{
    global $mysqli;
    $q = "SELECT * FROM bs_service_settings WHERE serviceId ='{$serviceID}'";
    $res = $mysqli->query($q);
    $rr = $res->fetch_assoc();
    return $rr["spot_price"];
}

function getMaxQtyEvent($id)
{
    global $mysqli;
    $q = "SELECT max_qty FROM bs_events WHERE id='" . $id . "'";
    $res = $mysqli->query($q);
    $rr = $res->fetch_assoc();
    return $rr["max_qty"];
}

function uploadFile($inputFile, $sFolderPictures)
{
    $image_path = $inputFile['tmp_name'];
    $photoFileNametmp = $inputFile['name'];
    $fileNamePartstmp = explode(".", $photoFileNametmp);
    $fileExtensiontmp = strtolower(end($fileNamePartstmp)); // part behind last dot
    $allowedExtentions = array("jpeg", "jpg", "png", "gif");
    $allowedMime = array("image/jpeg", "image/jpg", "image/png", "image/gif");
    $fileInfo = getimagesize($image_path);
    $err = false;

    if ($inputFile['size'] > 20971520) {
        $ssize = sprintf("%01.2f", $inputFile['size'] / 1048576);
        $err = "Your file is " . $ssize . ". Max file size is 20 MB.<br>";
    }
    if (!in_array(strtolower($fileExtensiontmp), $allowedExtentions)) {
        $err .= "Picture's extension should be ." . join(" ,.", $allowedExtentions) . "<br />";
    } elseif (!in_array($fileInfo['mime'], $allowedMime)) {
        $err .= "Picture's type should be ." . join(" ,.", $allowedMime) . "<br />";
    }

    if (empty($err)) {
        // $newFile=$_SERVER['DOCUMENT_ROOT'].$sFolderPictures;//print $newFile;
        $newFile = $sFolderPictures; //print $newFile;
        $ret = move_uploaded_file($inputFile['tmp_name'], $newFile);
        if (!$ret) {
            $err .= "Upload failed. No file received. Check your installation directory in dbconnect.php";
        } else {
            $imgPath = $sFolderPictures;
        }
    }
    if (file_exists($inputFile['tmp_name'])) {
        @unlink($inputFile['tmp_name']);
    }
    return array("error" => $err, 'imgPath' => $imgPath);
}


function getEventList($eventID = null, $qty = null, $date, $couponCode = '')
{
    $availability = ""; //print "dd".$qty;
    global $mysqli;

    $query = "SELECT * FROM bs_events WHERE id={$eventID} ORDER BY eventDate ASC ";

    $currencyPos = getOption('currency_position');
    $currency = getOption('currency');


    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {

        $availability .= "<div class='eventWrapper'>";
        //we have events for this day!
        $event_num = $result->num_rows;
        //we need to check if at least one event has spaces. if yes then { $bgClass="cal_reg_on";  } else { $bgClass="cal_reg_off"; }
        $event_available = false;
        $event_count = 0;
        $text = "";
        $curr = getAdminPaypal();
        $startEnd = getEventStartEndDate($eventID, $date, 'array');


        while ($row = $result->fetch_assoc()) {
            $coupons = $row['coupon'];
            $spaces_left = $row["recurring"] == 1 ? getSpotsLeftForEvent($row["id"], $date) : getSpotsLeftForEvent($row["id"]);
            $availability .= "<div class='eventContainer'>";

            $availability .= "<div class='eventCheckbox'>";
            if ($spaces_left > 0) {

                $availability .= "<input type='hidden' name='eventID' value='" . $row["id"] . "' >";
            } else {
                $availability .= "&nbsp;";
            }
            $availability .= "</div>";
            $availability .= "<div class='eventTitle'><h1 itemprop=\"name\">" . $row["title"] . "</h1></div>";
            $availability .= "<table class='evntCont' width='100%'><tr><td width='80%' valign='center'><div class='eventDescr'>";

            $availability .= TXT_EVENT_START . " <span>" . getDateFormat($startEnd['from']) . "&nbsp;&nbsp;" . date((getTimeMode()) ? " g:i a" : " H:i", strtotime($startEnd['from'])) . "</span><br>
									" . TXT_EVENT_ENDS . " <span>" . getDateFormat($startEnd['to'])  . "&nbsp;&nbsp;" . date((getTimeMode()) ? " g:i a" : "H:i", strtotime($startEnd['to'])) . "</span>
					<br />";

            if (!empty($row['location'])) {
                if (!empty($row['map_link'])) {
                    $availability .= LOCATION . "<a href='{$row['map_link']}' target='_blank'>{$row['location']}</a><br/>";
                } else {
                    $availability .= LOCATION . "{$row['location']}<br/>";
                }
            }
            if ($row["path"] != "") {
                $availability .= "<div class='eventImage'><img src='" . $row["path"] . "' alt='" . $row["title"] . "' /></div>";
            }
            $availability .= "<p itemprop=\"description\">" . nl2br($row["description"]) . "</p>";

            $availability .= "</div><td>";
            $availability .= "<td class='brd_l'><div class='spots'><span class='spot'>" . $spaces_left . "</span><span class='spot1'>" . TXT_SPOTS_LEFT . "</span></div></td>";
            if ($row["allow_multiple"] == "1") {
                $qty_max = (getMaxQtyEvent($row["id"]) > $spaces_left) ? $spaces_left : getMaxQtyEvent($row["id"]);
                $availability .= "<td class='brd_l'><div class='tickets'>
                    <select name='qty_" . $row["id"] . "' id='qty'>";
                $availability .= "<option value='1'>" . TXT_FUNC_QTY . "</option>";
                for ($i = 1; $i <= $qty_max; $i++) {
                    $availability .= "<option value='" . $i . "' " . (!empty($qty) && $i == $qty && $row["id"] == $eventID ? "selected='selected'" : "") . ">" . $i . "</option>";
                }
                $availability .= "</select></div></td>";
            }
            if ($row["entryFee"] > 0) {

                $price = $row["entryFee"];
                if (getOption('enable_tax')) {
                    $price = $price + ($price * getOption('tax') / 100);
                }
                $availability .= "<td class='brd_l'><div class='fee'><b> " . ($currencyPos == 'b' ? $currency : "") . " <span  id='price'>" . number_format($price, 2) . "</span> " . ($currencyPos == 'a' ? $currency : "") . "<del id='feeValueOld'></del></div></td>";
            } else {
                $availability .= "<td class='brd_l'><div class='fee'><span style='color:#0FA1D2'>" . TXT_FUNC_FREE . "</span></div></td>";
            }
            $availability .= "</tr>";
            if ($coupons && $row["entryFee"] > 0) {
                $availability .= "<tr><td colspan='5' align='center'><label>" . TXT_COUPON_CODE . ":</label><input type='text' name='couponCode' id='couponCode' value='{$couponCode}' class='small'>&nbsp;<span id='discountDetails'></span></td></tr>";
            }
            $availability .= "</table>";
            $availability .= "<br clear='all'><div class='social'>" . getSocial($row["id"]) . "</div>";
            $availability .= "</div>";
        }
        if ($event_count == 1) { } else if ($event_count > 1) {
            $text = "<p>" . TXT_PLSSELECT . "</p>";
        } else {
            $text = "";
        }

        $availability .= "</div>";
    }

    return $availability;
}

function getEventsList($date, $serviceID = 1, $eventID = null, $selEvent = null, $qty = null)
{
    $availability = ""; //print "dd".$qty;
    global $mysqli;
    if (!empty($eventID)) {
        $query = "SELECT * FROM bs_events WHERE id={$eventID} ORDER BY eventDate ASC ";
    } else {
        $query = "SELECT * FROM bs_events WHERE eventDate LIKE '%" . $date . "%' AND serviceID={$serviceID} ORDER BY eventDate ASC ";
    }

    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {
        $availability .= "<div class='eventWrapper'>";
        //we have events for this day!
        $event_num = $result->num_rows;
        //we need to check if at least one event has spaces. if yes then { $bgClass="cal_reg_on";  } else { $bgClass="cal_reg_off"; }
        $event_available = false;
        $event_count = 0;
        $text = "";
        $curr = getAdminPaypal();
        while ($row = $result->fetch_assoc()) {
            $spaces_left = $row["recurring"] == 1 ? getSpotsLeftForEvent($row["id"], $date) : getSpotsLeftForEvent($row["id"]);
            $availability .= "<div class='eventContainer'>";

            $availability .= "<div class='eventCheckbox'>";
            if ($spaces_left > 0) {
                if (!empty($selEvent)) {
                    $availability .= "<input type='radio' name='eventID' value='" . $row["id"] . "' " . ($selEvent == $row['id'] ? "checked" : "") . ">";
                } else {
                    $availability .= "<input type='radio' name='eventID' value='" . $row["id"] . "' checked>";
                }
            } else {
                $availability .= "&nbsp;";
            }
            $availability .= "</div>";
            $availability .= "<div class='eventTitle'><b>" . $row["title"] . "</b></div>";
            $availability .= "<table class='evntCont' width='100%'><tr><td width='80%' valign='center'><div class='eventDescr'>";
            if ($row["path"] != "") {
                $availability .= "<div class='eventImage'><img src='." . $row["path"] . "' alt='" . $row["title"] . "' /></div>";
            }
            $availability .= "Event starts at <span>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($row["eventTime"])) . "</span><br />" . nl2br($row["description"]) . "</div><td>";
            $availability .= "<td class='brd_l'><div class='spots'><span class='spot'>" . $spaces_left . "</span><span class='spot1'>" . TXT_SPOTS_LEFT . "</span></div></td>";
            if ($row["allow_multiple"] == "1") {
                $qty_max = (getMaxQtyEvent($row["id"]) > $spaces_left) ? $spaces_left : getMaxQtyEvent($row["id"]);
                $availability .= "<td class='brd_l'><div class='tickets'><select name='qty_" . $row["id"] . "'  id='qty'>";
                $availability .= "<option value='1'>" . TXT_FUNC_QTY . "</option>";
                for ($i = 1; $i <= $qty_max; $i++) {
                    $availability .= "<option value='" . $i . "' " . (!empty($qty) && $i == $qty && $row["id"] == $selEvent ? "selected='selected'" : "") . ">" . $i . "</option>";
                }
                $availability .= "</select></div></td>";
            }
            if ($row["payment_required"] == "1") {
                $price = $row["entryFee"];
                /*if (getOption('enable_tax')) {
                    $price = $price + ($price * getOption('tax') / 100);
                }*/
                $availability .= "<td class='brd_l'><div class='fee'><b> " . getOption('currency') . " " . number_format($price, 2) . "<del id='feeValueOld'></del><</div></td>";
            } else {
                $availability .= "<td class='brd_l'><div class='fee'><span style='color:#0FA1D2'>" . TXT_FUNC_FREE . "</span></div></td>";
            }

            $availability .= "</tr></table>";
            $availability .= "<br clear='all'><div class='social'>" . getSocial($row["id"]) . "</div>";
            $availability .= "</div>";
        }
        if ($event_count == 1) { } else if ($event_count > 1) {
            $text = "<p>" . TXT_PLSSELECT . "</p>";
        } else {
            $text = "";
        }

        $availability .= "</div>";
    }

    return $availability;
}

function getSocial($eventId)
{
    global $mysqli;
    global $baseDir;
    $query = "SELECT * FROM bs_events WHERE id={$eventId} ORDER BY eventDate ASC "; //print $_SERVER["HTTP_HOST"];
    $result = $mysqli->query($query);
    $row = $result->fetch_assoc();
    $url = "http://" . $_SERVER["HTTP_HOST"] . $baseDir . "event.php?eventID={$row['id']}";

    $soc = '<table><tr>';

    /*$soc.='<td>
    <div id="fb-root"><a href="javascript:;" onclick="openFbPopUp(\''.$url.'\')"><img src="images/facebook_like.png" alt="facebook Share"/></a></div>
    <script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
    <fb:like href="' . $url . '" send="true" layout="button_count" width="150" show_faces="true" font=""></fb:like>
    </td>';*/

    $soc .= '<td><a href="javascript:;" onclick="openFbPopUp(\'' . $url . '\')"><img src="images/facebook_like.png" alt="facebook Share"/></a></td>';

    $soc .= '<td><div style="display:inline-block">

<a href="https://twitter.com/share" class="twitter-share-button" data-via="BookingWizz" data-text="' . urlencode($row['title']) . '" data-lang="en" data-counturl="' . $_SERVER["HTTP_HOST"] . '" data-url="//' . $_SERVER["HTTP_HOST"] . '">Tweet</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script>
</div></td>';
    $soc .= "<td><g:plusone href=\"{$url}\" size=\"medium\"></g:plusone></td>";
    $soc .= "</tr></table>";
    return $soc;
}

function randomPassword(
    //autor: Femi Hasani [www.vision.to]
    $length = 7, //string length
    $uselower = 1, //use lowercase letters
    $useupper = 1, // use uppercase letters
    $usespecial = 1, //use special characters
    $usenumbers = 1, //use numbers
    $prefix = ''
) {
    $key = $prefix;
    // Seed random number generator
    srand((float) microtime() * rand(1000000, 9999999));
    $charset = "";
    if ($uselower == 1)
        $charset .= "abcdefghijkmnopqrstuvwxyz";
    if ($useupper == 1)
        $charset .= "ABCDEFGHIJKLMNPQRSTUVWXYZ";
    if ($usenumbers == 1)
        $charset .= "0123456789";
    //if ($usespecial == 1) $charset .= "#%^*()_+-{}][";
    if ($usespecial == 1)
        $charset .= "#*_+-";
    while ($length > 0) {
        $key .= $charset[rand(0, strlen($charset) - 1)];
        $length--;
    }
    return $key;
}
function get_month_list()
{
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
    for ($i = 1; $i < 8; $i++) {
        $r = date("l", strtotime("22-01-2012 +$i days"));
        $monthList[date("l", strtotime("22-01-2012 +$i days"))] = constant($r);
    }
    return $monthList;
}

function getShortWeek($n)
{
    $monthList = get_month_list();
    return strtr(date("D", strtotime("22-01-2012 +$n days")), $monthList);
}

function getWeek($n)
{
    $monthList = get_month_list();
    return strtr(date("l", strtotime("22-01-2012 +$n days")), $monthList);
}

#####################################################################################################

function getLangList()
{
    $langList = array();

    $path = MAIN_PATH . "\languages";
    $path1 = MAIN_PATH . "/languages";
    if (is_dir($path)) {
        $path = $path;
    } elseif (is_dir($path1)) {
        $path = $path1;
    }
    foreach (scandir($path) as $lang) {
        //print $lang;
        if (strpos($lang, "lang") !== false) {
            $langList[] = substr($lang, 0, strpos($lang, "."));
        }
    }

    return $langList;
}

function getLangNaw()
{
    $langList = array();
    $path = MAIN_PATH . "\languages\icons\\";
    $path1 = MAIN_PATH . "/languages/icons/";
    if (is_dir($path)) {
        $path = $path;
    } elseif (is_dir($path1)) {
        $path = $path1;
    }
    foreach (getLangList() as $lang) {
        if (is_file($path . $lang . ".png")) {
            $langList[$lang] = MAIN_URL . "languages/icons/{$lang}.png";
        }
    }
    return $langList;
}

function _getDate($date)
{
    $monthList = get_month_list();

    return strtr($date, $monthList);
}

function getDateFormat($date)
{
    $monthList = get_month_list();

    return strtr(date(getOption('date_mode'), strtotime($date)), $monthList);
}


function checkSchedule($reserveDateFrom, $reserveDateTo, $x1_from, $x2_from, $serviceID, $qty = 1, $id = null)
{
    global $mysqli;
    //print "$x1_from - $x2_from<br>";
    $a =  $reserveDateFrom;
    $b = $reserveDateTo;
    $serviceData = getServiceSettings($serviceID, "spaces_available");
    $serviceMultipl = getServiceSettings($serviceID, "show_multiple_spaces");
    //print $a."<br>".$b."<br><br>";

    $where = $id != null ? " AND bs_reserved_time.id !={$id}" : "";
    $sSQL = "SELECT SUM(qty) as qty FROM bs_reserved_time
				WHERE bs_reserved_time.recurring=0 AND bs_reserved_time.serviceID='{$serviceID}'{$where} AND (
			(reserveDateFrom < '{$b} {$x2_from}:00' AND reserveDateTo >= '{$b} {$x2_from}:00') OR
			(reserveDateTo > '{$a} {$x1_from}:00' AND reserveDateFrom <= '{$a} {$x1_from}:00') OR
			(reserveDateFrom <= '{$a} {$x1_from}:00' AND reserveDateTo >= '{$b} {$x2_from}:00') OR
			(reserveDateFrom >= '{$a} {$x1_from}:00' AND reserveDateTo <= '{$b} {$x2_from}:00'))"; //print $sSQL;

    $res = $mysqli->query($sSQL);

    if ($res->num_rows > 0) {
        //print "yes";
        $row = $res->fetch_assoc();
        $_qty = $row['qty'];
        if ($serviceMultipl) {
            if (($serviceData - $_qty) >= $qty) {
                return true;
            } else {
                return false;
            }
        } elseif ($_qty > 0) {
            return false;
        }
    }
    return true;
    //$result = $mysqli->query($sSQL) or die("err: " . $mysqli->error().$sSQL);
}

function sendMail($email, $subject, $template, $serviceID, $data = null)
{
    global $baseDir;
    $serviceSettings = getService($serviceID);
    $headers = "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/html; charset=utf-8\n";
    $headers .= "From: '{$serviceSettings['fromName']}' <{$serviceSettings['fromEmail']}> \n";

    if ($data == null) {
        $message = $template;
    } else {
        $data['{%server%}'] = $_SERVER['SERVER_NAME'];
        foreach ($data as $k => $v) {
            $$k = $v;
        }
        ob_start();
        include MAIN_PATH . "/emailTemplates/{$template}";
        $templ = ob_get_contents();
        ob_clean();

        //$templ=file_get_contents($_SERVER["DOCUMENT_ROOT"].$baseDir."emailTemplates/{$template}");
        $message = strtr($templ, $data);
    }
    $message .= "<br><br>Kind Regards,<br><a href='http://{$_SERVER['SERVER_NAME']}'>{$_SERVER['SERVER_NAME']}</a>";
    //$message.="<br><br><a href='http://{$_SERVER['SERVER_NAME']}'><img src='http://{$_SERVER['SERVER_NAME']}/images/logo_sm.png'></a>";
    mail($email, $subject, $message, $headers);
}

function sendMailFile($email, $subject, $template, $serviceID, $data = null, $file = null)
{
    global $baseDir;

    $boundary = "--" . md5(uniqid(time()));
    $EOL = PHP_EOL;

    $serviceSettings = getService($serviceID);
    $headers = "MIME-Version: 1.0;$EOL";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"$EOL";
    $headers .= "From: '{$serviceSettings['fromName']}' <{$serviceSettings['fromEmail']}> $EOL";
    $multipart = "--$boundary$EOL";
    $multipart .= "Content-Type: text/html; charset=utf-8$EOL";
    $multipart .= "Content-Transfer-Encoding: Quot-Printed$EOL";
    $multipart .= $EOL;

    if ($data == null) {
        $message = $template;
    } else {
        $data['{%server%}'] = $_SERVER['SERVER_NAME'];
        foreach ($data as $k => $v) {
            $$k = $v;
        }
        ob_start();
        include MAIN_PATH . "/emailTemplates/{$template}";
        $templ = ob_get_contents();
        ob_clean();

        //$templ=file_get_contents($_SERVER["DOCUMENT_ROOT"].$baseDir."emailTemplates/{$template}");
        $message = strtr($templ, $data);
    }
    $message .= "<br><br>Kind Regards,<br><a href='http://{$_SERVER['SERVER_NAME']}'>{$_SERVER['SERVER_NAME']}</a>";
    $multipart .= $message;
    if (!is_array($file)) {
        $name = "addToCalendar.ics";
        $multipart .= "$EOL--$boundary$EOL";
        $multipart .= "Content-Type: application/octet-stream; name=\"$name\"$EOL";
        $multipart .= "Content-Transfer-Encoding: base64$EOL";
        $multipart .= "Content-Disposition: attachment; filename=\"$name\"$EOL";
        $multipart .= $EOL; // раздел между заголовками и телом прикрепленного файла
        $multipart .= chunk_split(base64_encode($file));

        $multipart .= "$EOL--$boundary--$EOL";
    } else {
        foreach ($file as $name => $_file) {

            $multipart .= "$EOL--$boundary$EOL";
            $multipart .= "Content-Type: application/octet-stream; name=\"$name\"$EOL";
            $multipart .= "Content-Transfer-Encoding: base64$EOL";
            $multipart .= "Content-Disposition: attachment; filename=\"$name\"$EOL";
            $multipart .= $EOL; // раздел между заголовками и телом прикрепленного файла
            $multipart .= chunk_split(base64_encode($_file));
        }
        $multipart .= "$EOL--$boundary--$EOL";
    }


    mail($email, $subject, $multipart, $headers);
}

function get_menu()
{

    bw_do_action('get_menu');
}

function bw_get_page($page)
{
    //print "bw_get_page_$page";
    bw_do_action("bw_get_page_$page");
    return true;
}

function get_admin_page($page)
{
    //print "bw_get_page_$page";
    bw_do_action("get_admin_page_$page");
    return true;
}


function getBookingDetailsText($orderID)
{
    global $mysqli;
    $text = "";
    $q = "SELECT * FROM  bs_reservations_items WHERE reservationID ='{$orderID}'";
    $res = $mysqli->query($q);
    while ($rr = $res->fetch_assoc()) {
        $text .= "[ " . getDateFormat($rr["reserveDateFrom"]) . date((getTimeMode()) ? " g:i a" : " H:i", strtotime($rr["reserveDateFrom"])) . " - " .
            getDateFormat($rr["reserveDateTo"]) . date((getTimeMode()) ? " g:i a" : " H:i", strtotime($rr["reserveDateTo"])) . " ]";
    }
    return $text;
}

function get_payment_info($orderID)
{
    global $mysqli;
    $amount = 0;
    $tax = 0;
    $paymentInfo = $discount = '';
    $taxRate = getOption("enable_tax") ? getOption("tax") : 0;
    $bookingInfo = getBooking($orderID);
    $qty = $bookingInfo['qty'];
    $serviceSettings = getServiceSettings($bookingInfo['serviceID']);
    $deposit = 1;

    if (!empty($bookingInfo['coupon'])) {
        $couponData = checkCoupon($bookingInfo['coupon'], $bookingInfo['serviceID']);

        if ($couponData['responce']) {
            $couponValue = $couponData['value'];
            $couponType = $couponData['type'];
        }
    }

    if (empty($bookingInfo['eventID'])) {

        $deposit = getService($bookingInfo['serviceID'], 'deposit');
        if ($serviceSettings['type'] == 't') {
            $price = getServiceSettings($bookingInfo['serviceID'], "spot_price");

            $sql = "SELECT COUNT(*) as spots FROM bs_reservations_items WHERE reservationID ='{$orderID}'";
            $result = $mysqli->query($sql);
            $spots = $result->fetch_assoc()['spots'];

            $subAmount = $spots * $price * $qty;

            $paymentInfo = TXT_FUNC_PAYMENT_FOR . " " . getBookingDetailsText($orderID);
        } else {
            $sSQL = "SELECT * FROM bs_reservations_items WHERE reservationID='" . $orderID . "' ORDER BY reserveDateFrom ASC";
            $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
            $orderInfo = $result->fetch_assoc();
            $orderSummery = _checkForAvailability($orderInfo['reserveDateFrom'], $orderInfo['reserveDateTo'], $bookingInfo['serviceID']);
            $subAmount = $orderSummery['totalPrice'];
            /*$price = getDayPrice($orderInfo['reserveDateFrom'], $bookingInfo['serviceID']);
            
            $days = getDaysInterval($orderInfo['reserveDateFrom'], $orderInfo['reserveDateTo']);
            
            $subAmount = $days * $price * $qty;*/

            $paymentInfo = TXT_FUNC_PAYMENT_FOR . " " . getBookingDetailsText($orderID);
        }
    } else {

        $sql = "SELECT * FROM bs_events WHERE id ='{$bookingInfo['eventID']}'";
        $result = $mysqli->query($sql);
        $eventInfo = $result->fetch_assoc();

        if ($eventInfo['payment_required'] == 1 && !empty($eventInfo['entryFee'])) {
            $subAmount = $eventInfo['entryFee'] * $qty;

            $paymentInfo = TXT_FUNC_PAYMNT_EVENT . " '{$eventInfo['title']}' on " . getDateFormat($eventInfo["eventDate"]) . date((getTimeMode()) ? " g:i a" : " H:i", strtotime($eventInfo["eventDate"]));

            $deposit = $eventInfo['deposit'];
        }
    }
    $_subAmount = $subAmount;
    if (!empty($couponValue) && !empty($couponType)) {
        if ($couponType == 'abs') {
            $subAmount = $subAmount - $couponValue;
            $subAmount = $subAmount < 0 ? 0 : $subAmount;
            $discount = getCurrencyText(number_format($couponValue, 2));
        } else {
            $subAmount = $subAmount * (1 - $couponValue / 100);
            $discount = "{$couponValue} % ( " . getCurrencyText(number_format($_subAmount * $couponValue / 100, 2)) . " )";
        }
    }
    $tax = $subAmount * $taxRate / 100;
    $amount = $subAmount + $tax;
    $payAmount = $amount * $deposit;
    return array(
        "tax" => $tax,
        "subAmount" => $subAmount,
        "_subAmount" => $_subAmount,
        "taxRate" => $taxRate,
        "amount" => round($amount, 2),
        "paymentInfo" => $paymentInfo,
        "discount" => $discount,
        "amountToPay" => round($payAmount, 2),
        "deposit" => $deposit
    );
}

function payment_paypal($pre_text, $orderID, $type = null, $refferer = null)
{

    $payment_info = get_payment_info($orderID);
    $deposit = $payment_info['deposit'] < 1 && $payment_info['deposit'] > 0 ? $payment_info['deposit'] : 1;


    $paypal_form = $pre_text . TXT_FUNC_ALMOST_DONE; //($type==null?TXT_FUNC_ALMOST_DONE:"");
    if ((IS_WP_PLUGIN == '1' && $type != 'pay') || ($refferer == 'calendar' && getOption('use_popup') == '1')) {
        $paypal_form .= '<br><input type="button" value="' . TXT_FUNC_CLICK_HERE_TO_PAY . '" onclick="_redirect(\'http://' . MAIN_URL . 'paypal.processing.php?orderID=' . $orderID . '\')">';
    } else {
        //CREATE PAYPAL PROCESSING
        require_once(MAIN_PATH . '/includes/paypal.class.php');
        $paypal = new paypal_class;
        $paypal->add_field('business', getOption('pemail'));
        //$scrpt = str_replace("booking.processing.php", "paypal.ipn.php", $_SERVER['SCRIPT_NAME']);
        //$scrpt = str_replace("booking.event.processing.php", "paypal.ipn.php", $_SERVER['SCRIPT_NAME']);
        $scrpt = MAIN_URL . 'paypal.ipn.php';
        $paypal->add_field('return', "http://" . $scrpt . '?action=success');
        $paypal->add_field('cancel_return', "http://" . $scrpt . '?action=cancel');
        $paypal->add_field('notify_url', "http://" . $scrpt . '?action=ipn');
        $paypal->add_field('item_name_1', $payment_info['paymentInfo']);
        $paypal->add_field('amount_1', number_format($payment_info['subAmount'] * $deposit, 2));
        $paypal->add_field('item_number_1', "0001");
        $paypal->add_field('quantity_1', '1');
        $paypal->add_field('custom', $orderID);
        $paypal->add_field('upload', 1);
        $paypal->add_field('cmd', '_cart');
        $paypal->add_field('txn_type', 'cart');
        $paypal->add_field('no_shipping', '1');
        if (!empty($payment_info['tax'])) {
            $paypal->add_field('tax_cart', number_format($payment_info['tax'] * $deposit, 2));
        }
        $paypal->add_field('num_cart_items', 1);
        $paypal->add_field('payment_gross', number_format($payment_info['subAmount'] * $deposit, 2));
        $paypal->add_field('currency_code', getOption('pcurrency'));
        $paypal_form .= "<form method=\"post\" name=\"paypal_form\" id=\"paypal_form\"";
        $paypal_form .= "action=\"" . $paypal->paypal_url . "\">\n";
        foreach ($paypal->fields as $name => $value) {
            $paypal_form .= "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
        }
        $paypal_form .= "<input type=\"submit\" class=\"submitProcessing\" value=\"" . TXT_FUNC_CLICK_HERE_TO_PAY . "\"></center>\n";
        $paypal_form .= "</form>\n";
    }
    return $paypal_form;
}

function payment_invoice($pre_text, $orderID, $type, $refferer = null)
{
    $text = $pre_text . TXT_FUNC_THANK_YOU_MSG;
    if (IS_WP_PLUGIN != '1') {
        //$text .='<a href="http://'. MAIN_URL.'index.php">'.BEP_15.'</a>';
    }
    return $text;
}

function do_payment($orderID, $payment_method, $type = null, $referrer = null)
{
    global $paymentMethods;
    $value = "";
    if (in_array($payment_method, $paymentMethods)) {
        bw_add_action("do_payment", "payment_" . $payment_method, $orderID, $type, $referrer);
        return bw_apply_filter("do_payment", $value, $orderID, $type, $referrer);
    } else {
        $orderInfo = getBooking($orderID);

        _error_log("Error 'do_payment function (orderID = {$orderID};payment_method= {$payment_method} ; booking-info=" . print_r($orderInfo, true) . ")'");


        if (!empty($orderInfo['eventID'])) {
            $eventInfo = getEventInfo($orderInfo['eventID']);
            $payment_method = $eventInfo['payment_method'];
        } else {
            $payment_method = getServiceSettings($orderInfo['serviceID'], "payment_method");
        }
        bw_add_action("do_payment", "payment_" . $payment_method, $orderID, $type);
        return bw_apply_filter("do_payment", $value, $orderID, $type);
    }
}

function sendPaymentEmails($orderId, $by = "")
{
    global $mysqli;
    $sql = "SELECT *,bs.serviceID as sid FROM bs_transactions bt
            INNER JOIN  bs_reservations bs ON bs.id=bt.reservationID
            LEFT JOIN bs_events e ON bt.eventID=e.id
    
            WHERE bt.reservationID='{$orderId}'";

    $res = $mysqli->query($sql);
    $row = $res->fetch_assoc();
    $service = getService($row['sid']);
    $serviceSettings = getServiceSettings($row['sid']);
    $subject = " Payment for order #{$orderId}";
    $data = array(
        "{%orderId%}" => $orderId,
        "{%name%}" => $row['name'],
        "{%trnID%}" => $row['transactionID'],
        "{%currency%}" => $row['currency'],
        "{%payer_email%}" => $row['payer_email'],
        "{%payer_name%}" => $row['payer_name'],
        "{%amount%}" => $row['amount'],
        "{%paymentProcessor%}" => $by
    );

    if (!empty($row['eventID'])) {
        $data['isEvent'] = true;
        $data['{%eventName%}'] = $row['title'];
        $data['{%description%}'] = $row['description'];
        if ($row['eventDate'] != $row['eventDateEnd']) {
            $Edate = getEventStartEndDate($row['eventID'], $row['date']);
        }
        $data['{%eventDate%}'] = $Edate;
        sendMail($row['email'], $subject, "paymentConfirmationEvent.php", $row['sid'], $data);
        sendMail(getAdminMail(), $subject, "paymentConfirmationEvent.php", $row['sid'], $data);
    } else {
        $ssql = "SELECT * FROM bs_reservations_items WHERE reservationID='{$orderId}'";
        $ress = $mysqli->query($ssql);
        if ($serviceSettings['type'] == 't') {
            $data['isTime'] = true;
            $time = array();
            $date = "";
            while ($r = $ress->fetch_assoc()) {
                $time[] = array("from" => _time($r['reserveDateFrom']), "to" => _time($r['reserveDateTo']), "qty" => $r['qty']);
                $date = getDateFormat($r['reserveDateFrom']);
            }
            $data['times'] = $time;
            $data['{%date%}'] = $date;
            $data['{%serviceName%}'] = $service['name'];
            sendMail($row['email'], $subject, "paymentConfirmationTime.php", $row['sid'], $data);
            sendMail(getAdminMail(), $subject, "paymentConfirmationTime.php", $row['sid'], $data);
        } else {
            $data['isDay'] = true;
            $bookData = $ress->fetch_assoc();
            $dateFrom = date("Y-m-d", strtotime($bookData['reserveDateFrom']));
            $dateTo = date("Y-m-d", strtotime($bookData['reserveDateTo']));
            $days = getDaysInterval($dateFrom, $dateTo);

            $data['{%from%}'] = getDateFormat($dateFrom);
            $data['{%to%}'] = getDateFormat($dateTo);
            $data['{%days%}'] = $days;
            $data['{%serviceName%}'] = $service['name'];
            $data['{%serviceDescr%}'] = nl2br($serviceSettings['description']);
            sendMail($row['email'], $subject, "paymentConfirmationDay.php", $row['sid'], $data);
            sendMail(getAdminMail(), $subject, "paymentConfirmationDay.php", $row['sid'], $data);
        }
    }
}
function _time($date)
{
    return date((getTimeMode()) ? " g:i a" : " H:i", strtotime($date));
}
function _date($date)
{
    return date("Y-m-d", strtotime($date));
}
function _hh($date)
{
    return date("H", strtotime($date));
}
function _mm($date)
{
    return date("i", strtotime($date));
}
function checkCoupon($couponCode, $serviceID)
{
    $responce = array();
    global $mysqli;
    $sql = "SELECT * FROM bs_coupons WHERE code='{$couponCode}'";
    $res = $mysqli->query($sql);
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if ($row['dateFrom'] <= date("Y-m-d") && $row['dateTo'] >= date("Y-m-d")) {
            $services = explode(",", $row['services']);
            if (in_array($serviceID, $services)) {
                $responce = array("responce" => true, "value" => $row['value'], "type" => $row['type']);
            } else {
                $responce = array("responce" => false, "message" => "This coupon not accepted fo this service");
            }
        } else {
            $responce = array("responce" => false, "message" => "This coupon out of date");
        }
    } else {
        $responce = array("responce" => false, "message" => "Coupon not found");
    }

    return $responce;
}
function checkCouponCode($code)
{
    global $mysqli;
    $sql = "SELECT * FROM bs_coupons WHERE code='{$code}'";
    $res = $mysqli->query($sql);
    if ($res->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}
function getCurrencyText($value)
{
    return (getOption('currency_position') == 'b' ? getOption('currency') . "&nbsp;" : "") . "$value" . (getOption('currency_position') == 'a' ? "&nbsp;" . getOption('currency') : "");
}

function timeNowDiff($time, $type = 'hours')
{

    $diff = strtotime($time) - strtotime("now");

    switch ($type) {
        case "hours":
            $diff = $diff < 0 ? 0 : round($diff / (60 * 60));
            break;
        case "minutes":
            $diff = $diff < 0 ? 0 : round($diff / 60);
            break;
        case "days":
            $diff = $diff < 0 ? 0 : round($diff / (60 * 60 * 24));
            break;
    }
    return $diff;
}

function dateToUTC($date, $format = 'Y-m-d H:i:s')
{
    $dt = new DateTime($date);
    $tz = new DateTimeZone("UTC");
    $dt->setTimezone($tz);
    return $dt->format($format);
}


function cron($type = 'cron')
{
    global $mysqli;
    bw_do_action("bw_load");
    $single_day_notification_time = getOption("single_day_notification");
    $multi_day_notification_time = getOption("multi_day_notification");
    $event_notification_time = getOption("event_notification");
    $output = '';

    //Check for single-day bookings
    if ($single_day_notification_time > 0 && getOption('single_day_notification_on') == 'y') {
        $output .= "=== Singl-Day Bookings<br>";
        $sql = "SELECT br.* FROM bs_services bs
        INNER JOIN bs_reservations br ON br.serviceID=bs.id
        WHERE bs.type='t' AND br.status IN('1','4') AND br.reminder_sent='n'";
        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc()) {

            $sql = "SELECT * FROM bs_reservations_items WHERE reservationID = '{$row['id']}' ORDER BY reserveDateFrom ASC";
            $rres = $mysqli->query($sql);
            $bookingData = array();
            while ($rows = $rres->fetch_assoc($rres)) {
                $bookingData[] = array(
                    'date' => getDateFormat($rows['reserveDateFrom']),
                    'timeFrom' => date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rows['reserveDateFrom'])),
                    'timeTo' => date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rows['reserveDateTo'])),
                    'qty' => $row['qty'],
                    'dateFrom' => getDateFormat(_date($rows['reserveDateFrom'])),
                    'dateTo' => getDateFormat(_date($rows['reserveDateTo'])),
                    '_timeFrom' => $rows['reserveDateFrom']
                );
            }
            $time = $bookingData[0]['_timeFrom'];
            $diff = timeNowDiff($time); //$output.= "$diff - {$row['id']} - {$time}<br>";

            if ($diff < $single_day_notification_time && $diff != 0) {

                $subject = "Priminimas atvykti";
                $data = array(
                    "{%name%}" => $row['name'],
                    "{%serviceName%}" => getService($row['serviceID'], 'name'),
                    "_info" => $bookingData,
                    "{%orderID%}" => $row['id']
                );

                sendMail($row['email'], $subject, "reminderSingleBooking.php", $row['serviceID'], $data);
                $output .= "send email for Singl-Day reservation #{$row['id']}<br>";

                $zinute = urlencode("Primename, kad rytoj laukiame Jūsų BreakRoom. BreakRoom: +37068449944");
                $sms = "http://smsplus1.routesms.com:8080/bulksms/bulksms?username=supersmslt&password=k7bd7y&type=0&dlr=1&destination=" . $row['phone'] . "&source=BreakRoom&message=" . $zinute;
                //echo $sms;
                $ch = curl_init($sms);
                curl_exec($ch);



                $ssql = "UPDATE bs_reservations SET reminder_sent='y' WHERE id='{$row['id']}'";
                $mysqli->query($ssql);
            }
        }
        $output .= "=======================================================================<br>";
    }

    //Check for multi-day bookings
    if ($multi_day_notification_time > 0 && getOption('multi_day_notification_on') == 'y') {
        $output .= "=== Multi-Day Bookings<br>";
        $sql = "SELECT br.*,bri.reserveDateFrom,bri.reserveDateTo FROM bs_services bs
        INNER JOIN bs_reservations br ON br.serviceID=bs.id
        INNER JOIN bs_reservations_items bri ON bri.reservationID = br.id
        WHERE bs.type='d' AND br.status IN('1','4') AND br.reminder_sent='n'";
        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc()) {

            $time = $row['reserveDateFrom'];
            $diff = timeNowDiff(_date($time) . " 12:00:00"); //$output.= "$diff<br>";
            if ($diff < $multi_day_notification_time && $diff != 0) {

                $subject = "Booking Reminder";
                $data = array(
                    "{%name%}" => $row['name'],
                    "{%service%}" => getService($row['serviceID'], 'name'),
                    "{%orderID%}" => $row['id'],
                    "{%dateFrom%}" => getDateFormat(_date($row['reserveDateFrom'])),
                    "{%dateEnd%}" => getDateFormat(_date($row['reserveDateTo'])),
                    "{%days%}" => getDaysInterval($row['reserveDateFrom'], $row['reserveDateTo'])
                );

                sendMail($row['email'], $subject, "reminderDayBooking.php", $row['serviceID'], $data);
                $output .= "send email for Multi-Day reservation #{$row['id']}<br>";
                //bw_do_action('bw_send_message',$row);


                $ssql = "UPDATE bs_reservations SET reminder_sent='y' WHERE id='{$row['id']}'";
                $mysqli->query($ssql);
            }
        }
        $output .= "=======================================================================<br>";
    }

    //Check for event bookings
    if ($event_notification_time > 0 && getOption('event_notification_on') == 'y') {
        $output .= "=== Event Bookings<br>";
        $sql = "SELECT br.name,br.qty,br.id,be.title,br.date,be.description,be.location,be.map_link,be.eventTime,be.id as eid,br.phone,br.eventID FROM bs_reservations br
            INNER JOIN bs_events be ON be.id=br.eventID
            WHERE br.status IN('1','4') AND br.reminder_sent='n' AND br.eventID IS NOT NULL";
        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc()) {


            $time = "{$row['date']} {$row['eventTime']}";
            $diff = timeNowDiff($time); //$output.= "$diff<br>";
            if ($diff < $event_notification_time && $diff != 0) {

                $subject = "Booking Reminder";
                $data = array(
                    "{%name%}" => $row['name'],
                    "{%service%}" => getService($row['serviceID'], 'name'),
                    "{%orderID%}" => $row['id'],
                    "{%eventName%}" => $row['title'],
                    "{%eventDate%}" => getEventStartEndDate($row['eid'], $row['date']),
                    "{%qty%}" => $row['qty'],
                    "{%eventDescr%}" => $row['description'],
                    "{%eventLocation%}" => $row['location'],
                    "{%eventMapLink%}" => $row['map_link']
                );

                sendMail($row['email'], $subject, "reminderEvent.php", $row['serviceID'], $data);

                $output .= "send email for Event reservation #{$row['id']}<br>";
                //bw_do_action('bw_send_message',$row);

                $ssql = "UPDATE bs_reservations SET reminder_sent='y' WHERE id='{$row['rid']}'";
                $mysqli->query($ssql);
            }
        }
        $output .= "=======================================================================<br>";
    }

    ob_start();
    print $output;
    bw_do_action("bw_cron");


    if ($type == 'regular') {
        ob_end_clean();
    } else {

        ob_end_flush();
    }
}
