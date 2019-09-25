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

######################### DO NOT MODIFY (UNLESS SURE) ########################


require_once("includes/config.php"); //Load the configurations

$orderID = (!empty($_GET["orderID"]))?strip_tags(str_replace("'","`",$_GET["orderID"])):'';
$serviceID = (!empty($_GET["serviceID"]))?strip_tags(str_replace("'","`",$_GET["serviceID"])):'';
//print  $orderID;

 bw_do_action("bw_load");

 $bookingInfo = getBooking($orderID);
 
  
 if(empty($bookingInfo['eventID'])){
 
    $infoForBooking = do_payment($orderID,getServiceSettings($serviceID,"payment_method"),'pay');
   
 }else{
     $eventInfo = getEventInfo($bookingInfo['eventID']);
     $infoForBooking = do_payment($orderID,$eventInfo['payment_method'],"pay");
 }
 
 require_once("includes/header.php"); //Load the configurations  
 
 ?>


<div id="index">
<h1><?php echo BEP_16;?> #<?php echo $orderID?></h1>
<?php echo getOrderSummery($orderID);?>

<?php echo $infoForBooking?>
<br>


<?php include "includes/footer.php"?>