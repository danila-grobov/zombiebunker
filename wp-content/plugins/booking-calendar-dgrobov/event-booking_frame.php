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
	$selEvent = (!empty($_REQUEST["selEvent"]))?strip_tags(str_replace("'","`",$_REQUEST["selEvent"])):'';
	$captcha_sum = (!empty($_POST["captcha_sum"]))?strip_tags(str_replace("'","`",$_POST["captcha_sum"])):'';
	$captcha = (!empty($_POST["captcha"]))?strip_tags(str_replace("'","`",$_POST["captcha"])):'';
	$qty = (!empty($_REQUEST["qty_".$eventID]))?strip_tags(str_replace("'","`",$_REQUEST["qty_".$eventID])):'';
	$qtySel = (!empty($_REQUEST["qty_".$selEvent]))?strip_tags(str_replace("'","`",$_REQUEST["qty_".$selEvent])):'';
	$serviceID = (!empty($_REQUEST["serviceID"]))?strip_tags(str_replace("'","`",$_REQUEST["serviceID"])):1;
	$msg2 = (!empty($_REQUEST["msg2"]))?strip_tags(str_replace("'","`",$_REQUEST["msg2"])):'';
	$eventID = (!empty($_REQUEST["eventID"]))?strip_tags(str_replace("'","`",$_REQUEST["eventID"])):'';
    $couponCode = (!empty($_REQUEST["couponCode"]))?strip_tags(str_replace("'","`",$_REQUEST["couponCode"])):'';
	
	####################################### PREPARE AVAILABILITY TABLE ##############################################
	$availability = "";
	$eventInfo = getEventInfo($eventID);
	
	$availability =	getEventList($eventID,$qtySel,$date,$couponCode);
	//$availability = getEventsList($date,$serviceID,$eventID,$selEvent,$qtySel);
				
				
		if(!empty($msg2) && $msg2=="captcha"){
			$msg = "<div class='error_msg'>".CAPTCHA_ERROR."</div>";
		}
?>
<link rel="stylesheet" href="css/bs-admin.css" type="text/css" />
<?php include "includes/javascript.validationEvents.php";?>
<noscript>
    <div class="js_error"><?php echo JAVA_NEEDED?><a href="http://www.mozilla.com/en-US/firefox/upgrade.html" target="_blank"></php echo BROWSER?></a></div>
</noscript>
<?php echo $msg; ?>
<div class="internal_booking_form">
    <div id="mess">
            
        </div>
<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>

<form name="ff1" enctype="multipart/form-data" method="post" action="booking.event.processing.php" onsubmit="return checkForm();">  
<input type="hidden" value="<?php echo $date?>" name="date">
<input type="hidden" value="<?php echo $serviceID?>" name="serviceID">

<h2><?php echo EVENTS_LIST_TITLE?> <?php echo _getDate(date(getOption('date_mode'), strtotime($date)))?></h2>

<?php echo $availability?>
<br />
  
              <?php
			  $num1 = rand(1,9);
			  $num2 = rand(1,9);
			  $sum = $num1 + $num2;
			  ?>
			<div class="tab"><?php echo BOOKING_FORM?></div>
			<div class="book_form">
				<table width="650" class="booking_form">
					<tr>
						<td align="left">
							<span><?php echo YNAME?>*:&nbsp;</span>
							<input type="text" name="name" id="name" value="<?php echo $name?>"  onchange="checkFieldBack(this)"/>
							<span><?php echo BOOKING_FRM_PHONE?>*:&nbsp;</span>
							<input type="text" name="phone" id="phone" value="<?php echo $phone?>"  onchange="checkFieldBack(this)" onkeyup="noAlpha(this)"/>
							<span><?php echo BOOKING_FRM_EMAIL?>*:&nbsp;</span>
							<input type="text" name="email" id="email"  value="<?php echo $email?>" onchange="checkFieldBack(this);"/>
						
							
						</td>
						
						<td align="left" style="padding-left:10px">
							<span><?php echo BOOKING_FRM_COMMENTS?>&nbsp;</span>
							<textarea name="comments" id="comments" cols="15" rows="5" onchange="checkFieldBack(this)"><?php echo $comments?></textarea>
							<div style="float: left; margin: 33px 89px 0pt 0pt;">
							<?php echo $num1." + ".$num2." = "?><input type="text" name="captcha" id="captcha"  value="" style="width:50px;" onchange="checkFieldBack(this);"/>
							</div><input type="image" src="images/reserve_btn.jpg" style="margin-top: 28px;" />
							<input type="hidden" name="captcha_sum" value="<?php echo md5($sum);?>" />
                            <input type="text" name="email1" value="" class="hi">
						</td>
					</tr>
				</table>
			</div>
              </form>
              </div>
			  
			  <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>