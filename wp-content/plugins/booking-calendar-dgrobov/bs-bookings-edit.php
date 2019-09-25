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
    exit();
} else {

    bw_do_action("bw_load");
    bw_do_action("bw_admin");
    if (getOption('is_word_press') == "1") {
        $spinClass = "adjStartEnd_noSpin";
    } else {
        $spinClass = "adjStartEnd";
    }
    ######################## DO NOT MODIFY (UNLESS SURE) END ########################

    //show page only if admin access level

    //request all neccessary variables for user update action.
    $id = (!empty($_REQUEST["id"])) ? strip_tags(str_replace("'", "`", $_REQUEST["id"])) : '';
    $name = (!empty($_REQUEST["name"])) ? strip_tags(str_replace("'", "`", $_REQUEST["name"])) : '';
    $email = (!empty($_REQUEST["email"])) ? strip_tags(str_replace("'", "`", $_REQUEST["email"])) : '';
    $phone = (!empty($_REQUEST["phone"])) ? strip_tags(str_replace("'", "`", $_REQUEST["phone"])) : '';
    $status = (!empty($_REQUEST["status"])) ? strip_tags(str_replace("'", "`", $_REQUEST["status"])) : '';
    $old_status = (!empty($_REQUEST["old_status"])) ? strip_tags(str_replace("'", "`", $_REQUEST["old_status"])) : '';
    $comments = (!empty($_REQUEST["comments"])) ? strip_tags(str_replace("'", "`", $_REQUEST["comments"])) : '';
    $serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : '';
    $young = (!empty($_REQUEST["young"])) ? strip_tags(str_replace("'", "`", $_REQUEST["young"])) : '';
    $qty = (!empty($_REQUEST["qty"])) ? strip_tags(str_replace("'", "`", $_REQUEST["qty"])) : '';


    $action = (!empty($_REQUEST["action"])) ? strip_tags(str_replace("'", "`", $_REQUEST["action"])) : '';
    $sid = (!empty($_REQUEST["sid"])) ? strip_tags(str_replace("'", "`", $_REQUEST["sid"])) : '';

    if ($action == 'del' && !empty($sid)) {
        $sql = "DELETE FROM bs_reservations_items WHERE id='{$sid}' AND reservationID='{$id}'";
        $result = $mysqli->query($sql) or die(GENERIC_QUERY_FAIL);
        addMessage(BOOKING_DEL_ITEM, "warning");
    }

    //"edit page" action processing.
    if (!empty($_REQUEST["edit_page"]) && $_REQUEST["edit_page"] == "yes" && !empty($name)) {
        //$msg .= BOOKING_SUCC;
        addMessage(BOOKING_SUCC, "success");
        /*if(empty($id)){
                   $sql="INSERT INTO bs_reserved_time (dateCreated,reason,reserveDateFrom,reserveDateTo) VALUES (NOW(),'".$reason."','".$reserveDateFrom." ".$x1_from.":00"."','".$reserveDateTo." ".$x2_from.":00"."')";
                   $result=$mysqli->query($sql) or die("oopsy, error occured when tryin to create new booking.");
                   $id = $mysqli->insert_id;
                   $msg = "Booking was successfully created!";
               }*/

        $young = $young === '1' ? 1 : 0;
        $sql = "UPDATE bs_reservations SET
            young=" . $young  . ",
			name='" . $name . "',
			phone='" . $phone . "',
			email='" . $email . "',
			status='" . $status . "',
			comments='" . $comments . "' ,
			serviceID='" . $serviceID . "',
			qty='" . $qty . "'
			WHERE id='" . $id . "'";
        $result = $mysqli->query($sql) or die(GENERIC_QUERY_FAIL);
    }



    //select editable user's info and show it for editor.
    $sSQL = "SELECT id,dateCreated,name,phone,email,status,comments,serviceID,qty,young FROM bs_reservations WHERE id='" . $id . "'";
    $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
    if ($row = $result->fetch_assoc()) {
        foreach ($row as $key => $value) {
            $$key = $value;
        }
    }
    $result->free_result();
    //payment info
    $paymentData = get_payment_info($id);
    //bw_dump($paymentData);
    $subtotal = $paymentData['subAmount'];
    $tax = $paymentData['tax'];
    $total = $paymentData['amount'];
    $taxRate = $paymentData['taxRate'];


    //booked dates processing.
    $bookingArrFrom = (!empty($_REQUEST["reserveDateFrom"])) ? $_REQUEST["reserveDateFrom"] : array();
    $bookingArrTo = (!empty($_REQUEST["reserveDateTo"])) ? $_REQUEST["reserveDateTo"] : array();

    $HHFrom = (!empty($_REQUEST["HHFrom"])) ? $_REQUEST["HHFrom"] : array();
    $MMFrom = (!empty($_REQUEST["MMFrom"])) ? $_REQUEST["MMFrom"] : array();
    $HHTo = (!empty($_REQUEST["HHTo"])) ? $_REQUEST["HHTo"] : array();
    $MMTo = (!empty($_REQUEST["MMTo"])) ? $_REQUEST["MMTo"] : array();

    $act1 = false;
    $act2 = false;

    $sSQL = "SELECT reserveDateFrom FROM bs_reservations_items WHERE reservationID='" . $id . "' LIMIT 1";
    $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
    $date = mysqli_result($result, 0, 'reserveDateFrom');
    if (count($bookingArrTo) && count($bookingArrFrom)) {
        foreach ($bookingArrFrom as $key => $value) {
            if (!empty($value)) {
                //update
                $check = checkTimesIntervals($serviceID, $date, "{$HHFrom[$key]}:{$MMFrom[$key]}", "{$HHTo[$key]}:{$MMTo[$key]}");
                if ($check['res']) {
                    $dateFrom = "$value {$HHFrom[$key]}:{$MMFrom[$key]}";
                    $dateTo = "$value {$HHTo[$key]}:{$MMTo[$key]}";

                    $spots = checkSpotsForTimeInterval($serviceID, $dateFrom, $dateTo, $qty, $id);
                    if ($spots >= $qty) {
                        $q = "UPDATE bs_reservations_items SET
                            reserveDateFrom='" .  $dateFrom . "',
                            reserveDateTo='" . $dateTo . "',
                            qty='" . $qty . "'
                            WHERE id='" . $key . "'";
                        //echo $q;
                        $mysqli->query($q) or die($mysqli->error());
                        $act1 = true;
                    } else {
                        addMessage("For time interval from '{$HHFrom[$key]}:{$MMFrom[$key]}' to {$HHTo[$key]}:{$MMTo[$key]} available $spots spots'", "error");
                    }
                } else {
                    addMessage($check['message'], "error");
                }
            }
        }
    }
    if ($act1) {
        // $msg .= BOOKING_TIME_UPDATED;
        addMessage(BOOKING_TIME_UPDATED, "success");
    }
    if ($act2) {
        //$msg .= BOOKING_TIME_DELETED;
        addMessage(BOOKING_TIME_DELETED, "success");
    }
    $booked_dates = "";
    $bookingData = array();
    $sSQL = "SELECT * FROM bs_reservations_items WHERE reservationID='" . $id . "'";
    $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
    while ($row = $result->fetch_assoc()) {
        $booked_dates .= "From: <input type='text' name='bookingArrFrom[" . $row["id"] . "]' value='" . $row["reserveDateFrom"] . "'><br />";
        $booked_dates .= "To: <input type='text' name='bookingArrTo[" . $row["id"] . "]' value='" . $row["reserveDateTo"] . "'><br /><br />";


        $bookingData[] = array(
            'date' => getDateFormat($row["reserveDateFrom"]),
            'timeFrom' => date((getTimeMode()) ? "g:i a" : "H:i", strtotime($row["reserveDateFrom"])),
            'timeTo' => date((getTimeMode()) ? "g:i a" : "H:i", strtotime($row["reserveDateTo"])),
            'qty' => $qty
        );
    }


    if ($old_status != $status && $status == "1" && !empty($old_status)) {
        //send confirmation to client.
        //send email to customer

        $subject = EMAIL_SUBJ_CONFIRMED;
        $data = array(
            "{%name%}" => $name,
            "{%status%}" => BOOKING_FRM_CONFIRMED,
            "_info" => $bookingData,

        );
        sendMail($email, $subject, "timeBookingConfirmationStatus.php", $serviceID, $data);

        addMessage(ADM_MSG1, "success");
    }


    if ($old_status != $status && $status == "3" && !empty($old_status)) {
        //send cancel email to customer

        $subject = EMAIL_SUBJ_CANCELLED;
        $data = array(
            "{%name%}" => $name,
            "{%status%}" => BOOKING_FRM_CANCELLED,
            "_info" => $bookingData,

        );
        sendMail($email, $subject, "timeBookingConfirmationStatus.php", $serviceID, $data);
        addMessage(ADM_MSG2, "success");
    }

    ?>

    <?php include "includes/admin_header.php"; ?>
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

        });
    </script>
    <div id="content">




        <?php getMessages(); ?>
        <div class="content_block">
            <h2>
                <?php echo BOOKING_EDIT_TITLE ?>
                <a href="bs-bookings.php"><?php echo BACK_TO_LIST ?></a>
            </h2>


            <form action="bs-bookings-edit.php" enctype="multipart/form-data" method="post" name="ff1">
                <input type="hidden" value="<?php echo $status ?>" name="old_status" />
                <input value="yes" name="edit_page" type="hidden" />
                <input value="<?php echo $id; ?>" name="id" type="hidden" />
                <div class="form-row">
                    <?php if (!empty($id)) { ?>
                        <div class="cell third">
                            <label><?php echo DATE_BOOK_PLC ?></label>

                            <div class="date_created"><?php echo getDateFormat($dateCreated) . _time($dateCreated) ?> </div>
                        </div>
                    <?php } ?>
                    <div class="cell third">
                        <label><?php echo BOOKING_LST_STATUS ?></label>
                        <select name="status" class="select">
                            <option value=""><?php echo BOOKING_FRM_SELECT ?></option>
                            <option value="1" <?php echo $status == "1" ? "selected" : "" ?>><?php echo BOOKING_FRM_CONFIRMED ?></option>
                            <option value="2" <?php echo $status == "2" ? "selected" : "" ?>><?php echo BOOKING_FRM_NOTCONFIRMED ?></option>
                            <option value="3" <?php echo $status == "3" ? "selected" : "" ?>><?php echo BOOKING_FRM_CANCELLED ?></option>
                            <option value="4" <?php echo $status == "4" ? "selected" : "" ?>><?php echo BOOKING_FRM_PAID ?></option>
                        </select>
                    </div>


                    <div class="clear"></div>
                </div>


                <div class="form-row">
                    <h3>Customer’s Info</h3>
                    <div class="cell third">
                        <label><?php echo BOOKING_LST_NAME ?></label>
                        <input type="text" name="name" id="name" value="<?php echo $name ?>" />
                    </div>
                    <div class="cell third">
                        <label><?php echo BOOKING_LST_EMAIL ?></label>
                        <input type="text" name="email" id="email" value="<?php echo $email ?>" />
                    </div>
                    <div class="cell third">
                        <label><?php echo BOOKING_LST_PHONE ?></label>
                        <input type="text" name="phone" id="phone" value="<?php echo $phone ?>" />

                    </div>
                    <div class="clear"></div>
                </div>

                <div class="form-row">
                    <div class="cell">
                        <label><?php echo BOOKING_FRM_COMMENTS ?></label>
                        <textarea name="comments" id="comments" style="width: 510px;height: 150px;"><?php echo $comments ?></textarea>
                    </div>
                    <div class="clear"></div>
                </div>
                <hr />
                <div class="form-row">
                    <div class="cell third">

                        <label><?php echo BOOKING_FRM_SERVICE ?></label>
                        <select name="serviceID" class="select">
                            <?php
                                $sql = "SELECT * FROM bs_services";
                                $res = $mysqli->query($sql);
                                while ($row = $res->fetch_assoc()) {
                                    ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo ($serviceID == $row['id']) ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="cell short">
                        <label><?php echo BOOKING_FRM_QTY; ?></label>
                        <input type="text" name="qty" id="qty" value="<?php echo $qty ?>" class="small1 left" onkeyup="noAlpha(this)" />

                    </div>
                    <div class="customCell">
                        <label><?php echo BOOKING_LST_YOUNG; ?></label>
                        <input type="checkbox" <?php echo ($young === "1" ? "checked" : ""); ?> value="1" name="young" id="young" onkeyup="noAlpha(this)" />
                    </div>
                    <?php if ($total > 0) { ?>
                        <div class="cell short">
                            <label><?php echo BOOKING_FRM_SUBTOTAL; ?>&nbsp;(<?php echo getOption('currency') ?>)</label>
                            <input type="text" name="subtotal" id="subtotal" value="<?php echo number_format($subtotal, 2) ?>" class="small1 left" readonly="readonly" />

                        </div>
                        <?php if ($tax > 0) { ?>
                            <div class="cell short">
                                <label><?php echo BOOKING_FRM_TAX ?>&nbsp;(<?php echo getOption('currency') ?>)<?php echo $taxRate ?>%</label>
                                <input type="text" name="tax" id="tax" value="<?php echo number_format($tax, 2) ?>" class="small1 left" readonly="readonly" />
                            </div>
                        <?php } ?>


                        <div class="cell " style="position: relative;bottom: 4px;">
                            <label><?php echo BOOKING_FRM_TOTAL ?>&nbsp;(<?php echo getOption('currency') ?>)</label>
                            <input type="text" name="total" id="total" value="<?php echo number_format($total, 2) ?>" class="small1 left" readonly="readonly" />

                        </div>
                    <?php } ?>


                    <div class="clear"></div>
                </div>

                <div class="form-row">
                    <h3>Start and End Dates</h3>
                    <?php
                        $sSQL = "SELECT * FROM bs_reservations_items WHERE reservationID='" . $id . "' ORDER BY reserveDateFrom ASC";
                        $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
                        $countRows = $result->num_rows;
                        while ($row = $result->fetch_assoc()) {

                            $dateFrom = _date($row['reserveDateFrom']);
                            $HHFrom = _hh($row['reserveDateFrom']);
                            $MMFrom = _mm($row['reserveDateFrom']);

                            $dateTo = _date($row['reserveDateTo']);
                            $HHTo = _hh($row['reserveDateTo']);
                            $MMTo = _mm($row['reserveDateTo']);
                            ?>

                        <div class="dates-row" style="margin-bottom: 5px; ">
                            <label>Starts:</label>
                            <input type="text" name="reserveDateFrom[<?php echo $row['id'] ?>]" class='dateInput left datepicker' value="<?php echo $dateFrom ?>" />
                            <label>at:</label>
                            <div class="left">
                                <input type="text" name="HHFrom[<?php echo $row['id'] ?>]" value="<?php echo $HHFrom; ?>" class="<?php echo $spinClass; ?> adj_hrs_0" />

                            </div>
                            <label>:</label>
                            <div class="left">
                                <input type="text" name="MMFrom[<?php echo $row['id'] ?>]" value="<?php echo $MMFrom; ?>" class="<?php echo $spinClass; ?> adj_mins_0" />

                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="dates-row" style="margin-bottom: 5px;width: 315px;padding-right: 0;">
                            <label>Ends:</label>
                            <input type="text" name="reserveDateTo[<?php echo $row['id'] ?>]" class='dateInput left datepicker' value="<?php echo $dateTo ?>" />
                            <label>at:</label>
                            <div class="left">
                                <input type="text" name="HHTo[<?php echo $row['id'] ?>]" value="<?php echo $HHTo; ?>" class="<?php echo $spinClass; ?> adj_hrs_0" />

                            </div>
                            <label>:</label>
                            <div class="left">
                                <input type="text" name="MMTo[<?php echo $row['id'] ?>]" value="<?php echo $MMTo; ?>" class="<?php echo $spinClass; ?> adj_mins_0" />

                            </div>
                            <?php if ($countRows > 1) { ?>
                                <div class="left delCenter">
                                    <a href="bs-bookings-edit.php?id=<?php echo $id ?>&sid=<?php echo $row['id'] ?>&action=del">
                                        <img style="position: absolute;margin-left: 10px;" src="images/del_item.png" />
                                    </a>
                                </div>
                            <?php } ?>
                            <div class="clear"></div>
                        </div>

                    <?php } ?>
                    <div class="clear"></div>
                </div>
                <hr />
                <?php /*<input type="submit" name="create" id="create" value="<?php echo ADM_BTN_SUBMIT;?>" />*/  ?>
                <button class="save" type="submit"><span><?php echo ADM_BTN_SUBMIT ?></span></button>




            </form>

        </div>

        <?php include "includes/admin_footer.php"; ?>
    <?php } ?>