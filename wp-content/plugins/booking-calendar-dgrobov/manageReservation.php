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
include "includes/dbconnect.php";
include "includes/config.php";

$email = !empty($_REQUEST["email"]) ? addslashes(urldecode($_REQUEST["email"])) : "";

$uid = !empty($_REQUEST["uid"]) ? $_REQUEST["uid"] : "";

$uidOr = md5($email . 'FtTtffT');
//print $email."<br>".$uid."<br>".$uidOr;


########################################################################################################################################################
//"delete selected attendees" action processing.
if (!empty($_REQUEST["del"]) && $_REQUEST["del"] == "yes" && !empty($email) && $uid == $uidOr && !empty($_REQUEST["status"]) && !empty($_REQUEST["id"])) {
    $todelID = (!empty($_REQUEST["bsid"])) ? strip_tags(str_replace("'", "`", $_REQUEST["bsid"])) : '';
    if (!empty($todelID)) {

        //if($_REQUEST["status"]!=2 && $_REQUEST["status"]!=5){
        ##################################################################################
        #  	 SEND NOTICE TO ADMIN AND CUSTOMER
        //send email to admin
        $bookingId = $_REQUEST["id"];
        $bookingData = getBooking($bookingId);
        $delBookings = getService($bookingData['serviceID'], 'delBookings');
        if (!(($bookingData['status'] == 1 || $bookingData['status'] == 4) && $delBookings == 'n')) {

            $sql = "UPDATE bs_reservations SET status=5 WHERE id='" . $todelID . "'";
            $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete events 2");
            $msg2 .= "<span style='color:#00aa00'>" . MNG_ATTDEL . "</span>";


            $bookingName = $bookingData['name'];
            $bookingPhone = $bookingData['phone'];
            $bookingEmail = $bookingData['email'];
            $bookingServiceData = getService($bookingData['serviceID']);

            $paymentBookingIngo = get_payment_info($bookingId);
            $mailData = array(
                "{%service%}" => $bookingServiceData['name'],
                "{%name%}" => $bookingName,
                "{%phone%}" => $bookingPhone,
                "{%email%}" => $bookingEmail,
                "{%id%}" => $bookingId
            );
            $adminMail = getAdminMail();
            //mail($adminMail,$subject,$message,$headers);
            $subject = "Customer cancelled reservation #{$bookingId}";
            sendMail($adminMail, $subject, "userCancelReservation.php", $bookingData['serviceID'], $mailData);

            if (!empty($bookingData['eventID'])) {
                $bookingEvent = getEventInfo($bookingData['eventID']);
                $eventStarts = getEventStartEndDate($bookingData['eventID'], $bookingData['date']);
                $mailData = array(
                    "{%name%}" => $bookingName,
                    "{%service%}" => $bookingServiceData['name'],
                    "{%eventName%}" => $bookingEvent[0],
                    "{%eventDate%}" => $eventStarts,
                    "{%eventDescr%}" => $bookingEvent[1],
                    "{%qty%}" => $qty,
                    "{%status%}" => BOOKING_FRM_CANCELLED,
                    "{%id%}" => $bookingId
                );
                $subject = "Confirmation cancellation for reservation #{$bookingId}";
                sendMail($bookingEmail, $subject, "eventBookingCancelationCustomer.php", $bookingData['serviceID'], $mailData);
            } else {
                if ($bookingServiceData['type'] == 't') { //time booking

                    $bookingTimeData = array();
                    $Sqll = "SELECT * FROM bs_reservations_items WHERE reservationID='{$bookingId}'";
                    $Ress = $mysqli->query($Sqll);
                    while ($row = $Ress->fetch_assoc()) {

                        $date = getDateFormat($row['reserveDateFrom']);

                        $bookingTimeData[] = array(
                            'date' => getDateFormat($date),
                            'timeFrom' => date((getTimeMode()) ? "g:i a" : "H:i", strtotime($row['reserveDateFrom'])),
                            'timeTo' => date((getTimeMode()) ? "g:i a" : "H:i", strtotime($row['reserveDateTo'])),
                            'qty' => $row['qty']
                        );
                    }
                    $mailData = array(
                        "{%name%}" => $bookingName,
                        "{%serviceName%}" => $bookingServiceData['name'],
                        "{%status%}" => BOOKING_FRM_CANCELLED,
                        "_info" => $bookingTimeData,
                        "{%id%}" => $bookingId

                    );
                    $subject = "Confirmation cancellation for reservation #{$bookingId}";
                    sendMail($bookingEmail, $subject, "timeBookingCancelationCustomer.php", $bookingData['serviceID'], $mailData);
                } else { //day booking
                    $serviceSettings = getServiceSettings($bookingData['serviceID']);
                    $bookingTimeData = array();

                    $Sqll = "SELECT * FROM bs_reservations_items WHERE reservationID='{$bookingId}'";
                    $Ress = $mysqli->query($Sqll);
                    $bookDates = $Ress->fetch_assoc();
                    $days = getDaysInterval($bookDates['reserveDateFrom'], $bookDates['reserveDateTo']);
                    $mailData = array(
                        "{%name%}" => $bookingName,
                        "{%service%}" => $bookingServiceData['name'],
                        "{%dayDescr%}" => $serviceSettings['description'],
                        "{%from%}" => getDateFormat($bookDates['reserveDateFrom']),
                        "{%to%}" => getDateFormat($bookDates['reserveDateTo']),
                        "{%qty%}" => $bookingData['qty'],
                        "{%days%}" => $days,
                        "{%status%}" => BOOKING_FRM_NOTCONFIRMED,
                        "{%id%}" => $bookingId


                    );
                    $subject = "Confirmation cancellation for reservation #{$bookingId}";
                    sendMail($bookingEmail, $subject, "dayBookingCancelationCustomer.php", $bookingData['serviceID'], $mailData);
                }
            }
        }
    }
}
########################################################################################################################################################





