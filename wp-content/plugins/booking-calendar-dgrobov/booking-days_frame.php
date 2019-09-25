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
	
	bw_do_action("bw_load");
	$name = (!empty($_REQUEST["name"]))?strip_tags(str_replace("'","`",$_REQUEST["name"])):'';
	$phone = (!empty($_REQUEST["phone"]))?strip_tags(str_replace("'","`",$_REQUEST["phone"])):'';
	$email = (!empty($_REQUEST["email"]))?strip_tags(str_replace("'","`",$_REQUEST["email"])):'';
	$comments = (!empty($_REQUEST["comments"]))?strip_tags(str_replace("'","`",$_REQUEST["comments"])):'';
	$dateFrom = (!empty($_REQUEST["dateFrom"]))?strip_tags(str_replace("'","`",$_REQUEST["dateFrom"])):'';
        $dateTo = (!empty($_REQUEST["dateTo"]))?strip_tags(str_replace("'","`",$_REQUEST["dateTo"])):'';
	$captcha_sum = (!empty($_POST["captcha_sum"]))?strip_tags(str_replace("'","`",$_POST["captcha_sum"])):'';
	$captcha = (!empty($_POST["captcha"]))?strip_tags(str_replace("'","`",$_POST["captcha"])):'';
	$msg2 = (!empty($_REQUEST["msg2"]))?strip_tags(str_replace("'","`",$_REQUEST["msg2"])):'';
	$serviceID = (!empty($_REQUEST["serviceID"]))?strip_tags(str_replace("'","`",$_REQUEST["serviceID"])):1;
	$qty = (!empty($_REQUEST["qty"]))?strip_tags(str_replace("'","`",$_REQUEST["qty"])):1;
	$lb3 = (!empty($_REQUEST["lb3"]))?strip_tags(str_replace("'","`",$_REQUEST["lb3"])):'';
    $couponCode = (!empty($_REQUEST["couponCode"]))?strip_tags(str_replace("'","`",$_REQUEST["couponCode"])):'';
	//print_r($time);
	
	$serviceSettings = getServiceSettings($serviceID);
        $serviceName = getService($serviceID,"name");
	$dayPrice = getDayPrice($dateFrom,$serviceID);
        $currencyPos = getOption('currency_position');
        $currency = getOption('currency');
        $coupons = getServiceSettings($serviceID, 'coupon');
	$daysToEnd = getDaysAvailableByDate($dateFrom,$serviceID);
        $availableDatesString = getAvailableDates($serviceID);
$_availableDatesString = getAvailableDates($serviceID,true);
        
	if(!empty($lb3) && $lb3=="yes"){
		$msg = "<div class='error_msg'> ".CAPTCHA_ERROR."; </div>";
	}
?>

<?php include "includes/javascript.validationDays.php";?>
  <script type="text/javascript">
$(function(){
    updateDatePickers()
})
function addInterval(el){
        var data = '<div>form: <input type="text" name="_dateFrom[]" value="" class="datepicker small"/>&nbsp;&nbsp;to:&nbsp;<input type="text" name="_dateTo[]" value="" class="datepicker small"/>&nbsp;&nbsp;price&nbsp;<input type="text" name="_price[]" class="small" value=""></div>';    
        $(el).before(data);
        updateDatePickers();    
        }
        

function disableDates(date){
    var dates = [<?php echo  $availableDatesString ?>];
    var m = date.getMonth()+1, d = date.getDate(), y = date.getFullYear();
    //console.log('Checking (raw): ' + m + '-' + d + '-' + y);
    for (i = 0; i < dates.length; i++) {
        if(ArrayContains(dates,m + '-' + d + '-' + y)) {
            
            return [true];
        }
    }
    
    return [false];
}
function _disableDates(date){
    var dates = [<?php echo  $_availableDatesString ?>];

    var m = date.getMonth()+1, d = date.getDate(), y = date.getFullYear();
    //console.log('Checking (raw): ' + m + '-' + d + '-' + y);
    for (i = 0; i < dates.length; i++) {
        if(ArrayContains(dates,m + '-' + d + '-' + y)) {

            return [true];
        }
    }

    return [false];
}
function updateDatePickers(){
    dateFrom = $( "#dateFrom" ).datepicker({
        defaultDate: '<?php echo  $dateFrom ?>',
        changeMonth: true,
        showOn: "both",
        buttonImage: "images/services_32_1.png",
        buttonImageOnly: true,
        minDate:0,
        beforeShowDay:disableDates,
        dateFormat: "yy-mm-dd",
        changeYear: false,
        onSelect: function( selectedDate,inst ) {//console.log(inst);
            var date=new Date(inst.selectedYear,inst.selectedMonth,parseInt(inst.selectedDay,10));
            var days=date-new Date;
            var interval=Math.ceil(days/(1000*60*60*24));
            $( "#dateTo" ).val(selectedDate+1 );
            dateTo.not( this ).datepicker( "option", 'minDate', +interval+1 );
            //dateTo.not( this ).datepicker( "option", 'defaultDate', selectedDate+1 );
            //dateTo.not( this ).datepicker( "option", "maxDate", +(interval+<?php echo $daysToEnd ?>) );
            
            
        }
        
    });
    var currDate=new Date('<?php echo date("m/d/Y", strtotime($dateFrom)) ?>');
    var currdays=currDate-new Date();
    var curinterval=Math.ceil(currdays/(1000*60*60*24));//console.log(currDate);
    dateTo= $( "#dateTo" ).datepicker({
        //defaultDate: '<?php echo $dateFrom ?>'+1,
        changeMonth: true,
        showOn: "both",
        buttonImage: "images/services_32_1.png",
        buttonImageOnly: true,
        beforeShowDay:_disableDates,
        dateFormat: "yy-mm-dd",
        changeYear: false,
        minDate:+(curinterval+1),
        //maxDate:+(curinterval+<?php echo $daysToEnd ?>),//new Date(<?php echo date("Y", strtotime($daysToEnd)) ?>,<?php echo date("m", strtotime($daysToEnd)) ?>,<?php echo date("d", strtotime($daysToEnd)) ?>)
        onSelect: checkAvailability
    });
}
    </script>
