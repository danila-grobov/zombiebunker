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
$dateFrom = (!empty($_POST["dateFrom"])) ? strip_tags(str_replace("'", "`", $_POST["dateFrom"])) : '';
$dateTo = (!empty($_POST["dateTo"])) ? strip_tags(str_replace("'", "`", $_POST["dateTo"])) : '';
$captcha_sum = (!empty($_POST["captcha_sum"])) ? strip_tags(str_replace("'", "`", $_POST["captcha_sum"])) : '';
$captcha = (!empty($_POST["captcha"])) ? strip_tags(str_replace("'", "`", $_POST["captcha"])) : '';
$qty = (!empty($_REQUEST["qty"])) ? strip_tags(str_replace("'", "`", $_REQUEST["qty"])) : 1;
$serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();
$couponCode = (!empty($_POST["couponCode"])) ? strip_tags(str_replace("'", "`", $_POST["couponCode"])) : '';
$referrer = (!empty($_REQUEST["referrer"])) ? strip_tags(str_replace("'", "`", $_REQUEST["referrer"])) : '';

$msg = $infoForBooking = "";

$eventSummery = '';

$serviceSettings = getServiceSettings($serviceID);

$dayPrice = getDayPrice($date, $serviceID);

// captcha check
if (empty($captcha_sum) || empty($captcha) || md5($captcha) != $captcha_sum || !empty($email1)) {

    if (getOption('use_popup') && $referrer != 'calendar') {
        header("Location: index.php?lb3=yes&serviceID={$serviceID}&name=" . urlencode($name) . "&phone=" . urlencode($phone) . "&email=" . urlencode($email) .  "&comments=" . urlencode($comments) . "&dateFrom={$dateFrom}&dateTo={$dateTo}&couponCode=" . urlencode($couponCode));
    } else {
        header("Location: booking-days.php?lb3=yes&serviceID={$serviceID}&name=" . urlencode($name) . "&phone=" . urlencode($phone) . "&email=" . urlencode($email) . "&comments=" . urlencode($comments) . "&dateFrom={$dateFrom}&dateTo={$dateTo}&couponCode=" . urlencode($couponCode) . ($referrer == 'calendar' ? "&referrer=calendar" : ""));
    }

    exit();
}


