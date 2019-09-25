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

$name = (!empty($_REQUEST["name"])) ? strip_tags(str_replace("'", "`", $_REQUEST["name"])) : '';
$phone = (!empty($_REQUEST["phone"])) ? strip_tags(str_replace("'", "`", $_REQUEST["phone"])) : '';
$email = (!empty($_REQUEST["email"])) ? strip_tags(str_replace("'", "`", $_REQUEST["email"])) : '';
$comments = (!empty($_REQUEST["comments"])) ? strip_tags(str_replace("'", "`", $_REQUEST["comments"])) : '';
$interval = (!empty($_POST["interval"])) ? strip_tags(str_replace("'", "`", $_POST["interval"])) : '';
$date = (!empty($_REQUEST["date"])) ? strip_tags(str_replace("'", "`", $_REQUEST["date"])) : '';
$processNOJSbooking = (!empty($_POST["processNOJSbooking"])) ? strip_tags(str_replace("'", "`", $_POST["processNOJSbooking"])) : '';
$captcha_sum = (!empty($_POST["captcha_sum"])) ? strip_tags(str_replace("'", "`", $_POST["captcha_sum"])) : '';
$captcha = (!empty($_POST["captcha"])) ? strip_tags(str_replace("'", "`", $_POST["captcha"])) : '';

$msg = "";


$maximumBookings = getMaxBooking();
if ($maximumBookings != 0 && $maximumBookings != 99) {
	$maximumBookingsText = ", " . $maximumBookings . " hour(s) maximum.";
} else {
	$maximumBookingsText = "";
}

$minimumBookings = getMinBooking();
if ($minimumBookings != 0 && $minimumBookings != 99) {
	$minimumBookingsText = $minimumBookings . " hour(s) minimum";
} else {
	$minimumBookingsText = "";
}

if (!empty($processNOJSbooking) && $processNOJSbooking == "yes") {
	//check if not more than allowed in settings bookings selected.
	if (!empty($captcha_sum) && !empty($captcha) && md5($captcha) == $captcha_sum) {
		$time = (!empty($_POST["time"])) ? $_POST["time"] : '';
		$count_selected_bookings = count($time);
		if ($count_selected_bookings <= $maximumBookings && $count_selected_bookings >= $minimumBookings) {
			//everything ok
			//check if not empty name/phone/email
			if (!empty($name) && !empty($phone) && !empty($email) && !empty($time)) {
				if (!preg_match("(^[-\w\.]+@([-a-z0-9]+\.)+[a-z]{2,4}$)i", $email)) {
					$msg = "<div class='error_msg'>Invalid email address. Please check your input.</div>";
				} else {
					##################################################################################
					#  	3. PREPARE BOOKING DATE/TIME  
					# CREATE ORDER
					$q = "INSERT INTO bs_reservations (dateCreated, name, email, phone, comments,status, `interval`) VALUES (NOW(),'" . $name . "','" . $email . "','" . $phone . "','" . $comments . "','2','" . $interval . "')";
					$res = $mysqli->query($q) or die("error! 001:" . $mysqli->error());
					$orderID = $mysqli->insert_id;

					if (!empty($orderID)) {
						$tempVar = "";
						foreach ($time as $k => $v) {
							$dateFrom = date("Y-m-d H:i:s", strtotime($date . " +" . $v . " minutes"));
							$dateTo = date("Y-m-d H:i:s", strtotime($dateFrom . " +" . $interval . " minutes"));
							$q = "INSERT INTO bs_reservations_items (reservationID,dateCreated,reserveDateFrom,reserveDateTo) VALUES ('" . $orderID . "',NOW(),'" . $dateFrom . "','" . $dateTo . "')";
							$res = $mysqli->query($q) or die("error! 002");

							//needed for message
							$tempVar .= "<tr><td>" . date("d F Y", strtotime($date)) . "</td><td>" . date("g:i a", strtotime($dateFrom)) . "</td><td>" . date("g:i a", strtotime($dateTo)) . "</td></tr>";
						}

						##################################################################################
						#  	4. SEND NOTICE TO ADMIN AND CUSTOMER
						//send email to admin
						$headers  = "MIME-Version: 1.0\n";
						$headers .= "Content-type: text/html; charset=utf-8\n";
						$headers .= "From: 'Your Booking System' <noreply@" . $_SERVER['HTTP_HOST'] . "> \n";
						$subject = "New un-confirmed booking (#" . $orderID . ")!";
						$message = "Dear administrator,<br /> <br /> New booking has been successfully placed on your website. <br />";
						$message .= "Name: " . $name;
						$message .= "<br />Email: " . $email;
						$message .= "<br />Phone: " . $phone;
						$message .= "<br />Comments: " . $comments;
						$message .= "<br />Booking Information: <br />";
						$message .= "<table cellspacing=0 cellpadding=4 border=0>";
						$message .= "<tr><td>Date</td><td>Time From</td><td>Time To</td></tr>";
						$message .= $tempVar;
						$message .= "</table>";
						$message .= "<br />Reservation Status: Not Confirmed<br />";
						$message .= "<br /><br />Kind Regards, <br /> " . $_SERVER['HTTP_HOST'] . " Website";
						$adminMail = getAdminMail();
						mail($adminMail, $subject, $message, $headers);

						//send email to customer
						$headers  = "MIME-Version: 1.0\n";
						$headers .= "Content-type: text/html; charset=utf-8\n";
						$headers .= "From: '" . $_SERVER['HTTP_HOST'] . " Booking System' <info@" . $_SERVER['HTTP_HOST'] . "> \n";
						$subject = "Booking Placed (Not Cofirmed)";
						$message = "Dear " . $name . ",<br /> <br /> Thank you for your booking. Here is your booking information: <br />";
						$message .= "<table cellspacing=0 cellpadding=4 border=0>";
						$message .= "<tr><td>Date</td><td>Time From</td><td>Time To</td></tr>";
						$message .= $tempVar;
						$message .= "</table>";
						$message .= "<br />Reservation Status: Not Confirmed<br/> One of our representatives will contact you as soon as possible.";
						$message .= "<br /><br />Kind Regards, <br /> " . $_SERVER['HTTP_HOST'] . " Team";
						mail($email, $subject, $message, $headers);

						header("Location: thank-you.php");
					}
				}
			} else {
				//throw error
				$msg = "<div class='error_msg'>Following fields required: Name, Email, Phone, Booking time. Please double check your input. </div>";
			}
		} else {
			//throw error
			if ($maximumBookings != 99) {
				$msg = "<div class='error_msg'>Minimum booking time " . $minimumBookings . " hour(s), maximum " . $maximumBookings . " hour(s). Please adjust your booking!</div>";
			} else {
				$msg = "<div class='error_msg'>Minimum booking time " . $minimumBookings . " hour(s). Please adjust your booking!</div>";
			}
		}
	} else {
		//throw error
		$msg = "<div class='error_msg'>Captcha error! Please try again</div>";
	}
}

