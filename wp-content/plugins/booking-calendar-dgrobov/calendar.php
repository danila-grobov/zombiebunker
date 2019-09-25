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
//session_start();
$currDir = dirname(__FILE__).'/';

//require_once($currDir."includes/dbconnect.php"); //Load the settings
require_once($currDir."includes/config.php"); //Load the functions




$date = (!empty($_REQUEST["date"]))?strip_tags(str_replace("'","`",$_REQUEST["date"])):'';

$dateFrom = (!empty($_REQUEST["dateFrom"])) ? strip_tags(str_replace("'", "`", $_REQUEST["dateFrom"])) : '';
$dateTo = (!empty($_REQUEST["dateTo"])) ? strip_tags(str_replace("'", "`", $_REQUEST["dateTo"])) : '';

$lb1 = (!empty($_REQUEST["lb1"])) ? strip_tags(str_replace("'", "`", $_REQUEST["lb1"])) : '';
$lb2 = (!empty($_REQUEST["lb2"])) ? strip_tags(str_replace("'", "`", $_REQUEST["lb2"])) : '';
$lb3 = (!empty($_REQUEST["lb3"])) ? strip_tags(str_replace("'", "`", $_REQUEST["lb3"])) : '';

$ajax = (!empty($_REQUEST["ajax"]))?strip_tags(str_replace("'","`",$_REQUEST["ajax"])):'';

if(!$ajax){
    $_SESSION['site']=$_SERVER['REQUEST_URI'];
}

$eventID = (!empty($_GET["eventID"]))?$_GET["eventID"]:'';
$selEvent = (!empty($_GET["selEvent"]))?$_GET["selEvent"]:'';
$name = (!empty($_REQUEST["name"]))?strip_tags(str_replace("'","`",$_REQUEST["name"])):'';
$phone = (!empty($_REQUEST["phone"]))?strip_tags(str_replace("'","`",$_REQUEST["phone"])):'';
$email = (!empty($_REQUEST["email"]))?strip_tags(str_replace("'","`",$_REQUEST["email"])):'';
$comments = (!empty($_REQUEST["comments"]))?strip_tags(str_replace("'","`",$_REQUEST["comments"])):'';
$qty = (!empty($_REQUEST["qty_".$selEvent]))?strip_tags(str_replace("'","`",$_REQUEST["qty_".$selEvent])):'';
$time = (!empty($_GET["time"]))?$_GET["time"]:'';

$serviceID = (!empty($_REQUEST["serviceID"]))?strip_tags(str_replace("'","`",$_REQUEST["serviceID"])):getDefaultService();

$showServices = 1;


if(isset($curSrviceID) && !empty($curSrviceID)){
    $showServices = 0;
    $serviceID=$curSrviceID;
}else{
    $curSrviceID = $serviceID;
}
$showServices = (isset($_REQUEST["show_services"]))?$_REQUEST["show_services"]:$showServices;
############################## REQUEST CALENDAR DATE IF NAVIGATION USED ################################
$startDay = getServiceSettings($serviceID, 'startDay');
$iMonth = (!empty($_REQUEST["month"]))?strip_tags(str_replace("'","`",$_REQUEST["month"])):date('n');
$iYear = (!empty($_REQUEST["year"]))?strip_tags(str_replace("'","`",$_REQUEST["year"])):date('Y');	
$calendar = "";
$calendar = setupSmallCalendar($iMonth,$iYear,$serviceID,$baseDir);
list($iPrevMonth, $iPrevYear) = prevMonth($iMonth, $iYear);
list($iNextMonth, $iNextYear) = nextMonth($iMonth, $iYear);
$iCurrentMonth = date('n');
$iCurrentYear = date('Y');
$iCurrentDay = '';
if(($iMonth == $iCurrentMonth) && ($iYear == $iCurrentYear)){
	$iCurrentDay = date('d');
	$thismonth=true;
}
$iNextMonth = mktime(0, 0, 0, $iNextMonth, 1, $iNextYear);
$iPrevMonth = mktime(0, 0, 0, $iPrevMonth, 1, $iPrevYear);
$iCurrentDay = $iCurrentDay;
$iCurrentMonth = mktime(0, 0, 0, $iMonth, 1, $iYear);
$title =_getDate(date('F Y',$iCurrentMonth));

$serviceLink="&serviceID={$serviceID}";
################### PREPARE LINKS FOR CALENDAR NAVIGATION ######################
$prev_month_link = "<a href=\"?month=".date('m',$iPrevMonth)."&year=".date('Y',$iPrevMonth).$serviceLink."&show_services=".$showServices."&serviceID=".$serviceID."\" class=\"previous_month\" rel=\"nofollow\"><<</a>";
$next_month_link = "<a href=\"?month=".date('m',$iNextMonth)."&year=".date('Y',$iNextMonth).$serviceLink."&show_services=".$showServices."&serviceID=".$serviceID."\" class=\"next_month\"  rel=\"nofollow\">>></a>";
################### PREPARE CALENDAR HEADER DEPENDING ON MON OR SUN AS FIRST DAY ######################
if($startDay=="0"){
	$calendarHeader = '<td class="weekend dash_border">'.getShortWeek(0).'</td><td class="dash_border">'.getShortWeek(1).'</td><td class="dash_border">'.getShortWeek(2).'</td><td class="dash_border">'.getShortWeek(3).'</td><td class="dash_border">'.getShortWeek(4).'</td><td class="dash_border">'.getShortWeek(5).'</td><td class="weekend dash_border">'.getShortWeek(6).'</td>';
} else if($startDay=="1"){ 
	$calendarHeader = '<td class="dash_border">'.getShortWeek(1).'</td><td class="dash_border">'.getShortWeek(2).'</td><td class="dash_border">'.getShortWeek(3).'</td><td class="dash_border">'.getShortWeek(4).'</td><td class="dash_border">'.getShortWeek(5).'</td><td class="weekend dash_border">'.getShortWeek(6).'</td><td class="weekend dash_border">'.getShortWeek(0).'</td>';
}
	
	
	
