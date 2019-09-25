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
require_once('includes/paypal.class.php');  // include the class file
$paypal = new paypal_class;             // initiate an instance of the class
//$paypal->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';     // paypal url

include "includes/header.php";
?>

<div id="index">
<h1><?php echo PP_THANK_H1?></h1>
<?php
switch ($_GET['action']) { 
    case 'success':      // Order was successful...
      echo "<p>".PP_THANKYOU."</p>";
       
       
	break;
      
    case 'cancel':       // Order was canceled...
      echo "<p>".PP_CANCEL."</p>";
	break;
      
   case 'ipn':          // Paypal is calling page for IPN validation...
   	  if ($paypal->validate_ipn()) {		

		//-----> send notification 
		//creating message for sending

		$subject = PP_SUBJ_RECEIVED;
		$adminMail = getAdminMail();
		
		$data=array(
			"{%payerEmail%}"=>$paypal->pp_data['payer_email'],
			"{%date%}"=>date('m/d/Y'),
			"{%time%}"=>date('g:i A')
		);
                $orderNumber = $paypal->pp_data['custom'];
                $orderInfo = getBooking($orderNumber);
		if(!empty($orderInfo['eventID'])){
			
			//EVENT payment
			
			$eventInf = getEventInfo($orderInfo['eventID']);
			$data['{%text%}']="<br /> Payment was made for event \"".$eventInf[0]."\" (".$eventInf[2].")";
			
			$q="UPDATE bs_reservations SET status='4' WHERE id='".$orderNumber."' AND eventID='".$orderInfo['eventID']."'";
			$mysqli->query($q);
			
			$q="INSERT INTO bs_transactions (reservationID,eventID,dateCreated,transactionID,amount,payment_status,currency,payer_email,payer_name)	VALUES ('".$orderNumber."','".$orderInfo['eventID']."','".DATETIME."','".$paypal->pp_data['txn_id']."','".$paypal->pp_data['mc_gross_1']."','".$paypal->pp_data['payment_status']."','".$paypal->pp_data['mc_currency']."','".$paypal->pp_data['payer_email']."','".$paypal->pp_data['first_name']." ".$paypal->pp_data['last_name']."')";
			$mysqli->query($q);
			sendPaymentEmails($orderNumber,"PayPal Gateway");
		} else {
			//Booking payment
			
			$data['{%text%}']="<br /> Payment was made for regular booking";

			//-----> send notification end 		
			
			$q="UPDATE bs_reservations SET status='4' WHERE id='".$orderNumber."'";
			$mysqli->query($q);
			
			$q="INSERT INTO bs_transactions (reservationID,eventID,dateCreated,transactionID,amount,payment_status,currency,payer_email,payer_name)	VALUES ('".$orderNumber."','0','".DATETIME."','".$paypal->pp_data['txn_id']."','".$paypal->pp_data['mc_gross_1']."','".$paypal->pp_data['payment_status']."','".$paypal->pp_data['mc_currency']."','".$paypal->pp_data['payer_email']."','".$paypal->pp_data['first_name']." ".$paypal->pp_data['last_name']."')";
			$mysqli->query($q);
                        sendPaymentEmails($orderNumber,"PayPal Gateway");
		}
		
		
	  
	  
      }
      break;
 }     
 if (IS_WP_PLUGIN == '1') {
    if (isset($_SESSION['site'])) {
        echo "<a href='{$_SESSION['site']}' id='back' style='display:block'>Back to calendar</a>";
    }
}else{
    echo "<a href='".$baseDir."index.php' id='back' style='display:block'>Back to calendar</a>";
}
include "includes/footer.php";
?>