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

    if (!empty($_REQUEST["edit_page"]) && $_REQUEST["edit_page"] == "yes") {

        include_once('includes/export/iCalGenerator.php');
        $iCal = new iCalGenerator();

        $serviceData = getService($serviceID);
        $org1 = !empty($serviceData['fromName']) ? $serviceData['fromName'] : "BookingWizz";
        $org2 = !empty($serviceData['fromEmail']) ? $serviceData['fromEmail'] : "noreply@" . $_SERVER["HTTP_HOST"];

        if ($events) {
            $SQL = "SELECT *,br.id as bid FROM bs_reservations br
                INNER JOIN bs_events be ON be.id=br.eventID
                WHERE (br.serviceID='$serviceID' AND br.date >='{$dateFrom}' AND br.date <= '{$dateTo}' AND be.recurring=1) OR
                (br.serviceID='$serviceID' AND be.eventDate >='{$dateFrom}' AND be.eventDate <= '{$dateTo}' AND be.recurring=0)  AND br.status IN('1','4')";
            //print $SQL;
            $res = $mysqli->query($SQL);
            while ($row = $res->fetch_assoc()) {
                $startTime = $row['recurring'] ? $row['date'] . _time($row['eventDate']) : $row['eventDate'];
                $endTime = $row['recurring'] ? $row['date'] . _time($row['eventDateEnd']) : $row['eventDateEnd'];
                $description = "Email: {$row['email']} | Phone: {$row['phone']} | QTY: {$row['qty']}" . (!empty($row['comments']) ? " | '{$row['comments']}'" : "");

                $iCal->startDate = $startTime;
                $iCal->endDate = $endTime;
                $iCal->name = $serviceData['name'] . " | " . $row['name'];
                $iCal->location = $row['location'];
                $iCal->description = $description;
                $iCal->mailFrom = $org1;
                $iCal->mailTo = $org2;
                $iCal->url = 'http://' . $_SERVER["HTTP_HOST"];

                $iCal->addEvent();
            }
        }
        if ($tbookings) {
            $sql = "SELECT bri.*,br.*,bri.id as bid,s.type FROM bs_reservations_items bri
                INNER JOIN bs_reservations br ON br.id= bri.reservationID
                INNER JOIN bs_services s ON s.id=br.serviceID
                WHERE bri.reserveDateFrom >='$dateFrom' AND bri.reserveDateTo <='$dateTo' AND br.serviceID='$serviceID' AND br.status IN('1','4')";
            //AND s.type='t'";

            $res = $mysqli->query($sql);
            while ($row = $res->fetch_assoc()) {

                $description = "Email: {$row['email']} | Phone: {$row['phone']} | QTY: {$row['qty']}" . (!empty($row['comments']) ? " | '{$row['comments']}'" : "");

                if ($row['type'] == 't') {
                    $iCal->startDate = $row['reserveDateFrom'];
                    $iCal->endDate = $row['reserveDateTo'];
                } else {
                    $iCal->startDate = _date($row['reserveDateFrom']) . " 12:00:00";
                    $iCal->endDate = _date($row['reserveDateTo']) . " 12:00:00";
                }
                $iCal->name = $serviceData['name'] . " | " . $row['name'];
                $iCal->location = '';
                $iCal->description = $description;
                $iCal->mailFrom = $org1;
                $iCal->mailTo = $org2;
                $iCal->url = 'http://' . $_SERVER["HTTP_HOST"];

                $iCal->addEvent();
            }
        }

        if ($iCal->countEvents > 0) {
            $file = $iCal->renderIcal('file');
            exit();
        } else {
            addMessage(NO_ATTENDEES, "warning");
        }
    }


    include "includes/admin_header.php"; ?>
<script type="text/javascript">
    $(function() {
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd",
            showOn: "button",
            buttonImage: "images/new/calendar.png",
            buttonImageOnly: true
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

        <form action="bs-attendees-export.php" enctype="multipart/form-data" method="post" name="ff1">

            <input value="yes" name="edit_page" type="hidden" />

            <div class="form-row">

                <div class="cell third">

                    <label><?php echo BOOKING_FRM_SERVICE ?></label>
                    <select name="serviceID" class="select">
                        <?php
                            $sql = "SELECT * FROM bs_services";
                            $res = $mysqli->query($sql);
                            while ($row = $res->fetch_assoc()) {
                                ?>
                        <option value="<?php echo $row['id'] ?>" <?php echo ($serviceID == $row['id']) ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clear"></div>
                <div class="form-row" style="padding-left:0px;">
                    <h3><?php echo EXPORT_TITLE3 ?></h3>

                    <div class="dates-row no_padding">

                        <label>Starts:</label>
                        <input type="text" name="dateFrom" id="dateFrom" class='dateInput left datepicker' value="<?php echo _date($dateFrom) ?>" />
                    </div>
                    <div class="dates-row no_padding">
                        <label>Ends:</label>
                        <input type="text" name="dateTo" id="dateTo" class='dateInput left datepicker' value="<?php echo _date($dateTo) ?>" />

                        <div class="clear"></div>
                    </div>
                </div>


                <div class="clear"></div>
                <div class="form-row">

                    <div class="dates-row no_padding" style="background:#ffffff;">
                        <label>&nbsp;</label>

                        <div class="valign no_padding">
                            <input type="checkbox" value="1" id="events" name="events">
                            <?php echo EVENT_BOOKINGS ?>
                        </div>

                    </div>
                    <div class="dates-row no_padding" style="background:#ffffff;">
                        <label>&nbsp;</label>

                        <div class="valign no_padding thrid">
                            <input type="checkbox" value="1" id="tbookings" name="tbookings">
                            <?php echo BOOKINGS_EXPRT ?>
                        </div>

                    </div>
                    <?php /* <div class="cell third">
                        <label>&nbsp;</label>
                            <div class="valign">
                                <input type="checkbox" value="1" id="dbookings" name="dbookings">
                                Day-bookings
                            </div>

                        </div>*/ ?>
                    <div class="clear"></div>
                </div>
            </div>


            <hr />
            <?php /*<input type="submit" name="create" id="create" value="<?php echo ADM_BTN_SUBMIT;?>" />*/ ?>
            <button class="save" type="submit"><span><?php echo ADM_BTN_SUBMIT; ?></span></button>


        </form>
    </div>
    <?php include "includes/admin_footer.php"; ?>
    <?php } ?>