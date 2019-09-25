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


    $id = (!empty($_REQUEST["id"])) ? intval($_REQUEST["id"]) : '';
    $title = (!empty($_REQUEST["title"])) ? strip_tags(str_replace("'", "`", $_REQUEST["title"])) : '';

    $value = (!empty($_REQUEST["value"])) ? intval($_REQUEST["value"]) : 1.0;

    $code = (!empty($_REQUEST["code"])) ? strip_tags(str_replace("'", "`", $_REQUEST["code"])) : '';
    $dateFrom = (!empty($_REQUEST["dateFrom"])) ? strip_tags(str_replace("'", "`", $_REQUEST["dateFrom"])) : date("Y-m-d");
    $dateTo = (!empty($_REQUEST["dateTo"])) ? strip_tags(str_replace("'", "`", $_REQUEST["dateTo"])) : date("Y-m-") . date("t");
    $type = (!empty($_REQUEST["type"])) ? strip_tags(str_replace("'", "`", $_REQUEST["type"])) : 'rel';
    $services = (!empty($_REQUEST["services"])) ? $_REQUEST["services"] : array();

    $msg = "";
    //bw_dump($_REQUEST);
    if (!empty($_REQUEST["edit_page"]) && $_REQUEST["edit_page"] == "yes") {

        $msg = MSG_COUPON_UPD;



        if (empty($id)) {
            if (checkCouponCode($code)) {
                addMessage("Coupon code '{$code}' already exists. Please type another", "error");
            } else {
                $sql = "INSERT INTO bs_coupons (title,dateFrom,dateTo,type,value,services,code)
                    VALUES ('" . $title . "','" . $dateFrom . "','" . $dateTo . "','" . $type . "','" . $value . "','" . join(",", $services) . "','" . $code . "')";

                $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to create new coupon.");
                $id = $mysqli->insert_id;

                $msg = MSG_COUPON_SAVE;
            }
        }

        if (!empty($id)) {

            $query = "UPDATE `bs_coupons` SET

                    `title` = '{$title}',
                    `dateFrom` = '{$dateFrom}',
                    `dateTo` = '{$dateTo}',
                    `type` = '{$type}',
                    `value`='{$value}',
                    `services`='" . join(",", $services) . "',
                    `code`='{$code}'
                    WHERE `id` = '{$id}'";
            $result = $mysqli->query($query) or die("oopsy, error occured when tryin to update coupon." . $mysqli->error() . " - $query");


            addMessage($msg, "success");
        }
    }
    if (!empty($id)) {
        $sSQL = "SELECT * FROM bs_coupons WHERE id='{$id}'";
        $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
        if ($row = $result->fetch_assoc()) {
            foreach ($row as $key => $val) {
                $$key = $val;
            }
        }
        $result->free_result();
        $services = explode(",", $services);
    }

    ?>
<?php include "includes/admin_header.php"; ?>
<script type="text/javascript">
    $(function() {
        updateDatePickers()
    })

    function updateDatePickers() {
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd",
            showOn: "button",
            buttonImage: "images/new/calendar.png",
            buttonImageOnly: true,
            changeYear: false
        });
    }

    function noAlpha(obj) {
        reg = /[^0-9.,]/g;
        obj.value = obj.value.replace(reg, "");
    }
</script>
<div id="content">




    <?php getMessages(); ?>
    <div class="content_block">
        <?php if (!empty($id)) { ?>
        <h2><?php echo TXT_COUPONS_ADD; ?> <a href="bs-coupons.php"><?php echo BACK_TO_LIST ?></a> </h2>
        <?php } else {
                ?>
        <h2><?php echo TXT_COUPONS_NEW; ?> <a href="bs-coupons.php"><?php echo BACK_TO_LIST ?></a> </h2>
        <?php }  ?>
        <p><?php echo BS_COUPONS_TXT; ?></p>
        <hr />


        <form action="" enctype="multipart/form-data" method="post" name="ff1">
            <div class="form-row">
                <div class="cell third">
                    <label><?php echo LABEL_COUPON_TITLE ?></label>
                    <input type="text" name="title" id="title" value="<?php echo $title ?>" />
                </div>
                <div class="cell medium1">
                    <label><?php echo LABEL_COUPON_CODE ?></label>
                    <input type="text" name="code" class="small" id="code" value="<?php echo $code ?>" />
                </div>
                <div class="cell short1">
                    <label><?php echo LABEL_COUPON_TYPE ?></label>
                    <div class="valign">
                        <input type="radio" value="rel" name="type" <?php echo $type == 'rel' ? "checked" : "" ?>>&nbsp;%
                        <input type="radio" value="abs" name="type" <?php echo $type == 'abs' ? "checked" : "" ?>>&nbsp;<?php echo getOption("currency") ?>
                    </div>
                </div>
                <div class="cell short">
                    <label><?php echo LABEL_COUPON_VALUE ?></label>
                    <input type="text" name="value" id="value" class="smaller" value="<?php echo $value ?>" onkeyup="noAlpha(this)" />
                </div>
                <div class="clear"></div>
            </div>
            <div class="form-row">
                <h3><?php echo TXT_SET_ST ?></h3>
                <div class="dates-row">

                    <label><?php echo TXT_COUPONS_STARTS ?>:</label>
                    <input type="text" name="dateFrom" id="dateFrom" class='dateInput left datepicker' value="<?php echo _date($dateFrom) ?>" />

                </div>
                <div class="dates-row" style="margin-left:-60px">
                    <label><?php echo TXT_COUPONS_ENDS ?>:</label>
                    <input type="text" name="dateTo" id="dateTo" class='dateInput left datepicker' value="<?php echo _date($dateTo) ?>" />

                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="form-row">
                <div class="cell">
                    <label><?php echo LABEL_COUPON_CALENDARS ?></label>
                    <?php
                        $sql = "SELECT * FROM bs_services ORDER BY name ASC";
                        $res = $mysqli->query($sql);
                        while ($s = $res->fetch_assoc()) { ?>
                    <input type="checkbox" name="services[]" value="<?php echo $s['id'] ?>" <?php echo in_array($s['id'], $services) ? "checked" : "" ?>>&nbsp;<?php echo $s['name'] ?><br />
                    <?php } ?>
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