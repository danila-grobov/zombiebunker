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




    //"edit page" action processing.
    if (!empty($_REQUEST["edit_page"]) && $_REQUEST["edit_page"] == "yes") {

        $userBookings = checkManualDayByUserBooking($reserveDateFrom, $reserveDateTo, $serviceID, $qty);
        if (!empty($reason) && !empty($reserveDateFrom) && !empty($reserveDateTo) && !empty($serviceID)) {

            if (!checkManualDay($reserveDateFrom, $reserveDateTo, $serviceID, $qty, $id) && !$recurring) {

                //$msg = "This time booked!";
                addMessage(MSG_TMBK, "warning");
            } elseif (date("Y-m-d", strtotime($reserveDateTo)) <= date("Y-m-d", strtotime($reserveDateFrom))) {

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
				VALUES ('" . $serviceID . "','" . DATETIME . "','" . $reason . "','" . $reserveDateFrom . " 00:00:00','" . $reserveDateTo . " 00:00:00','" . $qty . "','" . $repeate . "','" . $repeate_interval . "','" . $recurring . "')";
                $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to create new booking.");
                $id = $mysqli->insert_id;
                //$msg = "Booking was successfully saved!";
                addMessage(MSG_BKSAVE, "success");

                #checking for overwrite customer bookings
                if (count($userBookings) && !$recurring) {
                    $warningMessage = "This boking overwrite some customer booking<br/>";
                    foreach ($userBookings as $k => $v) {
                        $warningMessage .= "#{$v['id']} from <a href='bs-bookings_day-edit.php?id={$v['id']}' target='_blank'>{$v['name']}</a><br/>";
                    }
                    addMessage($warningMessage, "warning");
                }
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

        $reserveDateFrom = date("Y-m-d", strtotime($reserveDateFrom));

        $reserveDateTo = date("Y-m-d", strtotime($reserveDateTo));;
    }
    ?>

<?php include "includes/admin_header.php"; ?>
<?php if (!$recurring) { ?>
<style>
    .recurring {
        display: none;
    }

    <?php } ?>
</style>
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
        $("#reserveDateTo").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd",
            showOn: "button",
            buttonImage: "images/new/calendar.png",
            buttonImageOnly: true
        });

        $('#serviceID').bind("change", function() {
            var qty = $(this + ":selected").attr("rel");
            var el = '';
            for (var i = 1; i <= qty; i++) {
                el += "<option value='" + i + "'>" + i + "</option>";
            }
            $("#qty").html(el);
        })
        $("#recurring").bind("change", function() {

            if ($(this).is(':checked')) {
                $('.recurring').show();
            } else {
                $('.recurring').hide();
            }
        })
        $("#repeate").bind("change", function() {

            $("#int_name").html($(this).val());
        })
    });
</script>
<div id="content">




    <?php getMessages(); ?>
    <div class="content_block">
        <?php if (!empty($id)) { ?>
        <h2><?php echo EDIT_MAN_BOOK_DAY; ?> <a href="bs-reserve-view.php"><?php echo MSG_BACK ?></a> </h2>
        <?php } else {
                ?>
        <h2><?php echo ADD_MAN_BOOK_DAY; ?> <a href="bs-reserve-view.php"><?php echo MSG_BACK ?></a> </h2>
        <?php }  ?>
        <p><?php echo BS_RESERVE_DAY_DESCR; ?></p>
        <form action="bs-reserve-day.php" enctype="multipart/form-data" method="post" name="ff1">
            <div class="form-row">
                <div class="cell long">
                    <label><?php echo SHRT_DESCRPTN ?></label>
                    <input type="text" name="reason" id="reason" value="<?php echo $reason ?>" />
                </div>
                <div class="cell long">
                    <label><?php echo SEL_SERVICE ?></label>
                    <?php
                        $sql = "SELECT *,bs.id as sid FROM bs_services bs  INNER JOIN bs_service_days_settings bss ON bss.idService=bs.id";
                        $res = $mysqli->query($sql);
                        if ($res->num_rows > 0) {
                            ?>
                    <select name="serviceID" class="select" id="serviceID">

                        <?php while ($row = $res->fetch_assoc()) { ?>
                        <option value="<?php echo $row['sid'] ?>" rel="<?php echo $row['show_multiple_spaces'] ? $row['spaces_available'] : 1 ?>" <?php echo ($serviceID == $row['sid']) ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </select>
                    <?php } ?>
                    <img src='images/info.png' border="0" class="tipTip adj_add_events_tip" title=" <?php echo SELECT_SERVICE_RESERVE ?>" />
                </div>

                <div class="clear"></div>
            </div>
            <hr />
            <div class="form-row">
                <h3>Reservation</h3>
                <div class="dates-row">
                    <label>Starts:</label>
                    <input type="text" name="reserveDateFrom" class='small left' id="reserveDateFrom" value="<?php echo $reserveDateFrom ?>" />

                    <div class="clear"></div>
                </div>
                <div class="dates-row">
                    <label>Ends:</label>
                    <input type="text" name="reserveDateTo" class='small left' id="reserveDateTo" value="<?php echo $reserveDateTo ?>" />

                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
            <hr />
            <button class="save" type="submit"><span><?php echo ADM_BTN_SUBMIT; ?></span></button>
            <input value="yes" name="edit_page" type="hidden" />
            <input value="<?php echo $id; ?>" name="id" type="hidden" />
        </form>
    </div>

    <?php include "includes/admin_footer.php"; ?>
    <?php } ?>