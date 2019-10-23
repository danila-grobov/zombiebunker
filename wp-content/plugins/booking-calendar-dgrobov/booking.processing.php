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
function validate_phone_number($phone)
{
    if (preg_match(
        '/((?:\+|00)[17](?: |\-)?|(?:\+|00)[1-9]\d{0,2}(?: |\-)?|(?:\+|00)1\-\d{3}(?: |\-)?)?(0\d|\([0-9]{3}\)|[1-9]{0,3})(?:((?: |\-)[0-9]{2}){4}|((?:[0-9]{2}){4})|((?: |\-)[0-9]{3}(?: |\-)[0-9]{4})|([0-9]{7}))/',
        $phone
    ))
        return true;
    return false;
}
bw_do_action("bw_load");
##################################################################################
#  	1. GET ALL VARIABLES
$name = (!empty($_POST["name"])) ? strip_tags(str_replace("'", "`", $_POST["name"])) : '';
$agrees = ($_POST["agrees"] === 'true') ? true : false;
if ($_POST["young"] === 'true') {
    $young = 1;
}
if ($_POST["young"] === 'false') {
    $young = 0;
}
if ($_POST["young"] === 'null') {
    $young = 'null';
}
$lng = $_POST["lng"];
$phone = (!empty($_POST["phone"])) ? strip_tags(str_replace("'", "`", $_POST["phone"])) : '';
$email = (!empty($_POST["email"])) ? strip_tags(str_replace("'", "`", $_POST["email"])) : '';

$email = str_replace(" ", "", $email);
$phone = str_replace(" ", "", $phone);
$email1 = (!empty($_POST["email1"])) ? strip_tags(str_replace("'", "`", $_POST["email1"])) : '';
$comments = (!empty($_POST["comments"])) ? strip_tags(str_replace("'", "`", $_POST["comments"])) : '';
$date = (!empty($_POST["date"])) ? strip_tags(str_replace("'", "`", $_POST["date"])) : '';
$interval = (!empty($_POST["interval"])) ? strip_tags(str_replace("'", "`", $_POST["interval"])) : '';
$time = (!empty($_POST["time"])) ? $_POST["time"] : '';
$captcha_sum = (!empty($_POST["captcha_sum"])) ? strip_tags(str_replace("'", "`", $_POST["captcha_sum"])) : '';
$captcha = (!empty($_POST["captcha"])) ? strip_tags(str_replace("'", "`", $_POST["captcha"])) : '';
$serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();
$qty = (!empty($_REQUEST["qty"])) ? intval($_REQUEST["qty"]) : 1;
$couponCode = (!empty($_POST["couponCode"])) ? strip_tags(str_replace("'", "`", $_POST["couponCode"])) : '';
$referrer = (!empty($_REQUEST["referrer"])) ? strip_tags(str_replace("'", "`", $_REQUEST["referrer"])) : '';

