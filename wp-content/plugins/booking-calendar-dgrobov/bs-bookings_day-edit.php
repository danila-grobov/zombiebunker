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
    $id = (!empty($_REQUEST["id"])) ? strip_tags(str_replace("'", "`", $_REQUEST["id"])) : '';
    $name = (!empty($_REQUEST["name"])) ? strip_tags(str_replace("'", "`", $_REQUEST["name"])) : '';
    $email = (!empty($_REQUEST["email"])) ? strip_tags(str_replace("'", "`", $_REQUEST["email"])) : '';
    $phone = (!empty($_REQUEST["phone"])) ? strip_tags(str_replace("'", "`", $_REQUEST["phone"])) : '';
    $status = (!empty($_REQUEST["status"])) ? strip_tags(str_replace("'", "`", $_REQUEST["status"])) : '';
    $old_status = (!empty($_REQUEST["old_status"])) ? strip_tags(str_replace("'", "`", $_REQUEST["old_status"])) : '';
    $comments = (!empty($_REQUEST["comments"])) ? strip_tags(str_replace("'", "`", $_REQUEST["comments"])) : '';
    $serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : '';
    $dateFrom = (!empty($_POST["dateFrom"])) ? strip_tags(str_replace("'", "`", $_POST["dateFrom"])) : '';
    $dateTo = (!empty($_POST["dateTo"])) ? strip_tags(str_replace("'", "`", $_POST["dateTo"])) : '';


    //"edit page" action processing.
    if (!empty($_REQUEST["edit_page"]) && $_REQUEST["edit_page"] == "yes" && !empty($name)) {
        $checkForAvailability = _checkSpotsForDayInterval($dateFrom, $dateTo, $serviceID, $id);

        if ($checkForAvailability === 1) {
            $msg .= "Booking was successfully updated!";
            $sql = "UPDATE bs_reservations SET
                name='" . $name . "',
                phone='" . $phone . "',
                email='" . $email . "',
                status='" . $status . "',
                comments='" . $comments . "',
                serviceID='" . $serviceID . "'
                WHERE id='" . $id . "'";
            $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to update page.");
            $sql = "UPDATE bs_reservations_items SET
                reserveDateFrom='" . $dateFrom . "',
                reserveDateTo='" . $dateTo . "'

                WHERE reservationID='" . $id . "'";
            $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to update page.");
        } else {
            addMessage($checkForAvailability, "error");
        }
    }

    //select editable user's info and show it for editor.
    $sSQL = "SELECT *  FROM bs_reservations WHERE id='" . $id . "'";
    $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
    if ($row = $result->fetch_assoc()) {
        foreach ($row as $key => $value) {
            $$key = $value;
        }
    }
    $sSQL = "SELECT *  FROM bs_reservations_items WHERE reservationID='" . $id . "'";
    $res = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
    $reservInfo = $res->fetch_assoc();
    $dateFrom = $reservInfo['reserveDateFrom'];
    $dateTo = $reservInfo['reserveDateTo'];
    $result->free_result();

    $paymentData = get_payment_info($id);
    //bw_dump($paymentData);
    $subtotal = $paymentData['subAmount'];
    $tax = $paymentData['tax'];
    $total = $paymentData['amount'];
    $taxRate = $paymentData['taxRate'];

    //get customer name and email
    $custInf = getInfoByReservID($id);
    //get event information for email notification
    $serviceSettings = getServiceSettings($serviceID);
    $serviceName = getService($serviceID, 'name');
    $days = getDaysInterval($dateFrom, $dateTo);
    if ($old_status != $status && $status == "1" && !empty($old_status)) {


        //send confirmation to client.
        //send email to customer
        $subject = "Day booking confirmed!";

        $data = array(
            "{%name%}" => $custInf[0],
            "{%service%}" => $serviceName,
            "{%dateFrom%}" => getDateFormat($dateFrom),
            "{%dateTo%}" => getDateFormat($dateTo),
            "{%descr%}" => $serviceSettings['description'],
            "{%days%}" => $days,
            "{%status%}" => BOOKING_FRM_CONFIRMED
        );
        sendMail($custInf[1], $subject, "dayBookingConfirmationStatus.php", $serviceID, $data);
        $sent = true;

        addMessage(ADM_MSG1, "success");
    }


    if ($old_status != $status && $status == "3" && !empty($old_status)) {
        //send cancel email to customer
        $subject = "Day booking canceled!";
        //mail($custInf[1], $subject, $message, $headers);
        $data = array(
            "{%name%}" => $custInf[0],
            "{%service%}" => $serviceName,
            "{%dateFrom%}" => getDateFormat($dateFrom),
            "{%dateTo%}" => getDateFormat($dateTo),
            "{%descr%}" => $serviceSettings['description'],
            "{%days%}" => $days,
            "{%status%}" => BOOKING_FRM_CANCELLED
        );
        sendMail($custInf[1], $subject, "dayBookingConfirmationStatus.php", $serviceID, $data);
        $sent = true;

        addMessage(ADM_MSG2, "success");
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
            <?php echo PAGE_TITLE_DAY_BOOK ?>
            <a href="bs-bookings.php?serviceID=<?php echo $serviceID; ?>"><?php echo BACK_TO_LIST ?></a>
        </h2>


        <form action="bs-bookings_day-edit.php" enctype="multipart/form-data" method="post" name="ff1">
            <input type="hidden" value="<?php echo $status ?>" name="old_status" />
            <input value="yes" name="edit_page" type="hidden" />
            <input value="<?php echo $id; ?>" name="id" type="hidden" />
            <div class="form-row">
                <?php if (!empty($id)) { ?>
                <div class="cell third">
                    <label><?php echo DATE_BOOK_PLC ?></label>

                    <div class="date_created"><?php echo getDateFormat($dateCreated) . _time($row["reserveDateFrom"]) ?> </div>
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
                    <textarea name="comments" id="comments" style="width: 510px;height: 150px;" /><?php echo $comments ?></textarea>
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
                        <option value="<?php echo $row['id'] ?>" <?php echo ($serviceID == $row['id']) ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="cell short">
                    <label><?php echo TOTAL_DAYS ?></label>
                    <input type="text" name="qty" id="qty" value="<?php echo getDaysInterval($dateFrom, $dateTo) ?>" class="small1 left" readonly="readonly" />

                </div>
                <?php if ($total > 0) { ?>
                <div class="cell short">
                    <label><?php echo BOOKING_FRM_SUBTOTAL ?>&nbsp;(<?php echo getOption('currency') ?>)</label>
                    <input type="text" name="subtotal" id="subtotal" value="<?php echo number_format($subtotal, 2) ?>" class="small1 left" readonly="readonly" />

                </div>
                <?php if ($tax > 0) { ?>
                <div class="cell short">
                    <label><?php echo BOOKING_FRM_TAX ?>&nbsp;(<?php echo getOption('currency') ?>) <?php echo $taxRate ?>%</label>
                    <input type="text" name="tax" id="tax" value="<?php echo number_format($tax, 2) ?>" class="small1 left" readonly="readonly" />
                    <?php } ?>
                </div>

                <div class="cell ">
                    <label><?php echo BOOKING_FRM_TOTAL ?>&nbsp;(<?php echo getOption('currency') ?>)</label>
                    <input type="text" name="total" id="total" value="<?php echo number_format($total, 2) ?>" class="small1 left" readonly="readonly" />

                </div>
                <?php } ?>
                <div class="clear"></div>
                <div class="form-row">
                    <h3>Start and End Dates</h3>
                    <div class="dates-row">

                        <label>Starts:</label>
                        <input type="text" name="dateFrom" id="dateFrom" class='dateInput left datepicker' value="<?php echo _date($dateFrom) ?>" />
                    </div>
                    <div class="dates-row">
                        <label>Ends:</label>
                        <input type="text" name="dateTo" id="dateTo" class='dateInput left datepicker' value="<?php echo _date($dateTo) ?>" />
                        <div class="clear"></div>
                    </div>
                </div>


                <div class="clear"></div>
            </div>


            <hr />
            <?php /*<input type="submit" name="create" id="create" value="<?php echo ADM_BTN_SUBMIT;?>" />*/ ?>
            <button class="save" type="submit"><span><?php echo ADM_BTN_SUBMIT; ?></span></button>




        </form>
    </div>
    <?php include "includes/admin_footer.php"; ?>
    <?php } ?>