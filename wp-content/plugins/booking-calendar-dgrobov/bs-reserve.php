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


if ($_SESSION["logged_in"] != true) {
    header("Location: admin.php");
} else {

    bw_do_action("bw_load");
    bw_do_action("bw_admin");

    ######################### DO NOT MODIFY (UNLESS SURE) END ########################
    //show page only if admin access level
    //request all neccessary variables for user update action.
    $id = (!empty($_REQUEST["id"])) ? strip_tags(str_replace("'", "`", $_REQUEST["id"])) : '';
    $reason = (!empty($_REQUEST["reason"])) ? strip_tags(str_replace("'", "`", $_REQUEST["reason"])) : '';
    $reserveDateFrom = (!empty($_REQUEST["reserveDateFrom"])) ? strip_tags(str_replace("'", "`", $_REQUEST["reserveDateFrom"])) : '';
    $reserveDateTo = (!empty($_REQUEST["reserveDateTo"])) ? strip_tags(str_replace("'", "`", $_REQUEST["reserveDateTo"])) : $reserveDateFrom;
    $serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : '1';
    $qty = (!empty($_REQUEST["qty"])) ? strip_tags(str_replace("'", "`", $_REQUEST["qty"])) : '1';

    $recurring = (isset($_REQUEST["recurring"])) ? strip_tags(str_replace("'", "`", $_REQUEST["recurring"])) : '0';
    $repeate_interval = (!empty($_REQUEST["repeate_interval"])) ? strip_tags(str_replace("'", "`", $_REQUEST["repeate_interval"])) : '1';
    $repeate = (!empty($_REQUEST["repeate"])) ? strip_tags(str_replace("'", "`", $_REQUEST["repeate"])) : '';


    $x1_from_h = (!empty($_REQUEST["1_from_h"])) ? strip_tags(str_replace("'", "`", $_REQUEST["1_from_h"])) : '';
    $x1_from_m = (!empty($_REQUEST["1_from_m"])) ? strip_tags(str_replace("'", "`", $_REQUEST["1_from_m"])) : '';

    $x2_from_h = (!empty($_REQUEST["2_from_h"])) ? strip_tags(str_replace("'", "`", $_REQUEST["2_from_h"])) : '';
    $x2_from_m = (!empty($_REQUEST["2_from_m"])) ? strip_tags(str_replace("'", "`", $_REQUEST["2_from_m"])) : '';

    $x1_from = $x1_from_h . ":" . $x1_from_m;
    $x2_from = $x2_from_h . ":" . $x2_from_m;

    //"edit page" action processing.
    if (!empty($_REQUEST["edit_page"]) && $_REQUEST["edit_page"] == "yes") {

        if (!empty($reason) && !empty($reserveDateFrom) && !empty($reserveDateTo) && !empty($serviceID)) {

            if (!checkSchedule($reserveDateFrom, $reserveDateTo, $x1_from, $x2_from, $serviceID, $qty, $id) && !$recurring) {

                //$msg = "This time booked!";
                addMessage(MSG_TMBK, "warning");
            } elseif ((strtotime("$reserveDateTo $x2_from") < strtotime("$reserveDateFrom $x1_from")) && !$recurring) {

                //$msg = "Reserved Date To earlier than the minimum interval";
                addMessage(MSG_DATETO2);
            } elseif ((date("Y-m-d", strtotime($reserveDateTo)) < date("Y-m-d", strtotime($reserveDateFrom . " +$repeate_interval $repeate"))) && $recurring) {

                //$msg = "Reserved Date To earlier than the minimum interval";
                addMessage(MSG_DATETO1);
            } else {
                //$msg .= "Booking was successfully updated!";
                //addMessage("Booking was successfully updated!","success");
                if (!empty($id)) {
                    // DELETE ALL RECORDS for this booking
                    $q = "DELETE FROM bs_reserved_time WHERE id='" . $id . "'";
                    $mysqli->query($q);
                    $q = "DELETE FROM bs_reserved_time_items WHERE reservedID='" . $id . "'";
                    $mysqli->query($q);
                }

                //now create the record from scratch....
                $sql = "INSERT INTO bs_reserved_time (serviceID,dateCreated,reason,reserveDateFrom,reserveDateTo,qty,repeate,repeate_interval,recurring) 
				VALUES ('" . $serviceID . "','" . DATETIME . "','" . $reason . "','" . $reserveDateFrom . " " . $x1_from . "','" . $reserveDateTo . " " . $x2_from . "','" . $qty . "','" . $repeate . "','" . $repeate_interval . "','" . $recurring . "')";

                $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to create new booking.");
                $id = $mysqli->insert_id;
                //$msg = "Booking was successfully saved!";
                addMessage(MSG_BKSAVE, "success");
            }
        } else {
            //$msg = "All fields are required!";
            addMessage(ALLFIELDSREQ);
        }
    }

    //select editable user's info and show it for editor.
    $sSQL = "SELECT * FROM bs_reserved_time WHERE id='" . $id . "'";
    $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
    if ($row = $result->fetch_assoc()) {
        foreach ($row as $key => $value) {
            $$key = $value;
        }
    }
    $result->free_result();
    $maxQty = getServiceSettings($serviceID, 'show_multiple_spaces') ? getServiceSettings($serviceID, 'spaces_available') : 1;
    if (!empty($id)) {

        $x1_from_h = date("H", strtotime($reserveDateFrom));
        $x1_from_m = date("i", strtotime($reserveDateFrom));

        $reserveDateFrom = _date($reserveDateFrom);

        $x2_from_h = date("H", strtotime($reserveDateTo));
        $x2_from_m = date("i", strtotime($reserveDateTo));

        $reserveDateTo = _date($reserveDateTo);
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
    <script type="text/javascript">
        $(function() {
            $("#reserveDateFrom").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                showOn: "button",
                buttonImage: "images/new/calendar.png",
                buttonImageOnly: true
            });
            //$('#reserveDateFrom').datepicker('option', {dateFormat: "yy-mm-dd"});
            $("#reserveDateTo").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                showOn: "button",
                buttonImage: "images/new/calendar.png",
                buttonImageOnly: true
            });
            //$('#reserveDateTo').datepicker('option', {dateFormat: "yy-mm-dd"})


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
            qtyDrop = $('#qty').msDropdown({
                mainCSS: 'spin'
            });
        });

        function updateQTY(el) {
            var qty = $(el).find("option:selected").attr("rel");
            $('#qtyCont').html();
            var elem = '<select name="qty" id="qty" class="smaller" >';
            for (var i = 1; i <= qty; i++) {
                elem += "<option value='" + i + "'>" + i + "</option>";
            }
            elem += "</select>";
            $("#qtyCont").html(elem);
            $("#qty").msDropdown({
                mainCSS: 'spin'
            })
        };
    </script>
    <div id="content">




        <?php getMessages(); ?>
        <div class="content_block">
            <?php if (!empty($id)) { ?>
                <h2><?php echo EDIT_MAN_BOOK; ?> <a href="bs-reserve-view.php"><?php echo BACK_TO_LIST ?></a> </h2>
            <?php } else {
                    ?>
                <h2><?php echo ADD_MAN_BOOK; ?> <a href="bs-reserve-view.php"><?php echo BACK_TO_LIST ?></a> </h2>
            <?php }  ?>
            <p><?php echo TXT_BS_RESERVE_DESCR; ?></p>
            <form action="bs-reserve.php" enctype="multipart/form-data" method="post" name="ff1">

                <hr />
                <div class="form-row">
                    <div class="cell long">
                        <label><?php echo SHRT_DESCRPTN ?></label>
                        <input type="text" name="reason" id="reason" value="<?php echo $reason ?>" />
                    </div>
                    <div class="cell long">
                        <label><?php echo SEL_SERVICE ?></label>
                        <?php
                            $sql = "SELECT *,bs.id as sid FROM bs_services bs  INNER JOIN bs_service_settings bss ON bss.serviceId=bs.id";
                            $res = $mysqli->query($sql);
                            if ($res->num_rows > 0) {
                                ?>
                            <select name="serviceID" class="select" id="serviceID" onchange="updateQTY(this)">

                                <?php while ($row = $res->fetch_assoc()) { ?>
                                    <option value="<?php echo $row['sid'] ?>" rel="<?php echo $row['show_multiple_spaces'] ? $row['spaces_available'] : 1 ?>" <?php echo ($serviceID == $row['sid']) ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                        <img src='images/info.png' border="0" class="tipTip adj_add_events_tip" title=" <?php echo SELECT_SERVICE_RESERVE ?>" />
                    </div>
                    <div class="cell short" style="position: relative;top: 4px;">
                        <label><?php echo BOOKING_FRM_QTY ?></label>
                        <span id="qtyCont">
                            <select name="qty" id="qty" class='smaller'>
                                <?php for ($i = 1; $i <= $maxQty; $i++) { ?>
                                    <option value="<?php echo $i ?>" <?php echo $qty == $i ? "selected='selected'" : "" ?>><?php echo $i ?></option>
                                <?php } ?>
                            </select>
                        </span>
                    </div>

                    <div class="cell short" style="position: relative;top: 4px;">
                        <label><?php echo BOOKING_FRM_QTY ?></label>
                        <span id="qtyCont">
                            <select name="qty" id="qty" class='smaller'>
                                <?php for ($i = 1; $i <= $maxQty; $i++) { ?>
                                    <option value="<?php echo $i ?>" <?php echo $qty == $i ? "selected='selected'" : "" ?>><?php echo $i ?></option>
                                <?php } ?>
                            </select>
                        </span>
                    </div>
                </div>
                <hr />
                <div class="form-row adj_fit_to_685">
                    <h3>Reservation</h3>
                    <div class="dates-row">
                        <label>Starts:</label>
                        <input type="text" name="reserveDateFrom" class='small left' id="reserveDateFrom" value="<?php echo $reserveDateFrom ?>" />
                        <label>at:</label>
                        <div class="left">
                            <input type="text" name="1_from_h" value="<?php echo $x1_from_h; ?>" class="adjStartEnd adj_hrs_0" />

                        </div>
                        <label>:</label>
                        <div class="left">
                            <input type="text" name="1_from_m" value="<?php echo $x1_from_m; ?>" class="adjStartEnd adj_mins_0" />

                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="dates-row">
                        <label>Ends:</label>
                        <input type="text" name="reserveDateTo" class='small left' id="reserveDateTo" value="<?php echo $reserveDateTo ?>" />
                        <label>at:</label>
                        <div class="left">
                            <input type="text" name="2_from_h" value="<?php echo $x2_from_h; ?>" class="adjStartEnd adj_hrs_0" />

                        </div>
                        <label>:</label>
                        <div class="left">
                            <input type="text" name="2_from_m" value="<?php echo $x2_from_m; ?>" class="adjStartEnd adj_mins_0" />

                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>
                <hr />
                <div class="form-row">
                    <div class="cell medium">
                        <input type="checkbox" name="recurring" id="recurring" value="1" <?php echo $recurring ? "checked" : "" ?> />
                        &nbsp;<?php echo RECURRING_MDB ?>
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
                            <img src='images/info.png' border="0" class="tipTip adj_add_events_tip" title="<?php echo REPEAT_MSG ?>" />

                        </div>
                        <div class="cell short">
                            <label><?php echo EVERY ?></label>
                            <input type="text" name="repeate_interval" value="<?php echo $repeate_interval; ?>" class="adjStartEnd" />

                            <span id="adj_ev_SS"><span id="int_name"><?php echo empty($repeate) ? "day" : $repeate ?></span>s</span>

                        </div>
                        <div class="clear"></div>
                    </div>



                    <div class="clear"></div>
                </div>
                <hr />
                <?php /*<input type="submit" name="create" id="create" value="<?php echo ADM_BTN_SUBMIT;?>" />*/ ?>
                <button class="save" type="submit"><span><?php echo ADM_BTN_SUBMIT; ?></span></button>
                <input value="yes" name="edit_page" type="hidden" />
                <input value="<?php echo $id; ?>" name="id" type="hidden" />
            </form>
        </div>

        <?php include "includes/admin_footer.php"; ?>
    <?php } ?>