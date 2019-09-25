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

require_once("includes/config.php"); //Load the configurations

bw_do_action("bw_load");
##################################################################################
#  	1. GET ALL VARIABLES
$name = (!empty($_POST["name"])) ? strip_tags(str_replace("'", "`", $_POST["name"])) : '';
$phone = (!empty($_POST["phone"])) ? strip_tags(str_replace("'", "`", $_POST["phone"])) : '';
$email = (!empty($_POST["email"])) ? strip_tags(str_replace("'", "`", $_POST["email"])) : '';
$email1 = (!empty($_POST["email1"])) ? strip_tags(str_replace("'", "`", $_POST["email1"])) : '';
$comments = (!empty($_POST["comments"])) ? strip_tags(str_replace("'", "`", $_POST["comments"])) : '';
$date = (!empty($_POST["date"])) ? strip_tags(str_replace("'", "`", $_POST["date"])) : '';
$eventID = (!empty($_POST["eventID"])) ? $_POST["eventID"] : '';
$captcha_sum = (!empty($_POST["captcha_sum"])) ? strip_tags(str_replace("'", "`", $_POST["captcha_sum"])) : '';
$captcha = (!empty($_POST["captcha"])) ? strip_tags(str_replace("'", "`", $_POST["captcha"])) : '';
$qty = (!empty($_REQUEST["qty_" . $eventID])) ? strip_tags(str_replace("'", "`", $_REQUEST["qty_" . $eventID])) : '1';
$serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();;
$ref = (!empty($_REQUEST["ref"])) ? strip_tags(str_replace("'", "`", $_REQUEST["ref"])) : 'all';
$couponCode = (!empty($_POST["couponCode"])) ? strip_tags(str_replace("'", "`", $_POST["couponCode"])) : '';
$referrer = (!empty($_REQUEST["referrer"])) ? strip_tags(str_replace("'", "`", $_REQUEST["referrer"])) : '';

$msg = $infoForBooking = "";

$eventSummery = '';

$eventInfo = getEventInfo($eventID);

// captcha check
if (empty($captcha_sum) || empty($captcha) || md5($captcha) != $captcha_sum || !empty($email1)) {
    if ($ref == "one") {
        header("Location: event.php?date=" . $date . "&lb2=yes&serviceID={$serviceID}&name=" . urlencode($name) . "&phone=" . urlencode($phone) . "&email=" . urlencode($email) . "&qty_{$eventID}=" . urlencode($qty) . "&comments=" . urlencode($comments) . "&eventID={$eventID}&couponCode=" . urlencode($couponCode));
    } else {
        if (getOption('use_popup') && $referrer != 'calendar') {
            header("Location: index.php?eventID=" . $eventID . "&lb2=yes&serviceID={$serviceID}&name=" . urlencode($name) . "&phone=" . urlencode($phone) . "&email=" . urlencode($email) . "&qty_{$eventID}=" . urlencode($qty) . "&comments=" . urlencode($comments) . "&selEvent={$eventID}" . "&date={$date}&couponCode=" . urlencode($couponCode));
        } else {
            header("Location: event-booking.php?eventID=" . $eventID . "&lb2=yes&serviceID={$serviceID}&name=" . urlencode($name) . "&phone=" . urlencode($phone) . "&email=" . urlencode($email) . "&qty_{$eventID}=" . urlencode($qty) . "&comments=" . urlencode($comments) . "&selEvent={$eventID}" . "&date={$date}&couponCode=" . urlencode($couponCode) . ($referrer == 'calendar' ? "&referrer=calendar" : ""));
        }
    }
    exit();
}