?>
<?php if($ajax!='yes'){?>
<link rel="stylesheet" href="//<?php echo $_SERVER['SERVER_NAME'].$baseDir?>css/small-calendar-styles.css" type="text/css" />
<script type="text/javascript">
if (typeof jQuery != "function"){document.write('<scr' + 'ipt type="text/javascript" src="<?php echo $baseDir?>js/jquery-1.9.0.min.js"></scr' + 'ipt>');}
</script>
<script>
    
if (typeof jQuery.colorbox != "function")
{
	document.write('<scr'+'ipt type="text/javascript" src="<?php echo $baseDir?>js/jquery.colorbox.js"></scri'+'pt>');
    document.write('<link type="text/css" media="screen" rel="stylesheet" href="<?php echo $baseDir?>css/colorbox.css" />');
}

function redirect(url,name){
    
    window.open(url,name);
    //$.colorbox.close();
}
</script>
<div id="calendar">

<?php }?>
    <div id="overlay"><div>Loading</div></div>
			<?php
                        if($showServices){
				$sql="SELECT * FROM bs_services";
				$res=$mysqli->query($sql);
				if($res->num_rows>1){
			?>
	<div style="float:right">
		<form name="ff1" id="ff1" method="post">
			<select name="serviceID" id="serviceID" <?php/*onchange="document.forms['ff1'].submit()"*/?>>
				
				<?php while($row=$res->fetch_assoc()){?>
					<option value="<?php echo $row['id']?>" <?php echo ($serviceID==$row['id'])?"selected=\"selected\"":""?>><?php echo $row['name']?></option>
				<?php }?>
			</select>
		</form>
	</div>
	<div style="clear:both"></div>
	<?php }}?>
<!-- CALENDAR NAVIGATION -->
    <table cellspacing="0" width="" class="dash_border">
    <tr class="calendar_header">
        <th  width="5%">
            <?php echo $prev_month_link?>
        </th>
        <th align="center" width="90%">
            <?php echo $title?>
        </th>
        <th align="right"  width="5%">
            <?php echo $next_month_link?>
        </th>
    </tr>
	<tr>
		<td colspan=3 align=center>
			<table class="bw_calendar" cellpadding="2" cellspacing="5" border="0">
			<tbody>
				 <tr>
				<?php echo $calendarHeader; ?>
                                </tr>
			</tbody>
			<?php echo $calendar; ?>
			</table>
		</td>
	</tr>
    </table>
<?php if($ajax!='yes'){?>	
</div>
<?php }?>
<script language="javascript" type="text/javascript">
	jQuery("#calendar").on("click",".day_number",function(){
		jQuery(".showInfo").hide();
		jQuery(this).find(".showInfo").show();
	});
	jQuery("#calendar").on("change","#serviceID",function(){
		var el=jQuery(this);
		var href="<?php echo $baseDir?>calendar.php?ajax=yes&serviceID="+el.val();
		getAjaxCalendar(href);
	
	});


	function getLightbox(reserveDate,serviceID){
		jQuery.fn.colorbox({href:'<?php echo $baseDir?>booking.php?referrer=calendar&date='+reserveDate+"&serviceID="+serviceID,innerWidth:'1100px',innerHeight:'800px',iframe:true});
			return false;
	}
	function getLightbox2(eventID,serviceID,date){
		jQuery.fn.colorbox({href:'<?php echo $baseDir?>event-booking.php?referrer=calendar&eventID='+eventID+"&serviceID="+serviceID+"&date="+date,innerWidth:'1100px',innerHeight:'800px',iframe:true});
		return false;
	}
    function getLightboxDays(date,serviceID){
        jQuery.fn.colorbox({href:'<?php echo $baseDir?>booking-days.php?referrer=calendar&dateFrom='+date+"&serviceID="+serviceID,innerWidth:'1100px',innerHeight:'800px',iframe:true});
        return false;
    }
    jQuery(document).ready(function() {
	
	jQuery(".calendar_header").on("click",".previous_month",function(e){
        e.preventDefault();
		var el=jQuery(this);
		var href="<?php echo $baseDir?>calendar.php"+el.attr('href')+"&ajax=yes"
		getAjaxCalendar(href);
		return false;
	});
	jQuery(".calendar_header").on("click",".next_month",function(e){
        e.preventDefault();
		var el=jQuery(this);
		var href="<?php echo $baseDir?>calendar.php"+el.attr('href')+"&ajax=yes";
		getAjaxCalendar(href);
		return false;
	});
	
});
function getAjaxCalendar(href){
	jQuery("#overlay").show();
		jQuery.ajax({
			url:href,
			dataType:"html",
			success:function(data){
				jQuery("#calendar").html(data);
			}
		});
}
function resizeFrame(height,width){
    jQuery.colorbox.resize({width:width+'px',height:height+'px'})
}
</script> 