$error = checkQtyForTimeBooking($serviceID, $time, $date, $interval, $qty);
if (!$error) {
    if (
        !empty($name) && validate_phone_number($phone)
        && filter_var($email, FILTER_VALIDATE_EMAIL)
        && $agrees === true
        && strlen($comments) < 10000
        && $young !== "null"
    ) {
        // if (!preg_match("(^[-\w\.]+@([-a-z0-9]+\.)+[a-z]{2,4}$)i", $email)) {
        //     $msg = "<div class='error_msg'>" . BEP_10 . "</div>";
        // } else {
        if (!empty($couponCode)) {
            $couponData = checkCoupon($couponCode, $serviceID);
            if ($couponData['responce']) {
                $couponValue = $couponData['value'];
                $couponType = $couponData['type'];
            } else {
                $couponCode = '';
            }
        }

        ##################################################################################
        #  	3. PREPARE BOOKING DATE/TIME
        # CREATE ORDER

        $price_per_spot = getPricePerSpot($serviceID);
        $status = getServiceSettings($serviceID, 'payment_method')  == 'invoice' ? 1 : 2;
        $q = "INSERT INTO bs_reservations (dateCreated, name, email, phone, comments,status, `interval`,`serviceID`,`qty`,`coupon`,`young`)
                                    VALUES ('" . DATETIME . "','" . $name . "','" . $email . "','" . $phone . "','" . $comments . "','" . $status . "','" . $interval . "','" . $serviceID . "','" . $qty . "','" . $couponCode . "'," . $young . ")";

        $res = $mysqli->query($q) or die("error! 001:" . $mysqli->error());

        $orderID = $mysqli->insert_id;
        $serviceName = getService($serviceID, 'name');
        if (!empty($orderID)) {
            $tempVar = "";
            $bookingData = array();
            $spots = 0;
            foreach ($time as $k => $v) {
                $dateFrom = date("Y-m-d H:i:s", strtotime($date . " +" . $v . " minutes"));
                $dateTo = date("Y-m-d H:i:s", strtotime($dateFrom . " +" . $interval . " minutes"));
                $q = "INSERT INTO bs_reservations_items (reservationID,dateCreated,reserveDateFrom,reserveDateTo,qty)
                          VALUES ('" . $orderID . "','" . DATETIME . "','" . $dateFrom . "','" . $dateTo . "','" . $qty . "')";
                $res = $mysqli->query($q) or die("error! 002");

                //needed for message
                $tempVar .= "<tr><td>" . getDateFormat($date) . "</td><td>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($dateFrom)) . "</td><td>" . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($dateTo)) . "</td><td>" . $qty . "</td></tr>";
                $bookingData[] = array(
                    'date' => getDateFormat($date),
                    'timeFrom' => date((getTimeMode()) ? "g:i a" : "H:i", strtotime($dateFrom)),
                    'timeTo' => date((getTimeMode()) ? "g:i a" : "H:i", strtotime($dateTo)),
                    'qty' => $qty,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo
                );
                $spots++;
            }




            $paymentBookingIngo = get_payment_info($orderID);
            if ($price_per_spot == 0 || $paymentBookingIngo['amount'] == 0) {
                $infoForBooking = BEP_11;
                $subject = BEP_161 . " (#" . $orderID . ")!";
            } else {
                $subject = BEP_16 . " (#" . $orderID . ")!";
                $infoForBooking = do_payment($orderID, getServiceSettings($serviceID, "payment_method"), null, $referrer);
            }

            //bw_dump($paymentBookingIngo);

            $uid = md5($email . "FtTtffT");
            $linkCancelReservation = "<a href=\"http://" . $_SERVER['SERVER_NAME'] . $baseDir . "manageReservation.php?email=" . urlencode($email) . "&uid=" . $uid . "\">link</a>";
            ##################################################################################
            #  	4. SEND NOTICE TO ADMIN AND CUSTOMER
            //send email to admin



            $adminMail = getAdminMail();

            $_startDate = dateToUTC($bookingData[0]['dateFrom']);
            $_endDate = end($bookingData);
            $_endDate = dateToUTC($_endDateIcal);





            $eventURL = "http://{$_SERVER['SERVER_NAME']}" . $baseDir . "booking.php?serviceID={$serviceID}&date=" .  _date($_endDate);
            $googleLinkData = array(
                "action" => "TEMPLATE",
                "text" =>  $serviceName,
                "dates" => date("Ymd", strtotime($_startDate)) . "T" . date("His", strtotime($_startDate)) . "Z/" . date("Ymd", strtotime($_endDate)) . "T" . date("His", strtotime($_endDate)) . "Z",
                "sprop" => urlencode("website:{$eventURL}"),
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
                "{%status%}" => $status == 1 ? BOOKING_FRM_CONFIRMED : BOOKING_FRM_NOTCONFIRMED,
                "_info" => $bookingData,
                "{%collect%}" => ($status == 1 && $price_per_spot > 0 ? " (Please collect payment from customer)<br/>" : ""),
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
                "{%linkCancelReservation%}" => $linkCancelReservation,
                "{%google_link%}" => $googleLink,
                "deposit" => $paymentBookingIngo['deposit'],
                "{%totalToPay%}" => number_format($paymentBookingIngo['amountToPay'], 2)
            );
            echo json_encode(["success" => true]);
            sendMail($adminMail, $subject, "timeBookingConfirmationAdmin.php", $serviceID, $data);
            //send email to customer



            include_once './includes/export/booking_ical.php';
            sendMailFile($email, "Zombie Bunker - rezervacija/reservation", "timeBookingConfirmationCustomer.php", $serviceID, $data, $ical_file);

            // issiusti sms, vartotojui:

            // $zinute = urlencode("Informuojame, kad Jusu registracija patvirtinta. Zombie Bunker: +37068449944");
            // $sms = "http://smsplus1.routesms.com:8080/bulksms/bulksms?username=supersmslt&password=k7bd7y&type=0&dlr=1&destination=" . $phone . "&source=Zombie Bunker&message=" . $zinute;
            //$ch = curl_init($sms);
            //curl_exec($ch);



            //sendMail($email, $subject, "timeBookingConfirmationCustomer.php", $serviceID, $data,$ical_file);
            //header("Location: thank-you.php");

            if (($price_per_spot == 0 && $status != 1) || $paymentBookingIngo['amount'] == 0) {
                if (getService($serviceID, "autoconfirm") && $paymentBookingIngo['amount'] == 0) {
                    $subject = EMAIL_SUBJ_CONFIRMED;
                    $data = array(
                        "{%name%}" => $name,
                        "{%status%}" => BOOKING_FRM_CONFIRMED,
                        "_info" => $bookingData,

                    );
                    sendMail($email, $subject, "timeBookingConfirmationStatus.php", $serviceID, $data);

                    $sql = "UPDATE bs_reservations SET status = 1 WHERE id='{$orderID}'";
                    $res = $mysqli->query($sql) or die("error autoconfirm booking!");
                }
            }
        }
    } else {
        $errors = [];
        if (empty($name)) {
            $errors +=  array("name" => $lng == "LT" ? "Įveskite savo vardą" : "Please enter your name");
        }
        if ($young === "null") {
            $errors +=  array("young" => $lng == "LT" ? "Turite pasirinkti dalyvių amžių" : "Please select the age of the attendants");
        }
        if (!validate_phone_number($phone)) {
            $errors += array("phone" => $lng == "LT" ? "Įveskite taisyklinga telefono numerį ( +xxx xxx xxxxx )" : "Please enter a valid phone number ( +xxx xxx xxxxx )");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors += array("email" => $lng == "LT" ? "Įveskite taisyklinga elektroninio pašto adresą ( xxx@xxx.xxx )" : "Please enter a valid email address ( xxx@xxx.xxx )");
        }
        if ($agrees === false) {
            $errors += array("agrees" => $lng == "LT" ? "Norėdami naudotis mūsų paslaugomis turite sutikti su duomenų tvarkymo taisyklėmis." : "In order to use our services you have to accept our Privacy Policy.");
        }
        if (strlen($comments) >= 10000) {
            $errors += array("alert" => $lng == "LT" ? "Deja, bet jūsų komentaras yra per ilgas" : "Your comment is too long");
        }
        echo json_encode($errors);
    }
} else {
    echo json_encode(["alert" => $lng == "LT" ? "Deja, bet ši sesija jau užimta" : "This session has already been booked"]);
    $paypal_form = "";
}
