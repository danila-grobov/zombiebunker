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
session_start();
require_once("includes/dbconnect.php"); //Load the settings
require_once("includes/config.php"); //Load the functions


if ($_SESSION["logged_in"] != true) {
    header("Location: admin.php");
    exit();
} else {
    bw_do_action("bw_load");
    bw_do_action("bw_admin");
    ######################## DO NOT MODIFY (UNLESS SURE) END ########################

    //show page only if admin access level

    //request all neccessary variables for user update action.
    $serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : '';
    $dateFrom = (!empty($_POST["dateFrom"])) ? strip_tags(str_replace("'", "`", $_POST["dateFrom"])) : date("Y-m-d", strtotime(date('Y') . "-" . date('m') . "-01"));
    $dateTo = (!empty($_POST["dateTo"])) ? strip_tags(str_replace("'", "`", $_POST["dateTo"])) : date("Y-m-d");

    $events = (!empty($_POST["events"])) ? intval($_POST["dateTo"]) : 0;
    $tbookings = (!empty($_POST["tbookings"])) ? intval($_POST["dateTo"]) : 0;
    $dbookings = (!empty($_POST["dbookings"])) ? intval($_POST["dateTo"]) : 0;

    if (!empty($_REQUEST["show_reports"]) && $_REQUEST["show_reports"] == "yes") {
		
        }


    include "includes/admin_header.php"; ?>
<script type="text/javascript">
    $(function () {
        $(".datepicker").datepicker({
            changeMonth:true,
            changeYear:true,
            dateFormat:"yy-mm-dd",
            showOn:"button",
            buttonImage:"images/new/calendar.png",
            buttonImageOnly:true
        });
    })
</script>
<div id="content">
<?php getMessages(); ?>
<div class="content_block" style="min-height: 700px">
    <h2><?php echo MENU6_1; ?></h2>
    <p><?php echo REPORT_TITLE2 ?></p>
    <form action=""  method="post" name="ff1">
        <input value="yes" name="show_reports" type="hidden"/>
        <div class="form-row">
            <h3><?php echo REPORTS_1 ?></h3>
            <div class="cell third">
                <label><?php echo BOOKING_FRM_SERVICE ?></label>
                <select name="serviceID" class="select" id="serviceID" >
                    <?php
                    $sql = "SELECT * FROM bs_services";
                    $res = $mysqli->query($sql);
                    while ($row = $res->fetch_assoc()) {
                        ?>
                        <option value="<?php echo $row['id']?>" <?php echo ($serviceID == $row['id']) ? "selected" : ""?>><?php echo $row['name']?></option>
                        <?php } ?>
                </select>
            </div>
            <div class="clear"></div>
            <div class="form-row" style="padding-left:0px;">
                <h3><?php echo REPORTS_2 ?></h3>
                <div class="dates-row no_padding">
                    <label><?php echo REPORTS_6 ?>:</label>
                    <input type="text" name="dateFrom" id="dateFrom" class='dateInput left datepicker'
                           value="<?php echo _date($dateFrom) ?>"/>
                </div>
                <div class="dates-row no_padding">
                    <label><?php echo REPORTS_7 ?>:</label>
                    <input type="text" name="dateTo" id="dateTo" class='dateInput left datepicker'
                           value="<?php echo _date($dateTo) ?>"/>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="clear"></div>         
        </div>
        <hr/>
        <button id="ViewReportsSlideCE" class="save" type="submit"><span><?php echo REPORTS_3 ?></span></button>
        <div class="errors-reports" ></div>
    </form>
    <div class="bw-reports-wrapper" id="EventsXY" >
    	<h3><?php echo REPORTS_4 ?> <span id="updateDateFrom" >DATE_1</span> to <span id="updateDateTo" >DATE_2</span> <?php echo REPORTS_9?> <span id="forService" >FOR_SERVICE</span></h3>
    	<h4 class="tipReports" ><strong id="disableTip_page-reports_order_1" style="color: red;cursor: pointer;" class="rememberOption" title="Don't show this again." >x&nbsp;&nbsp;</strong><?php echo REPORTS_DESCR?></h4>
    	<div id="graph" style="width: 905px;height: 300px;" ></div>
    	<div class="extra style_checkboxes" ></div>
    </div>
</div>
<?php include "includes/admin_footer.php";?>
<?php } ?>