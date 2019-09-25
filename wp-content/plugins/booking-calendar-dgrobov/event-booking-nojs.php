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
	
	$name = (!empty($_REQUEST["name"]))?strip_tags(str_replace("'","`",$_REQUEST["name"])):'';
	$phone = (!empty($_REQUEST["phone"]))?strip_tags(str_replace("'","`",$_REQUEST["phone"])):'';
	$email = (!empty($_REQUEST["email"]))?strip_tags(str_replace("'","`",$_REQUEST["email"])):'';
	$comments = (!empty($_REQUEST["comments"]))?strip_tags(str_replace("'","`",$_REQUEST["comments"])):'';
	$date = (!empty($_REQUEST["date"]))?strip_tags(str_replace("'","`",$_REQUEST["date"])):'';
	$eventID = (!empty($_REQUEST["eventID"]))?strip_tags(str_replace("'","`",$_REQUEST["eventID"])):'';
	$processNOJSbooking = (!empty($_POST["processNOJSbooking"]))?strip_tags(str_replace("'","`",$_POST["processNOJSbooking"])):'';
	$captcha_sum = (!empty($_POST["captcha_sum"]))?strip_tags(str_replace("'","`",$_POST["captcha_sum"])):'';
	$captcha = (!empty($_POST["captcha"]))?strip_tags(str_replace("'","`",$_POST["captcha"])):'';
	$qty = (!empty($_REQUEST["qty_".$eventID]))?strip_tags(str_replace("'","`",$_REQUEST["qty_".$eventID])):'1';
	
	####################################### PREPARE AVAILABILITY TABLE ##############################################
	$reservedArray=array();
	$availability = "";
	$show_form = true;
	$text="";
	$paypal_form="";
	
	if(!empty($processNOJSbooking) && $processNOJSbooking=="yes"){
		if(!empty($captcha_sum) && !empty($captcha) && md5($captcha)==$captcha_sum){
		if(!empty($name) && !empty($phone) && !empty($email) && !empty($eventID)){
			if(!preg_match("(^[-\w\.]+@([-a-z0-9]+\.)+[a-z]{2,4}$)i", $email)){
				$msg = "<div class='error_msg'>Invalid email address. Please check your input.</div>";
			} else {
			//processing		
				##################################################################################
	#  	3. PREPARE BOOKING DATE/TIME  
	# CREATE ORDER
	$q="INSERT INTO bs_reservations (dateCreated, name, email, phone, comments,status,eventID, qty) VALUES (NOW(),'".$name."','".$email."','".$phone."','".$comments."','2','".$eventID."','".$qty."')";
	$res=$mysqli->query($q) or die("error!");
	$orderID=$mysqli->insert_id;
	
		if(!empty($orderID) && !empty($eventID)){
			
			//get customer name and email
			$custInf = getInfoByReservID($orderID);
			//get event information for email notification
			$eventInf = getEventInfo($eventID);
			
				//send confirmation to client only if previous status was anything but "Confirmed"
				$headers  = "MIME-Version: 1.0\n";
				$headers .= "Content-type: text/html; charset=utf-8\n";
				$headers .= "From: '".$_SERVER['HTTP_HOST']." Booking System' <info@".$_SERVER['HTTP_HOST']."> \n";
				$subject = "Event booking placed!";	
				$message = "Dear ".$custInf[0].",<br /> <br /> Thank you for your reservation. <br />";
				if($eventInf[3]=="1"){ 
					$message .= "Your reservation will be processed/confirmed after we will receive your payment.<br />";
				}
				$message .="Event name: ".$eventInf[0]."<br />";
				$message .="Event date: ".$eventInf[2]."<br />";
				$message .="Ticket Quantity: ".$qty."<br />";
				$message .="Event description: ".$eventInf[1]."<br />";
				$message .="<br />Reservation Status: Not Confirmed<br/>";
				$message .="<br /><br />Kind Regards, <br /> ".$_SERVER['HTTP_HOST']." Team";	
				mail($custInf[1],$subject,$message,$headers);	
				$sent=true;
			
		
			
		##################################################################################
		#  	4. SEND NOTICE TO ADMIN AND CUSTOMER
		//send email to admin
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=utf-8\n";
		$headers .= "From: 'Your Booking System' <noreply@".$_SERVER['HTTP_HOST']."> \n";
		$subject = "New un-confirmed event reservation (#".$orderID.")!";
		$message = "Dear administrator,<br /> <br /> New event reservation has been placed on your website. <br />";
		$message .="Name: ".$name;
		$message .="<br />Email: ".$email;
		$message .="<br />Phone: ".$phone;
		$message .="<br />Ticket Quantity: ".$qty;
		$message .="<br />Comments: ".$comments;
		$message .="<br />Event name: ".$eventInf[0]."<br />";
		$message .="Event date: ".$eventInf[2]."<br />";
		$message .="<br />Reservation Status: Not Confirmed<br />";
		$message .="<br /><br />Kind Regards, <br /> ".$_SERVER['HTTP_HOST']." Website";
		$adminMail = getAdminMail();
		mail($adminMail,$subject,$message,$headers);
		
		
		//header("Location: event-thank-you.php");
		
	
	
	if($eventInf[3]=="1"){
		$text = "<h2>Thank you for your reservation!</h2><p>You're almost done. There's just one thing left to do - payment. Please click button below and you will be transfered to PayPal.com for fast and secure payment. Please note that your booking will be confirmed only after</p>";
		
		
		
		//CREATE PAYPAL PROCESSING
		require_once('paypal.class.php'); 
		$paypal = new paypal_class;
		$tt = getAdminPaypal();
		$paypal->add_field('business', $tt[0]);
		$scrpt = str_replace("event-booking-nojs.php","paypal.ipn.php",$_SERVER['SCRIPT_NAME']);
		$paypal->add_field('return', "http://".$_SERVER['HTTP_HOST'].$scrpt.'?action=success');
		$paypal->add_field('cancel_return', "http://".$_SERVER['HTTP_HOST'].$scrpt.'?action=cancel');
		$paypal->add_field('notify_url', "http://".$_SERVER['HTTP_HOST'].$scrpt.'?action=ipn');
		$paypal->add_field('item_name_1', "Registration for event '".$eventInf[0]."' (".$eventInf[2].")");
		$paypal->add_field('amount_1', number_format($eventInf[4],2));
		$paypal->add_field('item_number_1', "0001");
		$paypal->add_field('quantity_1', $qty);
		$paypal->add_field('custom', $orderID."-".$eventID);
		$paypal->add_field('upload', 1);
		$paypal->add_field('cmd', '_cart'); 
		$paypal->add_field('txn_type', 'cart'); 
		$paypal->add_field('num_cart_items', 1);
		$paypal->add_field('payment_gross', number_format($eventInf[4],2));
		$paypal->add_field('currency_code', $tt[1]);
		
		
		
		$paypal_form = "<form method=\"post\" name=\"paypal_form\" ";
		$paypal_form .= "action=\"".$paypal->paypal_url."\">\n";
		foreach ($paypal->fields as $name => $value) {
			$paypal_form .= "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
		}
		$paypal_form .= "<input type=\"submit\" class=\"submitProcessing\" value=\"Click Here To Pay For Event\"></center>\n";
		$paypal_form .= "</form>\n";
		
		
		$show_form=false;
		
	} else { 
		$text = "<h2>Thank you for your reservation!</h2><p>">RESERV_MSG."</p>";
				$paypal_form = "";
		$show_form=false;
	}
	
		}
				
			} 
		} else { 
			$msg = "<div class='error_msg'>".FIELDS_NEEDED." </div>";
		}
		
		} else { 
		//throw error
		$msg = "<div class='error_msg'>".CAPTCHA_ERROR."</div>";
		}
	}
		
			
			$availability = getEventsList($date);
		
				
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Booking System v3.1 - NOJS</title>
<link rel="stylesheet" href="css/bs-admin.css" type="text/css" />
</head>
<body>
<noscript>
    <div class="js_error"><?php echo JAVA_NEEDED?> <a href="http://www.mozilla.com/en-US/firefox/upgrade.html" target="_blank"><?php echo BROWSER?></a></div>
