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
    $action = (!empty($_REQUEST["action"])) ? intval($_REQUEST["action"]) : '';
    $ids = (!empty($_REQUEST["ids"])) ? intval($_REQUEST["ids"]) : '';
    $name = (!empty($_REQUEST["name"])) ? strip_tags(str_replace("'", "`", $_REQUEST["name"])) : '';

    $spots = (!empty($_REQUEST["spots"])) ? intval($_REQUEST["spots"]) : 1;
    $description = (!empty($_REQUEST["description"])) ? strip_tags(str_replace("'", "`", $_REQUEST["description"])) : '';
    $maxDays = (!empty($_REQUEST["maxDays"])) ? intval($_REQUEST["maxDays"]) : 1;
    $minDays = (!empty($_REQUEST["minDays"])) ? intval($_REQUEST["minDays"]) : 1;
    $daysBefore = (!empty($_REQUEST["daysBefore"])) ? intval($_REQUEST["daysBefore"]) : 0;
    $payment_method = (!empty($_REQUEST["payment_method"])) ? strip_tags(str_replace("'", "`", $_REQUEST["payment_method"])) : 'invoice';
    $coupon = (isset($_REQUEST["coupon"])) ? strip_tags(str_replace("'", "`", $_REQUEST["coupon"])) : '0';
    $startDay = (!empty($_REQUEST["startDay"])) ? strip_tags(str_replace("'", "`", $_REQUEST["startDay"])) : '0';

    $autoconfirm = (isset($_REQUEST["autoconfirm"])) ? strip_tags(str_replace("'", "`", $_REQUEST["autoconfirm"])) : '0';
    $fromName = (!empty($_REQUEST["fromName"])) ? strip_tags(str_replace("'", "`", $_REQUEST["fromName"])) : '';
    $fromEmail = (!empty($_REQUEST["fromEmail"])) ? strip_tags(str_replace("'", "`", $_REQUEST["fromEmail"])) : '';
    $show_event_titles = (!empty($_REQUEST["show_event_titles"])) ? strip_tags(str_replace("'", "`", $_REQUEST["show_event_titles"])) : 0;
    $show_event_image = (!empty($_REQUEST["show_event_image"])) ? strip_tags(str_replace("'", "`", $_REQUEST["show_event_image"])) : 0;
    $show_available_seats = (!empty($_REQUEST["show_available_seats"])) ? strip_tags(str_replace("'", "`", $_REQUEST["show_available_seats"])) : 0;
    $showPrice = (!empty($_REQUEST["showPrice"])) ? strip_tags(str_replace("'", "`", $_REQUEST["showPrice"])) : 0;

    $deposit = (isset($_REQUEST["deposit"])) ? strip_tags(str_replace("'", "`", $_REQUEST["deposit"])) : '100';
    $delBookings = (isset($_REQUEST["delBookings"])) ? strip_tags(str_replace("'", "`", $_REQUEST["delBookings"])) : 'n';

    $msg = "";
    //bw_dump($_REQUEST);
    if ($action == 'del_schedule') {
        $sql = "DELETE FROM bs_schedule_days WHERE idItem='{$ids}'";
        $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to delete schedule.<br>");
        addMessage(MSG_SHEDULE_DEL, "success");
    }

    ########################################################################################################################################################
    if (isset($_GET['delImg']) && $_GET['delImg'] == 'yes' && !empty($_GET['id'])) {

        $sql = "SELECT img FROM bs_service_days_settings WHERE idService='" . $id . "'";
        $result = $mysqli->query($sql) or die("oopsy, error when tryin to get images 1");
        @unlink($_SERVER['DOCUMENT_ROOT'] . $baseDir . mysqli_result($result, 'img'));

        $sSQL = "UPDATE bs_service_days_settings SET img='' WHERE idService='" . $id . "'";
        $mysqli->query($sSQL) or die("Invalid query: " . $mysqli->error() . " - $sSQL");
    }


    $action = (!empty($_REQUEST['action'])) ? $_REQUEST['action'] : "";

    if ($action == 'setDefault') {

        $sql = "UPDATE bs_services SET `default`='n'";
        $result = $mysqli->query($sql) or die("err: " . $mysqli->error() . $sql);

        $sql = "UPDATE bs_services SET `default`='y' WHERE id='{$id}'";
        $result = $mysqli->query($sql) or die("err: " . $mysqli->error() . $sql);
    }


    if (!empty($_REQUEST["edit_page"]) && $_REQUEST["edit_page"] == "yes") {
        if ($deposit > 0 && $deposit <= 100) {
            $msg = MSG_SRVSAVE;
            $deposit = $deposit / 100;
            if (empty($id)) {
                $sql = "SELECT * FROM bs_services";
                $res = $mysqli->query($sql);
                if ($res->num_rows > 0) {
                    $default = 'n';
                } else {
                    $default = 'y';
                }
                $sql = "INSERT INTO bs_services (type,name,date_created,	autoconfirm,fromName,fromEmail,show_event_titles,show_event_image,show_available_seats,`default`,`deposit`)
                VALUES ('d','" . $name . "','" . DATE . "','{$autoconfirm}','{$fromName}','{$fromEmail}','{$show_event_titles}','{$show_event_image}','{$show_available_seats}','{$default}','{$deposit}')";
                $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to create new service.");
                $id = $mysqli->insert_id;

                $query = "INSERT INTO `bs_service_days_settings` (`idService`, `spots`, `description`, `maxDays`,`minDays`, `daysBefore`,`payment_method`,`coupon`,`startDay`,`showPrice`)
                      VALUES ('{$id}', '{$spots}', '{$description}', '{$maxDays}','{$minDays}', '{$daysBefore}','{$payment_method}','{$coupon}','{$startDay}','{$showPrice}');";
                $result = $mysqli->query($query) or die("oopsy, error occured when tryin to create new service settings.<br>");
                $msg = MSG_SRVUPD;
            }

            if (!empty($_FILES['picture']['name'])) {
                $fileName = mktime();
                $imgPathUrl == null;
                $photoFileNametmp = $_FILES['picture']['name'];
                $fileNamePartstmp = explode(".", $photoFileNametmp);
                $counter2 = count($fileNamePartstmp) - 1;
                $fileExtensiontmp = strtolower($fileNamePartstmp[$counter2]); // part behind last dot

                if ($demo) {
                    $imgPath = $baseDir . "images/defaultEvent.jpg";
                } else {
                    $imgPath = uploadFile($_FILES['picture'], $_SERVER['DOCUMENT_ROOT'] . $baseDir . "uploads/" . $fileName . "." . $fileExtensiontmp);

                    if (!$imgPath['error']) {
                        $imgPathUrl = "uploads/" . $fileName . "." . $fileExtensiontmp;


                        $sql = "SELECT img FROM bs_service_days_settings WHERE idService='" . $id . "'";
                        $result = $mysqli->query($sql) or die("oopsy, error when tryin to get images 1");
                        @unlink($_SERVER['DOCUMENT_ROOT'] . $baseDir . mysqli_result($result, 'img'));

                        $sSQL = "UPDATE bs_service_days_settings SET img='" . $imgPathUrl . "' WHERE idService='" . $id . "'";
                        $mysqli->query($sSQL) or die("Invalid query: " . $mysqli->error() . " - $sSQL");
                    } else {
                        addMessage($imgPath['error'], "error");
                    }
                }
            }

            $sql = "UPDATE bs_services SET
                    type='d',
                    name='" . $name . "' ,
                    autoconfirm='" . $autoconfirm . "' ,
                    fromName='" . $fromName . "' ,
                    fromEmail='" . $fromEmail . "', 
                    `show_event_titles`='" . $show_event_titles . "',
                    `show_event_image`='" . $show_event_image . "',
                    `show_available_seats` = '" . $show_available_seats . "',
                    `deposit` = '" . $deposit  . "',
                    `delBookings` = '" . $delBookings . "'

                    WHERE id='" . $id . "'";
            $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to update service." . $mysqli->error() . " - $sql");

            $query = "UPDATE `bs_service_days_settings` SET

                    `spots` = '{$spots}',
                    `description` = '{$description}',
                    `maxDays` = '{$maxDays}',
                    `minDays` = '{$minDays}',
                    `daysBefore` = '{$daysBefore}',
                    `payment_method`='{$payment_method}',
                    `coupon` ='" . $coupon . "',
                    `startDay` ='" . $startDay . "',
                    `showPrice`=" . $showPrice . "
                    WHERE `idService` = '{$id}'";
            $result = $mysqli->query($query) or die("oopsy, error occured when tryin to update service." . $mysqli->error() . " - $query");

            # add days intervals

            $_dateFrom = (!empty($_REQUEST["_dateFrom"])) ? $_REQUEST["_dateFrom"] : array();
            $_dateTo = (!empty($_REQUEST["_dateTo"])) ? $_REQUEST["_dateTo"] : array();
            $_price = (!empty($_REQUEST["_price"])) ? $_REQUEST["_price"] : array();
            if (count($_dateFrom) > 0) {
                foreach ($_dateFrom as $k => $v) {
                    if ($_dateFrom[$k] != '0000-00-00' && $_dateTo[$k] != '0000-00-00' && !empty($_dateTo[$k]) && !empty($_dateFrom[$k])/*&& !empty($_price[$k])*/) {

                        $fromDate = date("Y-m-d", strtotime("$_dateFrom[$k] 2000"));
                        $toDate = date("Y-m-d", strtotime("$_dateTo[$k] 2000"));

                        if ($fromDate >= $toDate) {
                            addMessage("Incorrect days interval", "error");
                        } else {

                            $query = "INSERT INTO `bs_schedule_days` (idService,dateFrom,dateTo,price)
                                      VALUES ('{$id}','{$fromDate}','{$toDate}','{$_price[$k]}')";
                            $mysqli->query($query) or die("Invalid query: " . $mysqli->error() . " - $query");
                        }
                    }
                }
            }

            $dateFrom = (!empty($_REQUEST["dateFrom"])) ? $_REQUEST["dateFrom"] : array();
            $dateTo = (!empty($_REQUEST["dateTo"])) ? $_REQUEST["dateTo"] : array();
            $price = (!empty($_REQUEST["price"])) ? $_REQUEST["price"] : array();

            if (count($dateFrom) > 0) {
                foreach ($dateFrom as $k => $v) {
                    if ($dateFrom[$k] != '0000-00-00' && $dateTo[$k] != '0000-00-00'  && !empty($dateTo[$k]) && !empty($dateFrom[$k])/* !empty($price[$k])*/) {
                        $fromDate = date("Y-m-d", strtotime("$dateFrom[$k] 2000"));
                        $toDate = date("Y-m-d", strtotime("$dateTo[$k] 2000"));
                        if ($fromDate >= $toDate) {
                            addMessage("Incorrect days interval", "error");
                        } else {

                            $query = "UPDATE `bs_schedule_days` SET
                                            idService='{$id}',
                                            dateFrom='{$fromDate}',
                                            dateTo='{$toDate}',
                                            price='{$price[$k]}'
                                            WHERE idItem={$k}";
                            $mysqli->query($query) or die("Invalid query: " . $mysqli->error() . " - $query");
                        }
                    }
                }
            }
            addMessage($msg, "success");
        } else {
            addMessage(INCORRECT_DEPOSIT);
        }
    }
    if (!empty($id)) {
        $sSQL = "SELECT * FROM bs_services WHERE id='{$id}'";
        $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
        if ($row = $result->fetch_assoc()) {
            foreach ($row as $key => $value) {
                $$key = $value;
            }
        }
        $result->free_result();

        $sSQL = "SELECT * FROM bs_service_days_settings WHERE idService='{$id}'";
        $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
        if ($row = $result->fetch_assoc()) {
            foreach ($row as $key => $value) {
                $$key = $value;
            }
        }
        $result->free_result();
        $deposit = $deposit * 100;
    }
    ?>
    <?php include "includes/admin_header.php"; ?>
    <script type="text/javascript">
        $(function() {
            updateDatePickers()
        })

        function addInterval(el) {
            var data = '<div class="row_a"><div class="dates-row date"> <label><?php echo TXT_DAY_SRV_FROM ?>:</label> <input type="text" name="_dateFrom[]" value="" class="datepicker small left"/> <div class="clear"></div></div><div class="dates-row date"> <label><?php echo TXT_DAY_SRV_TO ?>:</label> <input type="text" name="_dateTo[]" value="" class="datepicker  small left"/> <div class="clear"></div></div><div style="padding-right: 92px;" class="dates-row date"> <label><?php echo TXT_DAY_SRV_PRICE ?>:</label> <input type="text" name="_price[]" class="small1 left" value="" onkeyup="noAlpha(this)"> <div class="clear"></div></div><a href="#" class="adj_SE_remove positioned_smdadd" >delete this</a>  </div>';
            $(el).before(data);
            updateDatePickers();
            findAndBindSE();
        }

        function updateDatePickers() {
            $(".datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "MM dd",
                changeYear: false,
                showOn: "button",
                buttonImage: "images/new/calendar.png",
                buttonImageOnly: true
            });
        }

        function noAlpha(obj) {
            reg = /[^0-9.,]/g;
            obj.value = obj.value.replace(reg, "");
        }

        function checkBookings(el) {
            var $data = $(el).attr("href");

            $.ajax({
                url: "ajax/checkDeletedDayAvailability.php",
                success: function(e) {
                    console.log(e.mess)
                    if (e.res) {
                        window.location.href = "?" + $data;
                    } else {
                        if (confirm(e.mess)) {
                            window.location.href = "?" + $data;
                        }
                    }
                },
                data: $data,
                async: false,
                dataType: "json"
            })

            return false;
        }
    </script>

    <div id="content">




        <?php getMessages(); ?>
        <div class="content_block">

            <h2><?php if (!empty($id)) {
                        echo  TXT_DAY_SRV_EDIT;
                    } else {
                        echo TXT_DAY_SRV_ADD;
                    } ?>

                <?php if ($default != 'y') {
                        if (!empty($id)) { ?>
                        <form class="formServiceDefault" action="" method="post">
                            <input type="hidden" name="id" value="<?php echo $id ?>" />
                            <input type="hidden" name="action" value="setDefault" />
                            <button type="submit"><span><?php echo MAKE_DAFAULT ?></span></button>
                        </form>
                    <?php }
                        } else { ?>
                    <span>( <?php echo THIS_DEFAULT_SERVICE ?>)</span>
                <?php } ?>
                <a href="bs-services.php"><?php echo MSG_BACK ?></a></h2>

            <p><?php echo BS_SERV_TXT_MD ?></p>
            <hr />

            <form action="bs-services_days-add.php" enctype="multipart/form-data" method="post" name="ff1">




                <div class="form-row">
                    <div class="cell third">
                        <label><?php echo TXT_DAY_SRV_TITLE ?></label>
                        <input type="text" name="name" id="title" value="<?php echo $name ?>" />

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
                        <img src='images/info.png' border="0" class="tipTip adj_add_events_tip" title="<?php echo PAYMENT_MSG ?>" />
                    </div>
                    <div class="cell short1">
                        <label><?php echo REQUIRED_DEPOSIT ?>&nbsp;%</label>

                        <input type="text" name="deposit" id="deposit" value="<?php echo $deposit ?>" class="small1 left" onkeyup="noAlpha(this)" />
                        <img src='images/info.png' border="0" class="left tipTip imgCenter" title="<?php echo NUMB_PLZ ?>" />


                    </div>
                    <div class="cell medium3">
                        <div class="valign">
                            <label>&nbsp;</label>
                            <input type="checkbox" name="coupon" id="coupon" value="1" <?php echo $coupon ? "checked" : "" ?> />
                            <?php echo MXM_COUPON ?>
                            <img src='images/info.png' border="0" class="tipTip" title="<?php echo TTIP_1 ?>" />
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-row">
                    <div class="cell short1 shorter">
                        <label><?php echo TXT_DAY_MAX_SPACES ?></label>
                        <input type="text" name="spots" id="spots" value="<?php echo $spots ?>" class="smaller left" />
                        <img src='images/info.png' border="0" class=" tipTip imgCenter" title="<?php echo TTIP_5 ?>" />
                    </div>
                    <div class="cell short1 shorter">
                        <label><?php echo TXT_DAY_SRV_MIN_DAYS ?></label>
                        <input type="text" name="minDays" id="minDays" value="<?php echo $minDays ?>" class="smaller left" />
                        <img src='images/info.png' border="0" class=" tipTip imgCenter" title="<?php echo TTIP_6_1 ?>" />
                    </div>
                    <div class="cell short1 shorter">
                        <label><?php echo TXT_DAY_SRV_MAX_DAYS ?></label>
                        <input type="text" name="maxDays" id="maxDays" value="<?php echo $maxDays ?>" class="smaller left" />
                        <img src='images/info.png' border="0" class=" tipTip imgCenter" title="<?php echo TTIP_6 ?>" />
                    </div>
                    <div class="cell short1 shorter">
                        <label><?php echo TXT_DAY_SRV_DAYS_BEFORE ?></label>
                        <input type="text" name="daysBefore" id="daysBefore" value="<?php echo $daysBefore  ?>" class="smaller left" />
                        <img src='images/info.png' border="0" class=" tipTip imgCenter" title="<?php echo TTIP_7 ?>" />
                    </div>
                    <div class="cell medium3 shorter">
                        <label><?php echo CALND_WEEK_STARTS ?></label>
                        <div class="valign">

                            <input type="radio" name="startDay" id="startDay" value="0" <?php echo $startDay == "0" ? "checked" : "" ?> /> <?php echo SUN ?>&nbsp;
                            <input type="radio" name="startDay" id="startDay" value="1" <?php echo $startDay == "1" ? "checked" : "" ?> /> <?php echo MON ?>
                        </div>
                    </div>
                    <div class="cell medium3">
                        <label><?php echo HIDE_PRICE ?></label>

                        <div class="valign">
                            <input type="radio" name="showPrice" id="showPrice" value="1" <?php echo $showPrice == "1" ? "checked" : "" ?> /> <?php echo YES; ?>
                            &nbsp;
                            <input type="radio" name="showPrice" id="showPrice" value="0" <?php echo $showPrice == "0" ? "checked" : "" ?> /> <?php echo NO; ?>
                            &nbsp;
                        </div>

                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-row">

                    <div class="cell third">
                        <label><?php echo ALLOW_DEL_BOOKINGS ?><img src='images/info.png' border="0" class="tipTip" title="<?php echo htmlspecialchars(ALLOW_DEL_BOOKINGS_NOTES) ?>" /></label>
                        <input type="radio" name="delBookings" id="delBookings" value="y" <?php echo $delBookings == "y" ? "checked" : "" ?> /> <?php echo YES; ?> &nbsp;
                        <input type="radio" name="delBookings" id="delBookings" value="n" <?php echo $delBookings == "n" ? "checked" : "" ?> /> <?php echo NO; ?> &nbsp;

                    </div>
                    <div class="cell third">
                        <label><?php echo AUTOCONFIRM ?>&nbsp;<img src='images/info.png' border="0" class="tipTip" title="<?php echo AUTOCONFIRM_MSG ?>" /></label>
                        <input type="radio" name="autoconfirm" id="autoconfirm" value="1" <?php echo $autoconfirm == "1" ? "checked" : "" ?> /> <?php echo YES; ?> &nbsp;
                        <input type="radio" name="autoconfirm" id="autoconfirm" value="0" <?php echo $autoconfirm == "0" ? "checked" : "" ?> /> <?php echo NO; ?>

                    </div>
                    <div class="clear"></div>
                </div>

                <div class="form-row">
                    <div class="cell">
                        <label><?php echo TXT_DAY_SRV_DESCR ?></label>
                        <textarea name="description" id="description" style="width: 660px;height: 150px;" /><?php echo $description ?></textarea>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-row">
                    <div class="cell">
                        <label><?php echo TXT_DAY_SRV_IMAGE ?></label>
                        <input type="file" name="picture" class="txt" />
                        <img src='images/info.png' border="0" class="tipTip" title="<?php echo IMGJPG ?>" />
                    </div>
                    <?php if (isset($img) && $img != '') { ?>
                        <div class="clear"></div>
                        <div class="cell">
                            <label><?php echo CRNT_SRV_IMG ?></label>

                            <a href="?id=<?php echo $id ?>&delImg=yes"><?php echo DEL_IMG ?></a><br />
                            <img height="100" src="<?php echo $img ?>" />
                        </div>
                    <?php } ?>
                    <div class="clear"></div>
                </div>
                <hr />
                <div class="form-row">
                    <h3><?php echo TXT_DAY_SRV_AVAILABILITY ?></h3>
                    <?php
                        if (!empty($id)) {
                            $sql = "SELECT * FROM bs_schedule_days WHERE idService={$id} ORDER BY dateFrom";
                            $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to update service.");
                            while ($row = $result->fetch_assoc()) {
                                ?>
                            <div class="row_a ">
                                <div class="dates-row date">
                                    <label><?php echo TXT_DAY_SRV_FROM ?></label>
                                    <input type="text" name="dateFrom[<?php echo $row['idItem'] ?>]" value="<?php echo date("F d", strtotime($row['dateFrom'])) ?>" class="datepicker small left" />

                                    <div class="clear"></div>
                                </div>
                                <div class="dates-row date">
                                    <label><?php echo TXT_DAY_SRV_TO ?></label>
                                    <input type="text" name="dateTo[<?php echo $row['idItem'] ?>]" value="<?php echo date("F d", strtotime($row['dateTo'])) ?>" class="datepicker  small left" />

                                    <div class="clear"></div>
                                </div>
                                <div class="dates-row nomr  date" style="padding-right:93px; ">
                                    <label><?php echo TXT_DAY_SRV_PRICE ?></label>
                                    <input type="text" name="price[<?php echo $row['idItem'] ?>]" class="small1 left" onkeyup="noAlpha(this)" value="<?php echo $row['price'] ?>">
                                    <div class="left delCenter" style="width: 0;position: relative;left: 15px;top: 5px;">
                                        <a href="id=<?php echo $id ?>&action=del_schedule&ids=<?php echo $row['idItem'] ?>" onclick="checkBookings(this);return false">
                                            <img src="images/del_item.png">
                                        </a>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                            </div>
                    <?php }
                        } ?>
                </div>
                <div class="form-row">
                    <div class="row_a">
                        <div class="dates-row date">
                            <label><?php echo TXT_DAY_SRV_FROM ?></label>
                            <input type="text" name="_dateFrom[]" value="" class="datepicker small left" />

                            <div class="clear"></div>
                        </div>
                        <div class="dates-row date">
                            <label><?php echo TXT_DAY_SRV_TO ?></label>
                            <input type="text" name="_dateTo[]" value="" class="datepicker  small left" />

                            <div class="clear"></div>
                        </div>
                        <div class="dates-row date" style="padding-right: 92px;">
                            <label><?php echo TXT_DAY_SRV_PRICE ?></label>
                            <input type="text" name="_price[]" class="small1 left" value="" onkeyup="noAlpha(this)">

                            <div class="clear"></div>
                        </div>

                    </div>
                    <a class="buttonAddSmall _valign" onclick="addInterval(this)" href="javascript:;"><span>add</span></a>

                </div>
                <div class="clear"></div>


                <hr />
                <div class="form-row">
                    <h3><?php echo EMAIL_SETTINGS ?></h3>
                    <div class="cell third" style="margin-right: 25px;">
                        <label><?php echo EMAIL_FROM_NAME ?></label>

                        <input type="text" name="fromName" id="fromName" value="<?php echo $fromName ?>" /><img src='images/info.png' border="0" class=" tipTip imgCenter" title="<?php echo TTIP_3 ?>" />

                    </div>
                    <div class="cell half">
                        <label><?php echo EMAIL_FROM_EMAIL ?></label>

                        <input type="text" name="fromEmail" id="fromEmail" value="<?php echo $fromEmail ?>" /><img src='images/info.png' border="0" class=" tipTip imgCenter" title="<?php echo TTIP_4 ?>" />

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