if (!empty($name) && !empty($phone) && !empty($email)) {
    if (!preg_match("(^[-\w\.]+@([-a-z0-9]+\.)+[a-z]{2,4}$)i", $email)) {
        addMessage(BEP_7, "error");
    } elseif ($dateFrom < date("Y-m-d")) {
        addMessage('Error', "error");
    } else {

        ##################################################################################
        #  	3. PREPARE BOOKING DATE/TIME  
        # 	CREATE ORDER
        $status = $serviceSettings['payment_method'] == 'invoice' ? 1 : 2;

        $avilability = checkSpotsForDayInterval($dateFrom, $dateTo, $serviceID);
        $avilabilityInfo = _checkForAvailability($dateFrom, $dateTo, $serviceID);

        if ($avilabilityInfo['res']) {
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

                $q = "INSERT INTO bs_reservations (dateCreated, name, email, phone, comments,status, `interval`,`serviceID`,`qty`,`coupon`) 
                             VALUES ('" . DATETIME . "','" . $name . "','" . $email . "','" . $phone . "','" . $comments . "','" . $status . "','" . $interval . "','" . $serviceID . "','" . $qty . "','" . $couponCode . "')";
                $res = $mysqli->query($q) or die("error! 001:" . $mysqli->error());
                $orderID = $mysqli->insert_id;


                if (!empty($orderID)) {
                    $q = "INSERT INTO bs_reservations_items (reservationID,dateCreated,reserveDateFrom,reserveDateTo,qty) 
                        VALUES ('" . $orderID . "','" . DATETIME . "','" . $dateFrom . "','" . $dateTo . "','" . $qty . "')";
                    $res = $mysqli->query($q) or die("error! 002");

                    $serviceName = getService($serviceID, 'name');
                    //get customer name and email
                    $custInf = getInfoByReservID($orderID);
                    $days = getDaysInterval($dateFrom, $dateTo);


                    $uid = md5($custInf[1] . "FtTtffT");
                    $linkCancelReservation = "<a href=\"http://" . $_SERVER['SERVER_NAME'] . $baseDir . "manageReservation.php?email=" . urlencode($custInf[1]) . "&uid=" . $uid . "\">link</a>";

                    $price_per_spot = getDayPrice($dateFrom, $serviceID);
                    $paymentBookingIngo = get_payment_info($orderID);

                    $subject = BDP_1;
                    $googleLinkData = array(
                        "action" => "TEMPLATE",
                        "text" =>  $serviceName,
                        "dates" => date("Ymd", strtotime(dateToUTC($dateFrom . " 12:00"))) . "T120000Z/" . date("Ymd", strtotime(dateToUTC($dateTo . " 12:00"))) . "T120000Z",
                        "sprop" => urlencode("website:http://{$_SERVER['name']}" . $baseDir . "booking.php?serviceID={$serviceID}&date=" .  _date($dateFrom)),
                        "details" => $serviceName,
                        "location" => ''
                    );
                    $googleLink = "http://www.google.com/calendar/event?" . http_build_query($googleLinkData);
                    $data = array(
                        "{%name%}" => $name,
                        "{%serviceName%}" => $serviceName,
                        "{%email%}" => $email,
                        "{%phone%}" => $phone,
                        "{%comments%}" => $comments,
                        "{%service%}" => $serviceName,
                        "{%orderID%}" => $orderID,
                        "{%dayDescr%}" => $serviceSettings['description'],
                        "summery" => $avilabilityInfo['info'],

                        "{%qty%}" => $qty,

                        "{%status%}" => BOOKING_FRM_NOTCONFIRMED,
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
                        "{%collect%}" => $serviceSettings['payment_method'] == 'invoice' ? BEP_6 : BEP_7,
                        "{%google_link%}" => $googleLink,
                        "deposit" => $paymentBookingIngo['deposit'],
                        "{%totalToPay%}" => number_format($paymentBookingIngo['amountToPay'], 2)
                    );
                    $bookingData[] = array(
                        'dateFrom' => "$dateFrom 12:00:00",
                        'dateTo' => "$dateTo 12:00:00"
                    );
                    include_once './includes/export/booking_ical.php';
                    sendMailFile($custInf[1], $subject, "dayBookingConfirmationCustomer.php", $serviceID, $data, $ical_file);
                    //sendMail($custInf[1], $subject, "dayBookingConfirmationCustomer.php",$serviceID, $data);
                    ##################################################################################
                    #  	4. SEND NOTICE TO ADMIN AND CUSTOMER
                    //send email to admin
                    $adminMail = getAdminMail();
                    $subject = $serviceSettings['payment_method'] == 'invoice' ? BDP_2 . " (#" . $orderID . ")!" : BDP_3 . " (#" . $orderID . ")!";
                    sendMail($adminMail, $subject, "dayBookingConfirmationAdmin.php", $serviceID, $data);
                    $sent = true;


                    $orderSummery = getOrderSummery($orderID, $date);

                    if ($paymentBookingIngo['amount'] > 0) {
                        $infoForBooking = do_payment($orderID, $serviceSettings['payment_method'], null, $referrer);
                    } else {
                        if (getService($serviceID, "autoconfirm") && $paymentBookingIngo['amount'] == 0 && $status != 1) {
                            $infoForBooking = BEP_11;
                            $subject = "Multi-Day booking confirmed!";
                            $sql = "UPDATE bs_reservations SET status = 1 WHERE id='{$orderID}'";
                            $res = $mysqli->query($sql) or die("error autoconfirm booking!");
                            $days = getDaysInterval($dateFrom, $dateTo);
                            $data = array(
                                "{%name%}" => $custInf[0],
                                "{%service%}" => $serviceName,
                                "{%dateFrom%}" => "$dateFrom 12:00:00",
                                "{%dateTo%}" => "$dateTo 12:00:00",
                                "{%days%}" => $days,
                                "{%descr%}" => $serviceSettings['description'],
                                "{%status%}" => BOOKING_FRM_CONFIRMED
                            );
                            sendMail($custInf[1], $subject, "dayBookingConfirmationStatus.php", $serviceID, $data);
                        }
                    }
                }
            } else {
                addMessage(BDP_4, "error");
            }
        } else {
            addMessage($avilabilityInfo['message'], "error");
        }
    }
} else {
    //throw error
    addMessage(BEP_6, "error");
}

$backLink = "http://" . MAIN_URL . "index.php?serviceID={$serviceID}&month=" . date("m", strtotime($dateFrom)) . "&year=" . date("Y", strtotime($dateFrom));
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
        echo "<a href='{$_SESSION['site']}' id='back' >" . BEP_15 . "</a>";
    } else {
        echo "<br/><br/><a href=\"{$backLink}\">" . BEP_15 . "</a>";
    }
    ?>

    <?php include "includes/footer.php" ?>