//prepare attendees page.
$files_table = "";
###################################################################################################################################################
if (!empty($email) && $uid == $uidOr) {
    //PAGES TABLE  GENERATION TO SHOW IN HTML BELOW
    $sql = "SELECT br.*,e.title as eventTitle,e.eventTime,e.recurring,e.payment_required,e.eventDate,e.eventDateEnd, s.name as serviceName,s.type as stype,s.delBookings  FROM bs_reservations br
			INNER JOIN bs_services s ON br.serviceID=s.id
			LEFT JOIN bs_events e ON e.id=br.eventID
			WHERE br.email='" . $email . "' 
			ORDER BY br.dateCreated DESC";
    $result = $mysqli->query($sql) or die("error getting attendees from db");
    if ($result->num_rows > 0) {
        while ($rr = $result->fetch_assoc()) {

            //$editable="<a href=\"bs-events-add.php?id=".$rr["id"]."\"><img src=\"images/pencil_16.png\" alt=\"Edit this event\" border=\"0\"/></a>";  
            $paymentRequired = false;
            $paymentLink = '';
            $bookDateFrom = $bookDateTo = "";
            $bookDateFrom = getDateFormat($rr["eventDate"]);
            $bookDateTo = getDateFormat($rr["eventDateEnd"]);
            if ($rr['status'] == 2) {
                if (!empty($rr['eventID'])) {
                    if ($rr['payment_required'] == 1) {
                        if (getSpotsLeftForEvent($rr['eventID'], ($rr['recurring'] ? $rr['date'] : "")) >= $rr['qty']) {
                            $paymentRequired = true;
                        } else {
                            $rr['status'] = 3;
                        }
                    }
                } else {
                    if ($rr['stype'] == 't') {
                        if (getServiceSettings($rr['serviceID'], 'spot_price')) {
                            $times = array();
                            $date = '';
                            $Sqll = "SELECT * FROM bs_reservations_items WHERE reservationID='{$rr['id']}'";
                            $Ress = $mysqli->query($Sqll);
                            while ($row = $Ress->fetch_assoc()) {
                                $h = date("H", strtotime($row['reserveDateFrom']));
                                $m = date("i", strtotime($row['reserveDateTo']));
                                $times[] = ($h * 60) + $m;
                                $date = date("Y-m-d", strtotime($row['reserveDateFrom']));
                            }

                            if (checkQtyForTimeBooking($rr['serviceID'], $times, $date, getServiceSettings($rr['serviceID'], 'interval'), $rr['qty'])) {

                                $rr['status'] = 3;
                            } else {
                                $paymentRequired = true;
                            }
                        }
                    } else {
                        $paymentLink = "<a href='payment_order.php?orderID={$rr['id']}&serviceID={$rr['serviceID']}'>Pay</a>";
                    }
                }
            }

            if ($paymentRequired) {
                $paymentLink = "<a href='payment_order.php?orderID={$rr['id']}&serviceID={$rr['serviceID']}'>Pay</a>";
            }

            $editable = "&nbsp;";
            $delete = "<a href='manageReservation.php?email=" . $email . "&amp;uid=" . $uid . "&amp;id=" . $rr['id'] . "&amp;del=yes&amp;bsid=" . $rr["id"] . "&amp;status=" . $rr['status'] . "'><img src='images/delete_16.png' width='10' border=\"0\"></a>";
            //$delete = "<a href='javascript:;' onclick=\"if(confirm('are you sure?')){ window.location='manageReservation.php?email=" . $email . "&uid=" . $uid . "&id=" . $rr['id'] . "&del=yes&bsid=" . $rr["id"] . "&status=" . $rr['status'] . "';}\"><img src = 'images/delete_16.png' width = '10' border = \"0\"></a>";
            $status = '';
            $serviceName = $rr['serviceName'];
            $eventName = $rr['eventTitle'];


            $qq = "SELECT * FROM bs_reservations_items WHERE reservationID='" . $rr["id"] . "'";
            $res = $mysqli->query($qq);
            if ($res->num_rows > 0) {
                $time = '';
                while ($r2 = $res->fetch_assoc()) {
                    $time .= date(((getTimeMode()) ? "g:i a" : "H:i"), strtotime($r2["reserveDateFrom"])) . " to " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($r2["reserveDateTo"])) . "<br/>";
                    $bookDateFrom = getDateFormat($r2["reserveDateFrom"]);
                    $bookDateTo = getDateFormat($r2["reserveDateTo"]);
                }
            }
            if (!empty($rr['eventID'])) {
                $time = date(((getTimeMode()) ? "g:i a" : "H:i"), strtotime($rr['eventDate'])) .
                    " to " . date(((getTimeMode()) ? "g:i a" : "H:i"), strtotime($rr['eventDateEnd']));
                $_bookingDates = getEventStartEndDate($rr['eventID'], $rr['date'], 'array');
                $bookingDates = "from: " . getDateFormat($_bookingDates['fromDate']) . "<br/>to: " . getDateFormat($_bookingDates['toDate']);
            } else {
                if ($rr['stype'] == 'd') {
                    $time = "";
                    $bookingDates = "from:{$bookDateFrom}<br/>to:{$bookDateTo}";
                } else {
                    $bookingDates = $bookDateFrom;
                }
                //$time = "" ;
            }

            if ($rr['status'] == 5) {
                $delete = "";
            } elseif (($rr['status'] == 1 || $rr['status'] == 4) && $rr['delBookings'] == 'n') {
                $delete = "";
            }
            switch ($rr['status']) {
                case "1":
                    $status = BOOKING_FRM_CONFIRMED;
                    break;
                case "2":
                    $status = BOOKING_FRM_NOTCONFIRMED;
                    break;
                case "3":
                    $status = BOOKING_FRM_CANCELLED;
                    break;
                case "4":
                    $status = BOOKING_FRM_PAID;
                    break;
                case "5":
                    $status = BOOKING_FRM_USERCANCELLED;
                    break;
            }

            $bgClass = ($bgClass == "even" ? "odd" : "even");

            $files_table .= "<tr class=\"" . $bgClass . "\">";
            $files_table .= "";
            $files_table .= "<td height=\"24\">" . $delete . "</td>";
            $files_table .= "<td>" . $rr["name"] . "</td>";
            $files_table .= "<td>" . $rr["qty"] . "</td>";
            $files_table .= "<td>" . $serviceName . "</td>";
            $files_table .= "<td>" . $eventName . "</td>";
            $files_table .= "<td>" . $time . "</td>";
            //$files_table .= "<td>".$rr["email"]."</td>";
            //$files_table .= "<td>".($bookDateFrom!=$bookDateTo?"from :$bookDateFrom<br>to:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$bookDateTo":$bookDateFrom)."</td>";
            $files_table .= "<td>" . $bookingDates . "</td>";
            $files_table .= "<td>" . $status . "</td>";

            $files_table .= "<td>" . $paymentLink . "</td></tr>";
        } // end of all files from db query (end of while loop)

        //show button to complete file deletion if proper permissions.

        //$files_table .="<tr><td height=\"32\" colspan=\"7\"  align='right'><input name=\"delete_files\" type=\"submit\" value=\"Update Statuses\"  /></td></tr>";


    } else {
        //0 files found in database. ( end of IF mysql_num_rows > 0 )
        $files_table .= "<tr><td colspan=\"7\">" . MNG_0FOUND . "</td></tr>";
    }
}
###################################################################################################################################################
?>
    <?php include "includes/header.php"; ?>
    <div id="content">
        <h1><?php echo MNG_RESERFOR; ?><?php echo $email ?></h1>

        <?php if (!empty($email) && $uid == $uidOr) { ?>

            <strong><?php echo $msg2; ?></strong>
            <form enctype="multipart/form-data" action="bs-events-add.php" method="post" name="ff2">
                <input type="hidden" value="yes" name="attendees_edit" />
                <input value="<?php echo $id; ?>" name="id" type="hidden" />
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="topRow">
                        <td width="3%" height="30" align="center">&nbsp;</td>
                        <td width="14%" align="left"><strong><?php echo TBL_NAME; ?></strong></td>
                        <td width="5%" align="left"><strong><?php echo TBL_QTY; ?></strong></td>
                        <td width="15%" align="left"><strong><?php echo TBL_SERVICE; ?></strong></td>
                        <td width="15%" align="left"><strong><?php echo TBL_EVENT; ?></strong></td>
                        <td width="18%" align="left"><strong><?php echo TBL_TIME; ?></strong></td>
                        <td width="15%" align="left"><strong><?php echo TBL_DATE; ?></strong></td>
                        <td width="31%" align="left"><strong><?php echo TBL_MNG; ?></strong></td>

                        <td width="4%" height="30" align="center">&nbsp;</td>
                    </tr>
                    <?php echo $files_table; ?>
                </table>
            </form>
        <?php } else { ?>
            <h2><?php echo NO_ACCESS; ?></h2>
        <?php } ?>

        <?php include "includes/footer.php"; ?>