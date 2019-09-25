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
	
	$eventID = (!empty($_REQUEST["eventID"]))?strip_tags(str_replace("'","`",$_REQUEST["eventID"])):'';
	$selEvent = $eventID;
	$captcha_sum = (!empty($_POST["captcha_sum"]))?strip_tags(str_replace("'","`",$_POST["captcha_sum"])):'';
	$captcha = (!empty($_POST["captcha"]))?strip_tags(str_replace("'","`",$_POST["captcha"])):'';
	$qty = (!empty($_REQUEST["qty_".$eventID]))?strip_tags(str_replace("'","`",$_REQUEST["qty_".$eventID])):'';
	$qtySel = (!empty($_REQUEST["qty_".$selEvent]))?strip_tags(str_replace("'","`",$_REQUEST["qty_".$selEvent])):'';
	
	$msg2 = (!empty($_REQUEST["msg2"]))?strip_tags(str_replace("'","`",$_REQUEST["msg2"])):'';
	$eventID = (!empty($_REQUEST["eventID"]))?strip_tags(str_replace("'","`",$_REQUEST["eventID"])):'';
	$lb2 = (!empty($_REQUEST["lb2"]))?strip_tags(str_replace("'","`",$_REQUEST["lb2"])):'';
	
	$eventInfo = getEventInfo($eventID);
	if($eventInfo === false){header("Location:eventList.php");exit();}
	$serviceID = $eventInfo[6];
	$date = $eventInfo[7];
	####################################### PREPARE AVAILABILITY TABLE ##############################################
	$availability = "";
	
	$availability = getEventList($eventID,$qtySel,$date);
				
				
		if(!empty($lb2) && $lb2=="yes"){
			$msg = "<div class='error_msg'>".CAPTCHA_ERROR."</div>";
		}
?>
<?php include "includes/header.php";?>

<div id="index" style="width:1048px;"  id="resize">

<h2><?php echo EVENTS_LIST_TITLE?> <?php echo $eventInfo[2]?></h2>


<div id="eventListConteiner">
<?php echo $msg;?>
<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>

<script type="text/JavaScript">
<!--
  
function checkForm() {
	var err=0;
	var msg2="";
<?php
$reqFields=array(
	"name",
	"phone",
	"email",
	"captcha"
	
);

foreach ($reqFields as $v) { ?>

	if (document.getElementById('<?php echo $v?>').value==0 || document.getElementById('<?php echo $v?>').value=="00") {
		if (err==0) {
			document.getElementById('<?php echo $v?>').focus();
		}
		document.getElementById('<?php echo $v?>').style.backgroundColor='#ffa5a5';
		err=1;
	}<?php

}
?>
	
	var reg1 = /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/; // not valid
	var reg2 = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/; // valid   
	if (document.getElementById('email').value==0 || !reg2.test(document.getElementById('email').value)) {
	if (err==0) {
		document.getElementById('email').focus();
	}
	document.getElementById('email').style.backgroundColor='#ffa5a5';
	err=1;
	}
	

		


if (err==0) {
			 return true;
	} else {
        alert('<?php echo MSG_JS_ALLFIELDS; ?>');
		return false;
	}
	
}


function checkFieldBack(fieldObj) {
	if (fieldObj.value!=0) {
		fieldObj.style.backgroundColor='#fff';
	}
}

function checkNumeric(value){
		var anum=/(^\d+$)|(^\d+\.\d+$)/
		if (anum.test(value))
			return true;
		return false;
    }

function noAlpha(obj){
	reg = /[^0-9.,]/g;
	obj.value =  obj.value.replace(reg,"");
 }

//-->
</script> 
<form name="ff1" enctype="multipart/form-data" method="post" action="booking.event.processing.php" onsubmit="return checkForm();">  
<input type="hidden" value="<?php echo $date?>" name="date">
<input type="hidden" value="<?php echo $serviceID?>" name="serviceID">
<input type="hidden" value="one" name="ref">
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
							<span><?php echo BOOKING_FRM_PHONE?>&nbsp;</span>
							<input type="text" name="phone" id="phone" value="<?php echo $phone?>"  onchange="checkFieldBack(this)" onkeyup="noAlpha(this)"/>
							<span><?php echo BOOKING_FRM_EMAIL?>&nbsp;</span>
							<input type="text" name="email" id="email"  value="<?php echo $email?>" onchange="checkFieldBack(this);"/>
						
							
						</td>
						
						<td align="left" style="padding-left:10px">
							<span><?php echo BOOKING_FRM_COMMENTS?>&nbsp;</span>
							<textarea name="comments" id="comments" cols="15" rows="5" onchange="checkFieldBack(this)"><?php echo $comments?></textarea>
							<div style="float: left; margin: 33px 89px 0pt 0pt;">
							<?php echo $num1." + ".$num2." = "?><input type="text" name="captcha" id="captcha"  value="" style="width:50px;" onchange="checkFieldBack(this);"/>
							</div><input type="image" src="images/reserve_btn.jpg" style="margin-top: 28px;" />
							<input type="hidden" name="captcha_sum" value="<?php echo md5($sum);?>" />
						</td>
					</tr>
				</table>
			</div>
              </form>
</div>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
<?php include "includes/footer.php";?>