####################################### PREPARE AVAILABILITY TABLE ##############################################
$int = getMinBooking(); //interval in minutes.
$int = $int * 60;
$reservedArray = array();
$seconds = 0;
$availability = "";
##########################################################################################################################
#	GET RESERVED TIME / RESERVED ARRAY
//ADMIN RESERVED TIME
$query = "SELECT * FROM bs_reserved_time WHERE reserveDateFrom LIKE '" . $date . "%' ORDER BY reserveDateFrom ASC ";
$result = $mysqli->query($query);
if ($result->num_rows > 0) {
	while ($rr = $result->fetch_assoc()) {
		//IF ADMIN SELECTED FROM 12:00 to 18:00 (more than 1 interval time between 2 spots)
		if (date("Y-m-d H:i", strtotime($rr["reserveDateFrom"] . " +" . $int . " minutes")) != $$rr["reserveDateTo"]) {
			for ($a = date("Y-m-d H:i", strtotime($rr["reserveDateFrom"])); $a < date("Y-m-d H:i", strtotime($rr["reserveDateTo"])); $a = date("Y-m-d H:i", strtotime($a . " +" . $int . " minutes"))) {
				$reservedArray[date("Y-m-d", strtotime($a))][] = date("H:i", strtotime($a));
			}
		} else {
			$reservedArray[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][] = date("H:i", strtotime($rr["reserveDateFrom"]));
		}
		# TODO - question: what if i had intervals was 30m, and we had bookings but then time passes and we changed interval to be 1h. What will be displayed.
		# on front - we can block past dates, however If somebody booked something in future, and we suddenly changed the interval time - for now we can 
		# simply state in admin that if you changed it - you have to manually advice customers and manually change their bookings (1 by 1)
	}
}

//ACTUAL CUSTOMER BOOKINGS
$query = "SELECT bs_reservations_items.* FROM `bs_reservations_items` INNER JOIN bs_reservations on bs_reservations_items.reservationID = bs_reservations.id WHERE bs_reservations.status='1' AND bs_reservations_items.reserveDateFrom LIKE '" . $date . "%' ORDER BY bs_reservations_items.reserveDateFrom ASC ";
$result = $mysqli->query($query);
if ($result->num_rows > 0) {
	while ($rr = $result->fetch_assoc()) {
		$reservedArray[date("Y-m-d", strtotime($rr["reserveDateFrom"]))][] = date("H:i", strtotime($rr["reserveDateFrom"]));
	}
}
//var_dump($reservedArray);
##########################################################################################################################

##########################################################################################################################
# PREPARE AVAILABILITY ARRAY 
$availabilityArr = array();
$tt = getStartEndTime(date("w", strtotime($date))); //week day of selected day.
//$startTime = $tt[0]; 
//$endTime = $tt[1]; // LEFTOVERS FROM V2
#TODO - check if "to" is < "from" = means that "to" is +1 day.
$st = date("Y-m-d H:i", strtotime($date . " +" . $tt[2] . " minutes"));
$et = date("Y-m-d H:i", strtotime($date . " +" . $tt[3] . " minutes"));
$a = $st;
$n = 0; //layout counter
$b = date("Y-m-d H:i", strtotime($a . " +" . $int . " minutes")); //default value for B is start time.
for ($a = $st; $b <= $et; $b = date("Y-m-d H:i", strtotime($a . " +" . $int . " minutes"))) {
	//echo "a: ".$a." // "."b: ".$b."<br />";
	$availabilityArr[date("Y-m-d", strtotime($a))][] = date("H:i", strtotime($a));
	$a = $b;
	$n++;
}