if (!empty($name) && !empty($phone) && !empty($email) && !empty($eventID)) {
    if (!preg_match("(^[-\w\.]+@([-a-z0-9]+\.)+[a-z]{2,4}$)i", $email)) {
        addMessage(BEP_10, "error");
    } elseif ($date < date("Y-m-d")) {
        addMessage('Error', "error");
    } else {

        ##################################################################################
        #  	3. PREPARE BOOKING DATE/TIME  
        # 	CREATE ORDER
        $status = $eventInfo[5] == 'invoice' && $eventInfo['payment_required'] == "1" ? 1 : 2;

        $avilability = $eventInfo["recurring"] == 1 ? getSpotsLeftForEvent($eventID, $date) : getSpotsLeftForEvent($eventID);

        if ($avilability >= $qty) {
            if (!empty($couponCode)) {
                $couponData = checkCoupon($couponCode, $serviceID);
                if ($couponData['responce']) {
                    $couponValue = $couponData['value'];
                    $couponType = $couponData['type'];
                } else {
                    $msg = "<div class='error_msg'>" . $couponData['message'] . "</div>";
                    $couponCode = '';
                }
            }

            $q = "INSERT INTO bs_reservations (serviceID,dateCreated, name, email, phone, comments,status,eventID, qty,date,coupon)
                VALUES ('" . $serviceID . "','" . DATETIME . "','" . $name . "','" . $email . "','" . $phone . "','" . $comments . "','" . $status . "','" . $eventID . "','" . $qty . "','{$date}','{$couponCode}')";
            $res = $mysqli->query($q) or die("error!");
            $orderID = $mysqli->insert_id;

            if (!empty($orderID) && !empty($eventID)) {
                $serviceName = getService($serviceID, 'name');
                //get customer name and email
                $custInf = getInfoByReservID($orderID);
                //get event information for email notification
                $eventInf = getEventInfo($eventID);

                $uid = md5($custInf[1] . "FtTtffT");
                $linkCancelReservation = "<a href=\"http://" . $_SERVER['SERVER_NAME'] . $baseDir . "manageReservation.php?email=" . urlencode($custInf[1]) . "&uid=" . $uid . "\">link</a>";

                $price_per_spot = $eventInf[4];
                $paymentBookingIngo = get_payment_info($orderID);

                $subject = BEP_5;
                $eventStarts = getEventStartEndDate($eventID, $date);
                $eventStartsArray = getEventStartEndDate($eventID, $date, "array");

                $eventURL = "http://{$_SERVER['SERVER_NAME']}" . $baseDir . "event-booking.php?eventID={$eventInf['id']}&serviceID={$eventInf['serviceID']}&date=" .  _date($eventStartsArray['from']);

                $_dateFrom = dateToUTC($eventStartsArray['from']);
                $_dateTo = dateToUTC($eventStartsArray['to']);

                $googleLinkData = array(
                    "action" => "TEMPLATE",
                    "text" =>  urlencode($eventInf[0]),
                    "dates" => date("Ymd", strtotime($_dateFrom)) . "T" . date("His", strtotime($_dateFrom)) . "Z/" . date("Ymd", strtotime($_dateTo)) . "T" . date("His", strtotime($_dateTo)) . "Z",
                    "sprop" => urlencode("website:{$eventURL}"),
                    "details" => $eventInf[1],
                    "location" => urlencode($eventInf['location'])
                );
                $googleLink = "http://www.google.com/calendar/event?" . http_build_query($googleLinkData);
                $data = array(
                    "{%name%}" => $name,
                    "{%email%}" => $email,
                    "{%phone%}" => $phone,
                    "{%comments%}" => $comments,
                    "{%service%}" => $serviceName,
                    "{%eventName%}" => $eventInf[0],
                    "{%eventDate%}" => $eventStarts,
                    "{%eventDescr%}" => $eventInf[1],

                    "{%eventLocation%}" => $eventInf['location'],
                    "{%eventMapLink%}" => $eventInf['map_link'],

                    "{%qty%}" => $qty,
                    "{%status%}" => $status == 1 ? BOOKING_FRM_CONFIRMED : BOOKING_FRM_NOTCONFIRMED,
                    "{%link%}" => $linkCancelReservation,
                    "{%currencyB%}" => getOption('currency_position') == 'b' ? getOption('currency') : "",
                    "{%currencyA%}" => getOption('currency_position') == 'a' ? getOption('currency') : "",
                    "{%tax%}" => number_format($paymentBookingIngo['tax'], 2),
                    "{%subtotal%}" => number_format($paymentBookingIngo['subAmount'], 2),
                    "{%_subtotal%}" => number_format($paymentBookingIngo['_subAmount'], 2),
                    "discount" => $paymentBookingIngo['discount'],
                    "{%coupon%}" => $couponCode,
                    "{%total%}" => number_format($paymentBookingIngo['amount'], 2),
                    "{%taxRate%}" => $paymentBookingIngo['taxRate'],
                    "_payment" => ($price_per_spot != 0 ? 1 : 0),
                    "_taxable" => !empty($paymentBookingIngo['tax']) ? 1 : 0,
                    "{%collect%}" => $eventInfo['payment_method'] == 'invoice' && $eventInfo['payment_required'] == "1" ? BEP_6 : BEP_7,
                    "{%google_link%}" => $googleLink,
                    "deposit" => $paymentBookingIngo['deposit'],
                    "{%totalToPay%}" => number_format($paymentBookingIngo['amountToPay'], 2)
                );

                $files = array();
                include_once './includes/export/event_ical.php';
                $files["addToCalendar.ics"] = $file;

                $files = bw_apply_filter('event_processing_mail_files', $files, $orderID);

                sendMailFile($custInf[1], $subject, "eventBookingConfirmationCustomer.php", $serviceID, $data, $files);
                //sendMail($custInf[1], $subject, "eventBookingConfirmationCustomer.php",$serviceID, $data);
                ##################################################################################
                #  	4. SEND NOTICE TO ADMIN AND CUSTOMER
                //send email to admin
                $adminMail = getAdminMail();
                $subject = $eventInfo[5] ? BEP_8 . " (#" . $orderID . ")!" : BEP_9 . " (#" . $orderID . ")!";
                sendMail($adminMail, $subject, "eventBookingConfirmationAdmin.php", $serviceID, $data);
                $sent = true;


                $orderSummery = getOrderSummery($orderID, $date);

                if ($eventInf['payment_required'] == "1" && !empty($eventInf['payment_method']) && $paymentBookingIngo['amount'] > 0) {
                    if (!empty($orderID) && !empty($eventInf['payment_method'])) {
                        $infoForBooking = do_payment($orderID, $eventInf['payment_method'], null, $referrer);
                    } else { }
                } else {
                    if (getService($serviceID, "autoconfirm") && $paymentBookingIngo['amount'] == 0) {
                        $infoForBooking = BEP_11;
                        $subject = "Event booking confirmed!";
                        $sql = "UPDATE bs_reservations SET status = 1 WHERE id='{$orderID}'";
                        $res = $mysqli->query($sql) or die("error autoconfirm booking!");
                        $data = array(
                            "{%name%}" => $custInf[0],
                            "{%service%}" => $serviceName,
                            "{%eventName%}" => $eventInf[0],
                            "{%eventDate%}" => $eventStarts,
                            "{%eventDescr%}" => $eventInf[1],
                            "{%qty%}" => $qty,
                            "{%status%}" => BOOKING_FRM_CONFIRMED
                        );
                        sendMail($custInf[1], $subject, "eventBookingConfirmationStatus.php", $serviceID, $data);
                    }
                }
            }
        } else {
            addMessage(BEP_12, "error");
        }
    }
} else {
    //throw error
    addMessage(BEP_13, "error");
}

?>
<?php include "includes/header.php" ?>
<script type="text/javascript">
    $(function() {
        if (($.browser.msie) && (($.browser.version == '7.0') || ($.browser.version == '8.0'))) {
            $("#back").show();
        }
    })
</script>
<div id="index">
    <h1><?php echo BEP_14; ?></h1>

    <?php getMessages(); ?>
    <?php echo ($orderSummery) ?>
    <?php echo $infoForBooking; ?>

    <br><br>
    <?php
    if (!empty($_SESSION['site']) && !getOption('use_popup')) {
        echo "<a href='{$_SESSION['site']}' id='back'>Back to calendar</a>";
    } else {
        echo "<br/><br/><a href=\"http://" . MAIN_URL . "index.php?serviceID={$serviceID}\">" . BEP_15 . "</a>";
    }
    ?>

    <?php include "includes/footer.php" ?>