<?php
/******************************************************************************
#                         BookingWizz v5.3
#******************************************************************************
#      Author:     Convergine (http://www.convergine.com)
#      Website:    http://www.convergine.com
#      Support:    http://support.convergine.com
#      Version:     5.3
#
#      Copyright:   (c) 2009 - 2013  Convergine.com
#	   Icons from PixelMixer - http://pixel-mixer.com/basic_set/ and by Manuel Lopez - http://www.iconfinder.com/search/?q=iconset%3A48_px_web_icons
#
#*******************************************************************************/

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

    if (!empty($_REQUEST["edit_page"]) && $_REQUEST["edit_page"] == "yes") {

        include_once('includes/export/iCalGenerator.php');
        $iCal = new iCalGenerator();


        $serviceData = getService($serviceID);
        $org1 = !empty($serviceData['fromName'])?$serviceData['fromName']:"BookingWizz";
        $org2 = !empty($serviceData['fromEmail'])?$serviceData['fromEmail']:"noreply@".$_SERVER["HTTP_HOST"];


            $sql = "SELECT bri.*,bri.id as bid,s.type FROM bs_reserved_time bri
                INNER JOIN bs_services s ON s.id=bri.serviceID
                WHERE bri.reserveDateFrom >='$dateFrom' AND bri.reserveDateTo <='$dateTo' AND bri.serviceID='$serviceID' ";
            //print $sql;;

            $res = $mysqli->query($sql);
            while ($row = $res->fetch_assoc()) {

                $description = "Reason: {$row['reason']}";
                if($row['type']=='t'){
                    $iCal->startDate =$row['reserveDateFrom'];
                    $iCal->endDate = $row['reserveDateTo'];
                }else{
                    $iCal->startDate =_date($row['reserveDateFrom'])." 12:00:00";
                    $iCal->endDate = _date($row['reserveDateTo'])." 12:00:00";
                }

                $iCal->name = $serviceData['name'] . " | " . $row['reason'];
                $iCal->location = '';
                $iCal->description = $description;
                $iCal->mailFrom = $org1;
                $iCal->mailTo = $org2;
                $iCal->url = 'http://'.$_SERVER["HTTP_HOST"];

                $iCal->addEvent();

            }


        if ($iCal->countEvents > 0) {
            $file = $iCal->renderIcal('file');
            exit();
        }else{
            addMessage(NO_ATTENDEES,"warning");
        }
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

    <div class="content_block">
        <h2>
            <?php echo EXPORT_TITLE ?>

        </h2>

        <p><?php echo EXPORT_TITLE2 ?></p>

        <form action="bs-manual-attendees-export.php" enctype="multipart/form-data" method="post" name="ff1">

            <input value="yes" name="edit_page" type="hidden"/>

            <div class="form-row">

                <div class="cell third">

                    <label><?php echo BOOKING_FRM_SERVICE ?></label>
                    <select name="serviceID" class="select">
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
                    <h3><?php echo EXPORT_TITLE3 ?></h3>

                    <div class="dates-row no_padding">

                        <label>Starts:</label>
                        <input type="text" name="dateFrom" id="dateFrom" class='dateInput left datepicker'
                               value="<?php echo _date($dateFrom) ?>"/>
                    </div>
                    <div class="dates-row no_padding">
                        <label>Ends:</label>
                        <input type="text" name="dateTo" id="dateTo" class='dateInput left datepicker'
                               value="<?php echo _date($dateTo) ?>"/>

                        <div class="clear"></div>
                    </div>
                </div>


                <div class="clear"></div>

            </div>


            <hr/>
            <?php /*<input type="submit" name="create" id="create" value="<?php echo ADM_BTN_SUBMIT;?>" />*/ ?>
            <button class="save" type="submit"><span><?php echo ADM_BTN_SUBMIT;?></span></button>


        </form>
    </div>
    <?php include "includes/admin_footer.php";?>
<?php } ?>