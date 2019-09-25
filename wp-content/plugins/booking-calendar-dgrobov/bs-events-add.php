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

$msg = "";
$msg2 = "";
if ($_SESSION["logged_in"] != true) {
    header("Location: admin.php");
    exit();
} else {
    bw_do_action("bw_load");
    bw_do_action("bw_admin");

    ######################### DO NOT MODIFY (UNLESS SURE) END ########################

    //show page only if admin access level

    //request all neccessary variables for user update action.
    $id = (!empty($_REQUEST["id"])) ? strip_tags(str_replace("'", "`", $_REQUEST["id"])) : '';
    $title = (!empty($_REQUEST["title"])) ? strip_tags(str_replace("'", "`", $_REQUEST["title"])) : '';
    $spaces = (!empty($_REQUEST["spaces"])) ? strip_tags(str_replace("'", "`", $_REQUEST["spaces"])) : '';
    $max_qty = (!empty($_REQUEST["max_qty"])) ? strip_tags(str_replace("'", "`", $_REQUEST["max_qty"])) : '1';
    $allow_multiple = (!empty($_REQUEST["allow_multiple"])) ? strip_tags(str_replace("'", "`", $_REQUEST["allow_multiple"])) : '2';
    $description = (!empty($_REQUEST["description"])) ? strip_tags(str_replace("'", "`", $_REQUEST["description"])) : '';
    $entryFee = (!empty($_REQUEST["entryFee"])) ? strip_tags(str_replace("'", "`", $_REQUEST["entryFee"])) : '';
    $payment_required = (!empty($_REQUEST["payment_required"])) ? strip_tags(str_replace("'", "`", $_REQUEST["payment_required"])) : '';
    $eventDate = (!empty($_REQUEST["eventDate"])) ? strip_tags(str_replace("'", "`", $_REQUEST["eventDate"])) : '';
    $eventDateEnd = (!empty($_REQUEST["eventDateEnd"])) ? strip_tags(str_replace("'", "`", $_REQUEST["eventDateEnd"])) : '';
    $serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : '';
    $payment_method = (!empty($_REQUEST["payment_method"])) ? strip_tags(str_replace("'", "`", $_REQUEST["payment_method"])) : '';

    $name = (!empty($_REQUEST["name"])) ? strip_tags(str_replace("'", "`", $_REQUEST["name"])) : '';
    $email = (!empty($_REQUEST["email"])) ? strip_tags(str_replace("'", "`", $_REQUEST["email"])) : '';
    $qty_booking = (!empty($_REQUEST["qty_booking"])) ? strip_tags(str_replace("'", "`", $_REQUEST["qty_booking"])) : '';
    $bookDate = (!empty($_REQUEST["bookDate"])) ? strip_tags(str_replace("'", "`", $_REQUEST["bookDate"])) : '';
    $comments = (!empty($_REQUEST["comments"])) ? strip_tags(str_replace("'", "`", $_REQUEST["comments"])) : '';
    $phone = (!empty($_REQUEST["phone"])) ? strip_tags(str_replace("'", "`", $_REQUEST["phone"])) : '';
    $status_add = (!empty($_REQUEST["status_add"])) ? strip_tags(str_replace("'", "`", $_REQUEST["status_add"])) : '';

    $status_upd = (!empty($_POST["status_upd"])) ? $_POST["status_upd"] : '';
    $old_status_upd = (!empty($_POST["old_status_upd"])) ? $_POST["old_status_upd"] : '';

    $color = (!empty($_REQUEST["color"])) ? strip_tags(str_replace("'", "`", $_REQUEST["color"])) : '';

    $recurring = (isset($_REQUEST["recurring"])) ? strip_tags(str_replace("'", "`", $_REQUEST["recurring"])) : '0';
    $repeate_interval = (!empty($_REQUEST["repeate_interval"])) ? strip_tags(str_replace("'", "`", $_REQUEST["repeate_interval"])) : '1';
    $repeate = (!empty($_REQUEST["repeate"])) ? strip_tags(str_replace("'", "`", $_REQUEST["repeate"])) : '';
    $recurringEndDate = (!empty($_REQUEST["recurringEndDate"])) ? strip_tags(str_replace("'", "`", $_REQUEST["recurringEndDate"])) : '';

    $coupon = (isset($_REQUEST["coupon"])) ? strip_tags(str_replace("'", "`", $_REQUEST["coupon"])) : '0';

    $location = (isset($_REQUEST["location"])) ? strip_tags(str_replace("'", "`", $_REQUEST["location"])) : '';
    $map_link = (isset($_REQUEST["map_link"])) ? strip_tags(str_replace("'", "`", $_REQUEST["map_link"])) : '';

    $deposit = (isset($_REQUEST["deposit"])) ? strip_tags(str_replace("'", "`", $_REQUEST["deposit"])) : '';

    $hh = (!empty($_POST["hh"])) ? $_POST["hh"] : '';
    $mm = (!empty($_POST["mm"])) ? $_POST["mm"] : '';
    $hh1 = (!empty($_POST["hh1"])) ? $_POST["hh1"] : '';
    $mm1 = (!empty($_POST["mm1"])) ? $_POST["mm1"] : '';

    $eventTime = $hh != '' && $mm != '' ? $hh . ":" . $mm : "00:00";
    $eventTimeEnd = $hh1 != '' && $mm1 != '' ? $hh1 . ":" . $mm1 : "00:00";


    ########################################################################################################################################################
    if (isset($_GET['delImg']) && $_GET['delImg'] == 'yes' && !empty($_GET['id'])) {

        $sql = "SELECT path FROM bs_events WHERE id='" . $id . "'";
        $result = $mysqli->query($sql) or die("oopsy, error when tryin to get images 1");
        @unlink($_SERVER['DOCUMENT_ROOT'] . $baseDir . mysqli_result($result, 'path'));

        $sSQL = "UPDATE bs_events SET path='' WHERE id='" . $id . "'";
        $mysqli->query($sSQL) or die("Invalid query: " . $mysqli->error() . " - $sSQL");
    }
    ########################################################################################################################################################
    //"edit page" action processing.
    if (!empty($_REQUEST["edit_page"]) && $_REQUEST["edit_page"] == "yes") {
        if ((date("Y-m-d", strtotime($recurringEndDate)) < date("Y-m-d", strtotime($eventDate . " +$repeate_interval $repeate"))) && $recurring) {
            addMessage(EVENT_END_RECURING);
        } elseif (!empty($eventTime) && !empty($title) && !empty($spaces) && !empty($description) && !empty($eventDate) && !empty($allow_multiple) && $eventDate != 'YYYY-MM-DD') {

            $eventDateEnd = ($eventDateEnd == 'YYYY-MM-DD' ? $eventDate : $eventDateEnd) . " " . $eventTimeEnd;
            $eventDate = $eventDate . " " . $eventTime;

            if (strtotime($eventDate) < strtotime($eventDateEnd)) {
                $msg = EVENT_SUC_MSG;


                if (empty($id)) {
                    $sql = "INSERT INTO bs_events (serviceID,eventDate,eventDateEnd,title,spaces,description,entryFee,payment_method,payment_required,max_qty,allow_multiple,repeate,repeate_interval,recurring,recurringEndDate,coupon)
							VALUES ('" . $serviceID . "','" . $eventDate . "','" . $eventDateEnd . "','" . $title . "','" . $spaces . "','" . $description . "','" . $entryFee . "','" . $payment_method . "','" . $payment_required . "','" . $max_qty . "','" . $allow_multiple . "','" . $repeate . "','" . $repeate_interval . "','" . $recurring . "','" . $recurringEndDate . "','" . $coupon . "')";

                    $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to create new event." . "<br>" . $sql . "<br>" . $mysqli->error());
                    $id = $mysqli->insert_id;

                    $msg = EVENT_SUC_UPD;
                }


                if (!empty($_FILES['picture']['name'])) {
                    $name = mktime();
                    $imgPathUrl == null;
                    $photoFileNametmp = $_FILES['picture']['name'];
                    $fileNamePartstmp = explode(".", $photoFileNametmp);
                    $counter2 = count($fileNamePartstmp) - 1;
                    $fileExtensiontmp = strtolower($fileNamePartstmp[$counter2]); // part behind last dot

                    if ($demo) {
                        $imgPath = $baseDir . "images/defaultEvent.jpg";
                    } else {
                        $imgPath = uploadFile($_FILES['picture'], $_SERVER['DOCUMENT_ROOT'] . $baseDir . "uploads/" . $name . "." . $fileExtensiontmp);

                        if (!$imgPath['error']) {
                            $imgPathUrl = "uploads/" . $name . "." . $fileExtensiontmp;


                            $sql = "SELECT path FROM bs_events WHERE id='" . $id . "'";
                            $result = $mysqli->query($sql) or die("oopsy, error when tryin to get images 1");
                            @unlink($_SERVER['DOCUMENT_ROOT'] . $baseDir . mysqli_result($result, 'path'));

                            $sSQL = "UPDATE bs_events SET path='" . $imgPathUrl . "' WHERE id='" . $id . "'";
                            $mysqli->query($sSQL) or die("Invalid query: " . $mysqli->error() . " - $sSQL");
                        } else {
                            addMessage($imgPath['error'], "error");
                        }
                    }
                }
                addMessage($msg, "success");
                $sql = "UPDATE bs_events SET title='" . $title . "',
                        eventDate='" . $eventDate . "',
                        eventDateEnd='" . $eventDateEnd . "',
                        eventTime='" . $eventTime . "',
                        serviceID='" . $serviceID . "',
                        spaces='" . $spaces . "',
                        description='" . $description . "',
                        entryFee='" . $entryFee . "',
                        payment_method='" . $payment_method . "',
                        payment_required='" . $payment_required . "',
                        max_qty='" . $max_qty . "',
                        recurringEndDate='" . $recurringEndDate . "',    
                        recurring='" . $recurring . "',
                        repeate_interval='" . $repeate_interval . "',
                        repeate='" . $repeate . "',
                        coupon='" . $coupon . "',  
                        location='" . $location . "', 
                        map_link='" . $map_link . "',
                        color='" . $color . "',
                        deposit='" . ($deposit / 100) . "',
			            allow_multiple='" . $allow_multiple . "' WHERE id='" . $id . "'";
                $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to update event." . $sql . "<br>" . $mysqli->error());


                $sql = "UPDATE bs_reservations SET serviceID='{$serviceID}' WHERE eventID='{$id}' ";
                $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to update event.");

                bw_do_action("update_event", $id);
            } else {


                addMessage(EVENT_STR_TIME, "error");

                $requiredFields['eventDate'] = 'error';
                $requiredFields['eventDateEnd'] = 'error';
            }
        } else {

            addMessage(ALLFIELDSREQ, "error");
            $requiredFields = array(
                "eventDate" => "",
                "eventDateEnd" => "",
                "description" => "",
                "title" => "",
                "spaces" => ""
            );
            if (empty($eventDate) || $eventDate == 'YYYY-MM-DD') {
                $requiredFields['eventDate'] = 'error';
            }
            if (empty($eventDateEnd) || $eventDateEnd == 'YYYY-MM-DD') {
                $requiredFields['eventDateEnd'] = 'error';
            }
            if (empty($description)) {
                $requiredFields['description'] = 'error';
            }
            if (empty($title)) {
                $requiredFields['title'] = 'error';
            }
            if (empty($spaces)) {
                $requiredFields['spaces'] = 'error';
            }
        }
    }
    ########################################################################################################################################################


    ###################################################################################################################################################


    //select event info and show it for editor.
    $sSQL = "SELECT * FROM bs_events WHERE id='" . $id . "'";
    $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
    if ($row = $result->fetch_assoc()) {
        foreach ($row as $key => $value) {
            $$key = $value;
        }
    }
    $result->free_result();

    if (!empty($eventDate)) {
        $timetmp = explode(" ", $eventDate);
        $timetmp = explode(":", $timetmp[1]);
    } else {
        $timetmp = array();
        $timetmp[0] = $hh;
        $timetmp[1] = $mm;
    }

    if (!empty($eventDateEnd)) {
        $timetmp1 = explode(" ", $eventDateEnd);
        $timetmp1 = explode(":", $timetmp1[1]);
    } else {
        $timetmp1 = array();
        $timetmp1[0] = $hh1;
        $timetmp1[1] = $mm1;
    }


    ?>

    <?php include "includes/admin_header.php"; ?>
    <?php if (!$recurring) { ?>
        <style>
            .lock.recurring {
                display: block;
            }
        </style>
    <?php } ?>
    <?php if ($allow_multiple == "2") { ?>
        <style>
            .lock.multiqty {
                display: block;
            }
        </style>
    <?php } ?>

    <link type="text/css" href="./js/datatable/css/jquery.dataTables.css" rel="stylesheet" />
    <script type="text/javascript" src="./js/datatable/js/jquery.dataTables.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#grid').dataTable({
                "sPaginationType": "full_numbers",
                "aoColumns": [{
                        "bSortable": false
                    },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    {
                        "bSortable": false
                    },
                    {
                        "bSortable": false
                    }

                ]
            });
            $("#grid_wrapper").append('<div class="deleteBtton"><input name="delete_files" type="submit" value="Update Statuses"  /></div>');
        });
    </script>
    <link type="text/css" href="./css/jquery.minicolors.css" rel="stylesheet" />
    <script type="text/javascript" src="./js/jquery.minicolors.js"></script>
    <script type="text/javascript">
        $(function() {
            $("#eventDate").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                showOn: "button",
                buttonImage: "images/new/calendar.png",
                buttonImageOnly: true
            });
            $("#eventDateEnd").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                showOn: "button",
                buttonImage: "images/new/calendar.png",
                buttonImageOnly: true
            });
            $("#recurringEndDate").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                showOn: "button",
                buttonImage: "images/new/calendar.png",
                buttonImageOnly: true
            });
            $("#bookDate").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                showOn: "button",
                buttonImage: "images/new/calendar.png",
                buttonImageOnly: true
            });
            //$('#reserveDateFrom').datepicker('option', {dateFormat: "yy-mm-dd"});

            $("#recurring").bind("change", function() {

                if ($(this).is(':checked')) {
                    $('.recurring').hide();
                } else {
                    $('.recurring').show();
                }
            })
            $("#repeate").bind("change", function() {

                $("#int_name").html($(this).val());
            })
            $("input[name='allow_multiple']").click(function() {
                if ($(this).val() == 1) {
                    $(".multiqty").hide();
                } else {
                    $(".multiqty").show();
                }
            });
            var settings = {
                animationSpeed: 100,
                animationEasing: 'swing',
                change: null,
                changeDelay: 0,
                control: 'hue',
                defaultValue: '',
                hide: null,
                hideSpeed: 100,
                inline: false,
                letterCase: 'lowercase',
                opacity: false,
                position: 'default',
                show: null,
                showSpeed: 100,
                swatchPosition: 'left',
                textfield: true,
                theme: 'default'
            }
            $('#color').minicolors(settings);
        });
    </script>

    <div id="content">




        <?php getMessages(); ?>
        <div class="content_block">
            <?php if (!empty($id)) { ?>
                <h2><?php echo EDIT_EVENT; ?> <a href="bs-events.php?serviceID=<?php echo $serviceID ?>"><?php echo MSG_BACK ?></a> </h2>
            <?php } else {
                    ?>
                <h2><?php echo ADD_EVENT; ?> <a href="bs-events.php?serviceID=<?php echo $serviceID ?>"><?php echo MSG_BACK ?></a> </h2>
            <?php }  ?>
            <p><?php echo BS_EVENT_ADD_TXT ?></p>
            <hr />
            <form action="bs-events-add.php" enctype="multipart/form-data" method="post" name="ff1">
                <div class="form-row add_events">
                    <div class="cell third">
                        <label><?php echo EVENT_TTL ?>*</label>
                        <input type="text" name="title" id="title" value="<?php echo $title ?>" class="<?php echo $requiredFields['title'] ?>" />
                    </div>
                    <div class="cell third">
                        <label><?php echo SEL_SERVICE ?></label>

                        <select name="serviceID" class="select" id="serviceID">
                            <?php
                                $sql = "SELECT * FROM bs_services";
                                $res = $mysqli->query($sql);
                                while ($row = $res->fetch_assoc()) {
                                    ?>
                                <option value="<?php echo $row['id'] ?>" <?php echo ($serviceID == $row['id']) ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </select>
                        <img src='images/info.png' border="0" class="tipTip adj_add_events_tip" title=" <?php echo SELECT_SERVICE ?>" />
                    </div>
                    <div class="cell third">
                        <label><?php echo EVENT_COLOR ?></label>
                        <input type="text" name="color" id="color" value="<?php echo $color ?>" class="small" />
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="form-row">
                    <div class="cell">
                        <label><?php echo EVENT_DISCRP ?>*</label>
                        <textarea name="description" id="description" style="width: 660px;height: 150px;" class="<?php echo $requiredFields['description'] ?>"><?php echo $description ?></textarea>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-row adj_not_so_long">
                    <div class="cell half">
                        <label><?php echo LOCATION ?></label>
                        <input type="text" name="location" id="location" value="<?php echo $location ?>" class="long" />
                    </div>
                    <div class="cell half">
                        <label><?php echo MAP_LINK ?></label>
                        <input type="text" name="map_link" id="map_link" value="<?php echo $map_link ?>" class="long" />
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="form-row">
                    <div class="cell">
                        <label><?php echo EVENT_IMAGE ?></label>
                        <input type="file" name="picture" class="txt" />
                        <img src='images/info.png' border="0" class="tipTip" title="<?php echo IMGJPG ?>" />
                    </div>
                    <div class="clear"></div>
                    <?php if (isset($path) && $path != '') { ?>
                        <div class="cell">
                            <label><?php echo CRNT_EV_IMG ?></label>

                            <a href="bs-events-add.php?id=<?php echo $id ?>&delImg=yes"><?php echo DEL_IMG ?></a><br />
                            <img height="100" src="<?php echo $path ?>" />
                        </div>
                    <?php } ?>
                    <div class="clear"></div>
                </div>
                <hr />

                <div class="form-row adj_fit_to_685">
                    <h3><?php echo EVENT_START_END ?>*</h3>
                    <div class="dates-row">
                        <label><?php echo TXT_COUPONS_STARTS ?>:</label>
                        <input type="text" name="eventDate" id="eventDate" class="small left <?php echo $requiredFields['eventDate'] ?>" value="<?php echo (!empty($eventDate) && $eventDate != 'YYYY-MM-DD' ? date("Y-m-d", strtotime($eventDate)) : "YYYY-MM-DD") ?>" />
                        <label><?php echo SYL_AT ?></label>
                        <div class="left">
                            <input type="text" name="hh" value="<?php echo $timetmp[0]; ?>" class="adjStartEnd adj_hrs_0" />

                        </div>
                        <label>:</label>
                        <div class="left">
                            <input type="text" name="mm" value="<?php echo $timetmp[1]; ?>" class="adjStartEnd adj_mins_0" />

                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="dates-row">
                        <label><?php echo TXT_COUPONS_ENDS ?>:</label>
                        <input type="text" name="eventDateEnd" id="eventDateEnd" class="small left <?php echo $requiredFields['eventDateEnd'] ?>" value="<?php echo (!empty($eventDateEnd) && $eventDateEnd != 'YYYY-MM-DD' ? date("Y-m-d", strtotime($eventDateEnd)) : "YYYY-MM-DD") ?>" />
                        <label><?php echo SYL_AT ?></label>
                        <div class="left">
                            <input type="text" name="hh1" value="<?php echo $timetmp1[0]; ?>" class="adjStartEnd adj_hrs_0" />

                        </div>
                        <label>:</label>
                        <div class="left">
                            <input type="text" name="mm1" value="<?php echo $timetmp1[1]; ?>" class="adjStartEnd adj_mins_0" />

                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>
                <hr />
                <div class="form-row">
                    <div class="cell short1">
                        <label><?php echo MAX_SPACE ?>*</label>

                        <input type="text" name="spaces" id="spaces" class="small1 left <?php echo $requiredFields['spaces'] ?>" value="<?php echo $spaces ?>" onkeyup="noAlpha(this)" />
                        <img src='images/info.png' border="0" class="left tipTip imgCenter" title="<?php echo NUMB_PLZ ?>" />


                    </div>
                    <div class="cell short1">
                        <label><?php echo PRICE ?>&nbsp;<?php echo getOption('currency') ?></label>

                        <input type="text" name="entryFee" id="entryFee" value="<?php echo $entryFee ?>" class="small1 left" onkeyup="noAlpha(this)" />
                        <img src='images/info.png' border="0" class="left tipTip imgCenter" title="<?php echo NUMB_PLZ ?>" />


                    </div>
                    <div class="cell short1">
                        <label><?php echo REQUIRED_DEPOSIT ?>&nbsp;%</label>

                        <input type="text" name="deposit" id="deposit" value="<?php echo $deposit * 100 ?>" class="small1 left" onkeyup="noAlpha(this)" />
                        <img src='images/info.png' border="0" class="left tipTip imgCenter" title="<?php echo NUMB_PLZ ?>" />


                    </div>
                    <div class="cell medium1">
                        <label><?php echo PAY_METD ?></label>


                        <select name="payment_method" class="select medium left">
                            <?php
                                $paymentMethosList = unserialize(getOption("payment_methods"));
                                foreach ($paymentMethosList as $key => $value) {
                                    ?>
                                <option value="<?php echo $key ?>" <?php echo $payment_method == $key ? "selected" : "" ?>><?php echo $value ?></option>
                            <?php } ?>
                        </select>
                        <img src='images/info.png' border="0" class=" tipTip imgCenter" title=" <?php echo OFFL_INVC_MSG ?>" />


                    </div>
                    <div class="cell medium3">
                        <label><?php echo PAYMT ?></label>
                        <div class="valign">
                            <input type="radio" name="payment_required" value="1" <?php echo $payment_required == "1" ? "checked" : "" ?> />
                            <?php echo REC ?>
                            <input type="radio" name="payment_required" value="2" <?php echo $payment_required == "2" ? "checked" : "" ?> <?php
                                                                                                                                                if (empty($payment_required)) {
                                                                                                                                                    echo "checked";
                                                                                                                                                }
                                                                                                                                                ?> />

                            <?php echo NOTREC ?>
                        </div>
                    </div>
                </div>
                <div class="form-row">

                    <div class="cell short1">
                        <input type="checkbox" name="recurring" id="recurring" value="1" <?php echo $recurring ? "checked" : "" ?> />
                        &nbsp;<?php echo RECURRING ?>
                    </div>
                    <div class="disabled left">
                        <div class="lock passive recurring"></div>
                        <div class="cell medium1">
                            <label><?php echo REP ?></label>

                            <select name="repeate" id="repeate" class='small select'>
                                <option value="day" <?php echo $repeate == 'day' ? "selected='selected'" : "" ?>><?php echo DAILY ?></option>
                                <option value="week" <?php echo $repeate == 'week' ? "selected='selected'" : "" ?>><?php echo WEEKLY ?></option>
                                <option value="month" <?php echo $repeate == 'month' ? "selected='selected'" : "" ?>><?php echo MONTHLY ?></option>
                                <option value="year" <?php echo $repeate == 'year' ? "selected='selected'" : "" ?>><?php echo YEARLY ?></option>

                            </select>
                            <img src='images/info.png' border="0" class="tipTip imgCenter" title="<?php echo REPEAT_MSG2 ?>" />

                        </div>
                        <div class="cell short">
                            <label><?php echo EVERY ?></label>
                            <input type="text" name="repeate_interval" value="<?php echo $repeate_interval; ?>" class="adjStartEnd" />

                            <span id="adj_ev_SS"><span id="int_name"><?php echo empty($repeate) ? "day" : $repeate ?></span>s</span>

                        </div>
                        <div class="cell">
                            <label><?php echo EVENT_END_RECURR ?></label>
                            <input type="text" name="recurringEndDate" id="recurringEndDate" class="small left" value="<?php echo (!empty($recurringEndDate) && $recurringEndDate != "0000-00-00" ? date("Y-m-d", strtotime($recurringEndDate)) : "") ?>" />
                            <img src='images/info.png' border="0" class="tipTip imgCenter adj_absl-20" title='<?php echo htmlspecialchars(REPEAT_MSG2) ?>' />


                        </div>
                        <div class="clear"></div>
                    </div>




                    <div class="clear"></div>
                </div>
                <hr />
                <div class="form-row">
                    <div class="cell half" style="width:380px;">
                        <label><?php echo TCT_QNTT ?></label>
                        <div class="valign">
                            <input type="radio" name="allow_multiple" value="1" <?php echo $allow_multiple == "1" ? "checked='checked'" : "" ?> />
                            <?php echo MLTP_TCT_CSTM ?>
                            <input type="radio" name="allow_multiple" value="2" <?php echo $allow_multiple == "2" ? "checked='checked'" : "" ?> <?php
                                                                                                                                                    if (empty($allow_multiple)) {
                                                                                                                                                        echo "checked";
                                                                                                                                                    }
                                                                                                                                                    ?> />
                            <?php echo ONE_TCT_CSTM ?>
                        </div>
                    </div>
                    <div class="cell short1">
                        <div class="disabled left">
                            <div class="lock multiqty"></div>
                            <label><?php echo MXM_TCT ?></label>
                            <input type="text" name="max_qty" id="max_qty" value="<?php echo $max_qty ?>" class="small1 left" onkeyup="noAlpha(this)" />
                            <img src='images/info.png' border="0" class="tipTip imgCenter adj_absl-20" title="<?php echo TCT_MSG ?>" />
                        </div>
                    </div>
                    <div class="cell " style="margin-left:7px;">
                        <label>&nbsp;</label>
                        <div class="valign">
                            <input type="checkbox" name="coupon" id="coupon" value="1" <?php echo $coupon ? "checked" : "" ?> />
                            <?php echo MXM_COUPON ?>
                            <img src='images/info.png' border="0" style="position: relative;top: 3px;" class="tipTip" title="<?php echo TTIP_1 ?>" />
                        </div>

                    </div>
                    <div class="clear"></div>
                    <?php bw_do_action('event_form', $id) ?>
                </div>


                <hr />
                <?php /*<input type="submit" name="create" id="create" value="<?php echo ADM_BTN_SUBMIT;?>" />*/ ?>
                <button class="save" type="submit"><span><?php echo ADM_BTN_SUBMIT; ?></span></button>
                <input value="yes" name="edit_page" type="hidden" />
                <input value="<?php echo $id; ?>" name="id" type="hidden" />
            </form>



            <?php include "includes/admin_footer.php"; ?>
        <?php }  ?>