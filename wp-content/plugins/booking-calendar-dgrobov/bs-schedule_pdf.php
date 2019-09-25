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

require_once('./includes/config.php');
require_once('./tcpdf/config/lang/eng.php');
require_once('./tcpdf/tcpdf.php');

$date = (!empty($_REQUEST["selectedDay"])) ? strip_tags(str_replace("'", "`", $_REQUEST["selectedDay"])) : date("Y-m-d");
$serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();;

$serviceName = getService($serviceID, 'name');

$data = array();
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'P', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('Shedule for ' . $serviceName);
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);


// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetMargins(5, 5, 5, true);
/*
//set margins
$pdf->SetMargins(5, 5, 5);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);*/

//set auto page breaks
$pdf->SetAutoPageBreak(false, 0);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);


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
        $reservationInfo = "<tr class='{$classRow} rr_{$tFrom}'><td>&nbsp;</td><td>{$rr['qty']}</td><td>{$rr["name"]}</td><td>{$rr["phone"]}</td><td><a 'bs-bookings-edit.php?id=" . $rr["rid"] . "'>{$rr['email']}</a></td><td>&nbsp;</td></tr>";

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
$J = 1;

//bw_dump($events);
//$ww= date("w",strtotime($date));
//$tt = getStartEndTime($ww,$serviceID);
if (!count($availabilityArr)) {
    $availability .= ADM_NONWORKING;
} else {
    $availability .= "<table style=\"width:100%\" border=\"0\"  align=\"left\" cellpadding=\"3\" cellspacing=\"0\">";
    $availability .= "<tr><th style=\"background-color: #000000;color:#ffffff;padding:5px\">Time</th>
                        <th style=\"background-color: #000000;color:#ffffff;padding:5px\">Spots Left</th>
                        <th style=\"background-color: #000000;color:#ffffff;padding:5px\">Customer Name</th>
                        <th style=\"background-color: #000000;color:#ffffff;padding:5px\">Customer Phone</th>
                        <th style=\"background-color: #000000;color:#ffffff;padding:5px\">Customer Email</th>
                        </tr>";
    $n = ($n - ($n % 2)) / 2;
    $count = 0;
    $i = 0;

    foreach ($availabilityArr as $k => $v) { //$v= date  (  2010-10-05 )
        foreach ($v as $kk => $vv) { //$vv = time slot in above date
            $i = $i ? 0 : 1;
            $class = $i ? "odd" : "even";

            $time = _time($vv) . "-" . _time(date("Y-m-d H:i", strtotime($vv . " +" . $int . " minutes")));
            $bookLink = "<a href='bs-reserve.php?serviceID={$serviceID}&reserveDateFrom={$date}&reserveDateTo={$date}&1_from_h=" . date("H", strtotime($vv)) . "&1_from_m=" . date("i", strtotime($vv)) . "&2_from_h=" . date("H", strtotime($vv . " +" . $int . " minutes")) . "&2_from_m=" . date("i", strtotime($vv . " +" . $int . " minutes")) . "' >Book</a>";
            if (isset($events[$k]) && in_array($vv, $events[$k])) {
                $availability .= "<tr><td style=\"border-bottom:0.1mm dashed #999999\"><img src=\"./images/new/clock.jpg\" height=\"10\"/><span>" . $time . "</span></td><td style=\"border-bottom:0.1mm dashed #999999;border-left:1px solid #eee\">Event</td><td style=\"border-bottom:0.1mm dashed #999999\">&nbsp;</td><td style=\"border-bottom:0.1mm dashed #999999\">&nbsp;</td><td style=\"border-bottom:0.1mm dashed #999999\">&nbsp;</td></tr>";
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
                    $adminReserveData .= "<tr><td>&nbsp;</td><td style=\"border-left:1px solid #eee\">{$data['qty']}</td><td style=\"border-left:1px solid #eee\">Manual Booking ( {$data['reason']} )</td><td style=\"border-left:1px solid #eee\">&nbsp;</td><td style=\"border-left:1px solid #eee\">&nbsp;</td></tr>";
                }
                $spacesAllowed = $availebleSpaces - $spacesBooked - $spacesBookedUser;
                $userBookings = "";
                if (($availebleSpaces - $spacesBooked) > 0) {

                    if (isset($reservationData1[$k][$vv])) {
                        foreach ($reservationData1[$k][$vv] as $rr) {
                            $classRow = $classRow == 'even1' ? "odd1" : "even1";
                            $viewLink = "<a href=\"bs-bookings-edit.php?id=" . $rr["rid"] . "\" class=\"greedButton grey\">Edit</a>";
                            $userBookings .= "<tr><td>&nbsp;</td><td style=\"border-left:1px solid #eee\">{$rr['qty']}</td><td style=\"border-left:1px solid #eee\">{$rr["name"]}</td><td style=\"border-left:1px solid #eee\">{$rr["phone"]}</td><td style=\"border-left:1px solid #eee\">{$rr['email']}</td></tr>";
                        }
                    }
                } else {
                    $spacesAllowed = 0;
                }
                if ($show_multiple_spaces) {
                    $availability .= "<tr><td style=\"border-bottom:0.1mm dashed #999999\"><img src=\"./images/new/clock.jpg\" height=\"10\"/><span>" . $time . "</span></td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\"><span><b>{$spacesAllowed} out of {$availebleSpaces}</b></span></td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">&nbsp;</td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">&nbsp;</td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">&nbsp;</td></tr>";
                } else {
                    $availability .= "<tr><td style=\"border-bottom:0.1mm dashed #999999\"><img src=\"./images/new/clock.jpg\" height=\"10\"/><span>" . $time . "</span></td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\"><span><b>0 out of 1</b></span></td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">&nbsp;</td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">&nbsp;</td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">&nbsp;</td></tr>";
                }
                $availability .= $adminReserveData . $userBookings;
            } elseif (isset($users[$k][$vv])/* || (isset($users[$k]) && array_key_exists($vv, $users[$k]))*/) {
                $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                //$availebleSpaces;
                $spacesBooked = $users[$k][$vv];
                $spacesAllowed = $availebleSpaces - $spacesBooked;
                if ($show_multiple_spaces) {
                    $userBookings = '';
                    if (isset($reservationData1[$k][$vv])) {

                        foreach ($reservationData1[$k][$vv] as $rr) {
                            $classRow = $classRow == 'even1' ? "odd1" : "even1";
                            $viewLink = "<a href=\"bs-bookings-edit.php?id=" . $rr["rid"] . "\" class=\"greedButton grey\">Edit</a>";
                            $userBookings .= "<tr><td>&nbsp;</td><td  style=\"border-left:1px solid #eee\">{$rr['qty']}</td><td style=\"border-left:1px solid #eee\">{$rr["name"]}</td><td style=\"border-left:1px solid #eee\">{$rr["phone"]}</td><td style=\"border-left:1px solid #eee\">{$rr['email']}</td></tr>";
                        }
                    }
                    $bookLink = $spacesAllowed > 0 ? $bookLink : "";
                    $availability .= "<tr><td style=\"border-bottom:0.1mm dashed #999999\"><img src=\"./images/new/clock.jpg\" height=\"10\"/><span>" . $time . "</span></td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\"><span><b>{$spacesAllowed} out of {$availebleSpaces}</b></span></td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">&nbsp;</td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">&nbsp;</td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">&nbsp;</td></tr>";
                    $availability .= $userBookings;
                    //$availability .="<tr class='schedule_av  class=\"$class\"".($spacesAllowed==0?"empty":"")."'><td width='100' valign='top' class='time'><div>" . $time . "</div></td><td valign='top'><span class='space'>{$spacesAllowed}</span>".($spacesAllowed?$bookLink:"") .SPC_LEFT . $reservationData[$k][$vv] . "</td></tr>";
                } else {
                    $rr = $reservationData1[$k][$vv][0];
                    $viewLink = "<a href=\"bs-bookings-edit.php?id=" . $rr["rid"] . "\" class=\"greedButton grey\">Edit</a>";
                    $availability .= "<tr><td style=\"border-bottom:0.1mm dashed #999999\"><img src=\"./images/new/clock.jpg\" height=\"10\"/><span>" . $time . "</span></td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\"><span><b>1 out of 1</b></span></td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">{$rr["name"]}</td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">{$rr["phone"]}</td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">{$rr['email']}</td></tr>";
                }
            } else {
                $availebleSpaces = $show_multiple_spaces ? $availebleSpaces : 1;
                //$availability .= "<tr class='schedule_av'><td width='100' valign='top' class='time'><div>" . $time . "</div></td><td valign='top'><span class='space'>{$availebleSpaces}</span>". SPC_LEFT. $reservationData[$k][$vv] . "{$bookLink}</td></tr>";
                $availability .= "<tr><td style=\"border-bottom:0.1mm dashed #999999\"><img src=\"./images/new/clock.jpg\" height=\"10\"/><span>" . $time . "</span></td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\"><span><b>{$availebleSpaces} out of {$availebleSpaces}</b></span></td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">N/A</td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">N/A</td><td style=\"border-left:1px solid #eee;border-bottom:0.1mm dashed #999999\">N/A</td></tr>";
            }


            $count++;
            $J++;
        }
    }

    $availability .= "</table>";
    //print $availability;
}
##########################################################################################################################

//print  $availability;exit();
$pdf->AddPage('P', 'A4', true);
// add a page
$style = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '1,2', 'phase' => 10, 'color' => array(150, 150, 150));

$pdf->SetLineStyle($style);
$pdf->SetCellPadding(0);

$header = "<font size=\"+12\">" . SCHEDL . " for " . getService($serviceID, 'name') . "</font><div style=\"height:1px\"></div>
                <font size=\"+2\">" . getDateFormat(_date($date)) . "</font>";
$pdf->writeHTMLCell(173, 30, 5, 5, $header, '', 1, 0, true, 'L', true);

$pdf->writeHTMLCell('', '', 5, '', $availability, '', 1, 0, true, 'L', true);
$pdf->Output('example_001.pdf', 'I');