$has_availability = false;
$availability .= "<table width=\"400\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\"><tr><td valign='top'>";

$n = ($n - ($n % 2)) / 2;
$count = 0;
foreach ($availabilityArr as $k => $v) { //$v= date  (  2010-10-05 )
	foreach ($v as $kk => $vv) { //$vv = time slot in above date 
		if ($count == $n) {
			$availability .= "</td><td align='left' valign='top'>";
			$count = 0;
		}
		if (!isset($reservedArray[$k]) || !in_array($vv, $reservedArray[$k])) {
			//$availability .=$b.":00 ".($b<12?"am":"pm")." - ".($b+1).":00 ".($b+1<12?"am":"pm")."- <input type=\"checkbox\" value=\"".$b."\" name=\"time[]\" ><br>";  V2
			$msm = ((int) substr($vv, 0, 2)) * 60; //minutes since miodnight of current day.
			$availability .= date("g:i a", strtotime($vv)) . " - " . date("g:i a", strtotime($vv . " +" . $int . " minutes")) . " - <input type=\"checkbox\" value=\"" . $msm . "\" name=\"time[]\" ><br />";
			$has_availability = true;
		} else {
			//$availability .= $b.":00 ".($b<12?"am":"pm")." - ".($b+1).":00 ".($b+1<12?"am":"pm")." - Booked.<br>"; V2
			$availability .= date("g:i a", strtotime($vv)) . " - " . date("g:i a", strtotime($vv . " +" . $int . " minutes")) . " - Booked.<br />";
		}
		$count++;
	}
}


$availability .= "</td></tr></table>";
##########################################################################################################################


if (!$has_availability) {
	$availability .= "<div class='error_msg'>THIS DAY IS FULLY BOOKED. PLEASE <a href='index.php'>SELECT</a> ANOTHER DATE</div>";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Booking System v3.1 - NOJS</title>
	<link rel="stylesheet" href="css/bs-admin.css" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<link type="text/css" media="screen" rel="stylesheet" href="css/colorbox.css" />
	<script type="text/javascript" src="js/jquery.colorbox.js"></script>
</head>

<body>
	<noscript>
		<div class="js_error">Please enable JavaScript or upgrade to better <a href="http://www.mozilla.com/en-US/firefox/upgrade.html" target="_blank">browser</a></div>
	</noscript>
	<?php echo $msg; ?>
	<div class="internal_booking_form">
		<form name="ff1" enctype="multipart/form-data" method="post" action="booking-nojs.php">
			<input type="hidden" value="<?php echo $date ?>" name="date">
			<input type="hidden" value="yes" name="processNOJSbooking" />
			<input type="hidden" name="interval" value="<?php echo $int; ?>" />
			<h2>Service Reservation for <br />
				<?php echo date("d F Y", strtotime($date)) ?></h2>



			<p>Please select desired time. <?php echo $minimumBookingsText ?> <?php echo $maximumBookingsText ?></p>

			<?php echo $availability ?>

			<table width="250" border="0" align="center" cellpadding="0" cellspacing="0">

				<tr>
					<td height="30" align="right" class="align_right">Your Name*:&nbsp;</td>
					<td>
						<input type="text" name="name" id="name" value="<?php echo $name ?>" />
					</td>
				</tr>
				<tr>
					<td height="30" align="right" class="align_right">Phone*:&nbsp;</td>
					<td><input type="text" name="phone" id="phone" value="<?php echo $phone ?>" /></td>
				</tr>
				<tr>
					<td height="30" align="right" class="align_right">E-mail*:&nbsp;</td>
					<td><input type="text" name="email" id="email" value="<?php echo $email ?>" /></td>
				</tr>

				<tr>
					<td align="right" valign="top" class="align_right">Comments:&nbsp;</td>
					<td><textarea name="comments" id="comments" cols="15" rows="5"><?php echo $comments ?></textarea></td>
				</tr>

				<?php
				$num1 = rand(1, 9);
				$num2 = rand(1, 9);
				$sum = $num1 + $num2;
				?>
				<tr>
					<td height="30" align="right" class="align_right"><?php echo $num1 . " + " . $num2 . " = " ?></td>
					<td align="left">&nbsp;&nbsp;&nbsp;<input type="text" name="captcha" id="captcha" value="" style="width:30px;" onchange="checkFieldBack(this);" />
						<input type="hidden" name="captcha_sum" value="<?php echo md5($sum); ?>" />
					</td>
				</tr>

				<tr>
					<td height="15">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" align="center" class="align_center"><input type="image" src="images/reserve_btn.jpg" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>

		</form>
	</div>
</body>

</html>