</noscript>
<?php echo $msg; ?>
<div class="internal_booking_form">

<?php echo $text; ?>
<?php echo $paypal_form; ?>
<?php if($show_form){?>

<form name="ff1" enctype="multipart/form-data" method="post" action="event-booking-nojs.php">  
<input type="hidden" value="<?php echo $date?>" name="date">
<input type="hidden" value="yes" name="processNOJSbooking" />




<h2>Events on <?php echo date("d F Y", strtotime($date))?></h2>

<?php echo $availability?>
<br />
<table width="250" border="0" align="center" cellpadding="0" cellspacing="0">
              
              <tr>
                <td height="30" align="right" class="align_right"><?php echo YNAME?>*:&nbsp;</td>
                <td>
                  <input type="text" name="name" id="name" value="<?php echo $name?>"  onchange="checkFieldBack(this)"/>
                </td>
              </tr>
              <tr>
                <td height="30" align="right" class="align_right"><?php echo BOOKING_FRM_PHONE?>&nbsp;</td>
                <td><input type="text" name="phone" id="phone" value="<?php echo $phone?>"  onchange="checkFieldBack(this)" onkeyup="noAlpha(this)"/></td>
              </tr>
              <tr>
                <td height="30" align="right" class="align_right"><?php echo BOOKING_FRM_EMAIL?>&nbsp;</td>
                <td><input type="text" name="email" id="email"  value="<?php echo $email?>" onchange="checkFieldBack(this);"/></td>
              </tr>
            
              <tr>
                <td align="right"  valign="top" class="align_right"><?php echo BOOKING_FRM_COMMENTS?>&nbsp;</td>
                <td><textarea name="comments" id="comments" cols="15" rows="5" onchange="checkFieldBack(this)"><?php echo $comments?></textarea></td>
              </tr>
              
              <?php
			  $num1 = rand(1,9);
			  $num2 = rand(1,9);
			  $sum = $num1 + $num2;
			  ?>
               <tr>
                <td height="30" align="right" class="align_right"><?php echo $num1." + ".$num2." = "?></td>
                <td align="left">&nbsp;&nbsp;&nbsp;<input type="text" name="captcha" id="captcha"  value="" style="width:30px;" onchange="checkFieldBack(this);"/>
                <input type="hidden" name="captcha_sum" value="<?php echo md5($sum);?>" />
                </td>
              </tr>
              
              <tr>
            	<td height="15">&nbsp;</td>
            	<td>&nbsp;</td>
          	  </tr>
              <tr>
                <td colspan="2" align="center" class="align_center"><input type="image" src="images/reserve_btn.jpg"  /></td>
              </tr>
              <tr>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
          	  </tr>
</table>

              </form>
              </div>
   <?php } ?>           
</div>
</body>
</html>