<?php echo $msg; ?>
<div class="internal_booking_form" id="resize">
    <div id="mess">
            
        </div>
    <form name="ff1" enctype="multipart/form-data" method="post" action="booking-days.processing.php" onsubmit="return checkForm();">
<input type="hidden" value="<?php echo $dateFrom?>" name="dateFrom">
<input type="hidden" name="serviceID" value="<?php echo $serviceID;?>" />

<h2><?php  echo _getDate(date(getOption("date_mode"),strtotime($dateFrom)))?> <?php echo AVAIL; ?></h2>



<div class="eventWrapper">
    <div class="eventContainer days">
            <div class="eventCheckbox">

        </div>
        <div class="eventTitle"><b><?php echo $serviceName;?></b></div>
        <table width="100%" class="evntCont">
            <tbody>
            <tr>
                <td width="80%" valign="top">
                    <div class="eventDescr">Maximum Spaces  <span><?php echo $serviceSettings['spots']?></span>
                        <div>
                            <?php if(!empty($serviceSettings['img'])){?><img src="<?php echo $serviceSettings['img']?>" width="100"/><?php }?>
                            <?php echo nl2br($serviceSettings['description'])?>
                        </div>
                    </div>
                </td>
                <td></td>
                <td class="brd_l">
                    <div class="selDate">
                        <label>From</label>
                        <div class="calCont">
                            <input type="text" value="<?php echo $dateFrom?>" name="dateFrom" id="dateFrom" class="small"/>
                        </div>
                    </div>
                </td>
                <td class="brd_l right">
                    <div class="selDate">
                        <label>To</label>
                        <div class="calCont">
                            <input type="text" value="<?php echo $dateTo?>" name="dateTo" id="dateTo" class="small"/>
                        </div>
                    </div>
                </td>
                <td class="brd_l">
                    <div class="fee">

                            <span style="color:#0FA1D2" id="feeValue">
                                <?php if(empty($dayPrice)){
                                    echo TXT_FUNC_FREE;

                                }else{
                                    echo getCurrencyText("0.00");
                                }?>

                            </span>
                        <del id='feeValueOld'></del>
                        <div id="fee_message">
                            <div id="fee_message_text"> </div>
                            <i></i>
                        </div>
                    </div>
                </td>
            </tr>
                <?php if($coupons){?>
                <tr>
                    <td colspan="5" align="center">
                        <label><?php echo TXT_COUPON_CODE?>:</label><input type='text' name='couponCode' id='couponCode' value='<?php echo $couponCode?>' class='small'>&nbsp;<span id='discountDetails'></span>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>
        <br clear="all">
        <div class="social"></div>

    </div>

</div>

 <?php
			  $num1 = rand(1,9);
			  $num2 = rand(1,9);
			  $sum = $num1 + $num2;
			  ?>
			<div class="tab"><?php echo BOOKING_FORM;?></div>
			<div class="book_form">
				<table width="650" class="booking_form">
					<tr>
						<td align="left">
							<span><?php echo YNAME;?>*:&nbsp;</span>
							<input type="text" name="name" id="name" value="<?php echo $name?>"  onchange="checkFieldBack(this)"/>
							<span><?php echo BOOKING_FRM_PHONE;?>*:&nbsp;</span>
							<input type="text" name="phone" id="phone" value="<?php echo $phone?>"  onchange="checkFieldBack(this)" onkeyup="noAlpha(this)"/>
							<span><?php echo BOOKING_FRM_EMAIL;?>*:&nbsp;</span>
							<input type="text" name="email" id="email"  value="<?php echo $email?>" onchange="checkFieldBack(this);"/>


						</td>

                        <td align="left" style="padding-left:10px">
                            <span><?php echo BOOKING_FRM_COMMENTS;?>:&nbsp;</span>
                            <textarea name="comments" id="comments" cols="15" rows="5" onchange="checkFieldBack(this)"><?php echo $comments?></textarea>
                            <div class="captchaCont">
                                <span><?php echo $num1." + ".$num2." = "?></span>
                                <input type="text" name="captcha" id="captcha"  value="" onchange="checkFieldBack(this);"/>
                            </div><input type="image" src="images/reserve_btn.jpg" style="margin-top: 28px;" />
                            <input type="hidden" name="captcha_sum" value="<?php echo md5($sum);?>" />
                            <input type="text" name="email1" value="" class="hi">
                        </td>
					</tr>
				</table>
			</div>

              </form>
		  
