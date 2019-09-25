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

if ($_SESSION["logged_in"] != true) {
    header("Location: admin.php");
    exit();
} else {
    bw_do_action("bw_load");
    bw_do_action("bw_admin");

    if (getOption('show_home_popup') === false) {
        updateOption('show_home_popup', 'show');
    }

    $bgClass = "even"; // default first row highlighting CSS class

    //PAGES TABLE  GENERATION TO SHOW IN HTML BELOW
    $sql = "SELECT br.*,e.title,s.type,s.name as sname FROM bs_reservations br
            LEFT JOIN bs_events e ON e.id=br.eventID
            INNER JOIN bs_services s ON s.id=br.serviceID
			ORDER BY br.dateCreated DESC LIMIT 15";
    $result = $mysqli->query($sql) or die("error getting bookings from db");
    if ($result->num_rows > 0) {
        while ($rr = $result->fetch_assoc()) {

            if (empty($rr['eventID'])) {
                if ($rr["type"] == 't') {
                    $editable = "<a href=\"//" . MAIN_URL . "bs-bookings-edit.php?id=" . $rr["id"] . "\" class=\"greedButton\">Edit</a>";
                } else {
                    $editable = "<a href=\"//" . MAIN_URL . "bs-bookings_day-edit.php?id=" . $rr["id"] . "\" class=\"greedButton\">Edit</a>";
                }
            } else {

                $editable = "<a href=\"//" . MAIN_URL . "bs-bookings_event-edit.php?id=" . $rr["id"] . "\" class=\"greedButton\">Edit</a>";
            }
            //$editable.="&nbsp;&nbsp;<a href='bs-bookings.php?id=" . $rr["id"] . "&amp;del=yes'><img src='images/delete_16.png' border=\"0\"></a>";
            $bgClass = ($bgClass == "even" ? "odd" : "even");

            $files_table .= "<tr class=\"" . $bgClass . "\">";
            $files_table .= "";
            $files_table .= "<td height=\"24\">" . (empty($rr["title"]) ? $rr["sname"] : $rr["title"]) . "</td>";
            $files_table .= "<td>" . $rr["name"] . "</td>";
            $files_table .= "<td>{$rr["phone"]}</td>";
            $files_table .= "<td><a class='adj_mail_link' title='Send Email' href='mailto:" . $rr["email"] . "'>{$rr["email"]}</a></td>";

            $files_table .= "<td>" . getDateFormat($rr["dateCreated"]) . date(((getTimeMode()) ? " g:i a" : " H:i"), strtotime($rr["dateCreated"])) . "</td>";
            $status = '';
            switch ($rr['status']) {
                case "1":
                    $status = BOOKING_FRM_CONFIRMED;
                    break;
                case "2":
                    $status = BOOKING_FRM_NOTCONFIRMED;
                    break;
                case "3":
                    $status = BOOKING_FRM_CANCELLED;
                    break;
                case "4":
                    $status = BOOKING_FRM_PAID;
                    break;
                case "5":
                    $status = BOOKING_FRM_USERCANCELLED;
                    break;
            }
            $files_table .= "<td>";
            if (empty($rr["eventID"])) {
                $qq = "SELECT * FROM bs_reservations_items WHERE reservationID='" . $rr["id"] . "'";
                $res = $mysqli->query($qq);
                if ($res->num_rows > 0) {
                    if ($rr["type"] == 't') {
                        while ($r2 = $res->fetch_assoc()) {
                            $files_table .= getDateFormat($r2["reserveDateFrom"]) . date(((getTimeMode()) ? " g:i a" : " H:i"), strtotime($r2["reserveDateFrom"])) . " to " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($r2["reserveDateTo"])) . "<br/>";
                        }
                    } else {
                        $r2 = $res->fetch_assoc($res);
                        $files_table .= getDateFormat($r2["reserveDateFrom"]) . " to " . getDateFormat($r2["reserveDateTo"]) . "<br/>";
                    }
                }
            } else {
                if ($rr["date"] != "0000-00-00") {
                    $files_table .= getDateFormat($rr["date"]);
                }
            }
            $files_table .= "</td>";
            $files_table .= "<td align='center'>" . $rr["qty"] . "</td>";
            $files_table .= "<td align='center'>" . (empty($rr["coupon"]) ? "" : $rr["coupon"]) . "</td>";
            $files_table .= "<td><span><span class='adj_center_text_table'>" . $status . "</span>&nbsp;&nbsp;" . $editable . "</span></td>";
        } // end of all records from db query (end of while loop)
        //show button to complete record deletion if proper permissions.

        //$files_table .= "<tr><td height=\"32\" colspan=\"7\"><input name=\"delete_files\" type=\"submit\" value=\"" . ADM_BTN_DELETE . "\"  /></td></tr>";
    } else {
        //0 files found in database. ( end of IF mysql_num_rows > 0 )
        $files_table .= "<tr><td colspan=\"7\">" . ADM_MSG4 . "</td></tr>";
    }


    if (empty($_SESSION['show_popup'])) {
        $_SESSION['show_popup'] = 1;
    } else {
        $_SESSION['show_popup']++;
    }
    include "includes/admin_header.php";
    ?>
        <script type="text/javascript">
            <?php if ($_SESSION['show_popup'] == 1 && getOption('show_home_popup') == 'show') { ?>
                $(function() {
                    $.fn.colorbox({
                        inline: true,
                        href: "#popup",
                        overlayClose: false,
                        onClosed: popUpClose
                    })

                })
            <?php } ?>

            function popUpClose() {
                if ($("#_popup").is(':checked')) {
                    $.post("ajax/checkShowPopup.php", {
                        show: false
                    }, function(data) {});
                }

            }
        </script>


        <link type="text/css" href="./js/datatable/css/jquery.dataTables.css" rel="stylesheet" />
        <div id="content">


            <div class="content_block ">

                <div class="padd">
                    <h2><?php echo ADMIN_WELCOME ?></h2>
                    <div class="dashboard links">
                        <h3>QUICK LINKS</h3>
                        <ul class="dashboard-links">
                            <li><a href="bs-schedule.php"><img src="./images/dash_logo1.jpg" border="0" /></a>
                                <span><a href="bs-schedule.php"><?php echo MENU1 ?></a></span>
                            </li>
                            <li><a href="bs-bookings.php"><img src="./images/dash_logo2.jpg" border="0" /></a>
                                <span><a href="bs-bookings.php"><?php echo MENU2 ?></a></span>
                                <a href="bs-bookings.php"><?php echo ucfirst(MENU2_1); ?></a>
                                <a href="bs-reserve-view.php"><?php echo ucfirst(MENU2_2); ?></a>
                                <a href="bs-reserve.php"><?php echo ucfirst(MENU2_3); ?></a>
                            </li>
                            <li><a href="bs-services.php"><img src="./images/dash_logo5.jpg" border="0" /></a>
                                <span><a href="bs-services.php"><?php echo MENU3; ?></a></span>
                                <a href="bs-services-add.php"><?php echo ucfirst(MENU3_1); ?></a>
                            </li>
                            <li><a href="bs-events.php"><img src="./images/dash_logo3.jpg" border="0" /></a>
                                <span><a href="bs-events.php"><?php echo MENU4 ?></a></span>
                                <a href="bs-events-add.php"><?php echo ucfirst(MENU4_1) ?></a>
                            </li>
                            <li><a href="bs-coupons.php"><img src="./images/dash_logo4.jpg" border="0" /></a>
                                <span><a href="bs-coupons.php"><?php echo MENU5 ?></a></span>
                                <a href="bs-coupon-add.php"><?php echo ucfirst(MENU5_1) ?></a>
                            </li>

                        </ul>
                    </div>
                    <div class="dashboard transactions">
                        <h3>Transactions log</h3>
                        <?php
                            $sql = "SELECT * FROM bs_transactions ORDER BY dateCreated DESC LIMIT 6";
                            $result = $mysqli->query($sql) or die("error getting transactions from db");
                            $odd = "odd";
                            if ($result->num_rows > 0) { ?>
                            <table border="0" cellpadding="3" cellspacing="0" width="100%">
                                <?php while ($row = $result->fetch_assoc()) {

                                            if (empty($row['eventID'])) {
                                                $serviceID = getBooking($row["reservationID"], "serviceID");
                                                $rerviceType = getService($serviceID, 'type');
                                                if ($rerviceType == 't') {
                                                    $editable = "bs-bookings-edit.php?id=" . $row["reservationID"];
                                                } else {
                                                    $editable = "bs-bookings_day-edit.php?id=" . $row["reservationID"];
                                                }
                                            } else {

                                                $editable = "bs-bookings_event-edit.php?id=" . $row["reservationID"];
                                            }
                                            ?>

                                    <tr class="<?php echo $odd = $odd == 'odd' ? "" : "odd" ?>">
                                        <td><a href="<?php echo $editable ?>"><?php echo $row['payer_email'] ?></a></td>
                                        <td><?php echo getDateFormat($row['dateCreated']) ?></td>
                                        <td>$<?php echo number_format($row['amount'], 2) ?></td>
                                    </tr>

                                <?php } ?>
                            </table><br />

                            &nbsp;<a href="bs-bookings.php">View all</a>
                        <?php } ?>
                    </div>
                    <div style="clear: both"></div>
                    <div class="dashboard stat">
                        <h3 style="position: relative;padding: 10px 0px;"><?php echo BASIC_STATS ?>
                            <div class="DashboardSelectCalendar">
                                <?php
                                    $sql = "SELECT * FROM bs_services";
                                    $res = $mysqli->query($sql);
                                    if ($res->num_rows > 1) {
                                        ?>
                                    <select name="DashboardSelectCalendar" id="DashboardSelectCalendar" class="selectWTF" onchange="startGraphs('?calendar='+this.value);">

                                        <?php
                                                $sql = "SELECT * FROM bs_services";
                                                $res = $mysqli->query($sql);
                                                $serviceID = getDefaultService();
                                                while ($row = $res->fetch_assoc()) {
                                                    ?>
                                            <option value="<?php echo $row['id'] ?>" <?php echo ($serviceID == $row['id']) ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                                        <?php } ?>

                                    </select>

                                <?php } ?>
                            </div>
                        </h3>
                        <div class="grafic" id="plottedJS" style="width: 512px;height: 282px;background: none;">

                        </div>
                        <div class="info">
                            <p><?php echo BASIC_STATS_DESCR; ?></p>
                            <ul id="pgraphsDesc" style="padding-top: 10px;">

                            </ul>
                        </div>
                        <div style="clear: both"></div>
                    </div>

                    <div class="dashboard stat">
                        <h3><?php echo LASTED_BOOKINGS ?></h3>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="dataTable">
                            <thead>
                                <tr class="topRow">
                                    <th width="118" height="30" align="left"><strong><?php echo BOOKING_LST_EVENT; ?></strong></th>
                                    <th width="125" align="left"><strong><?php echo BOOKING_LST_NAME; ?></strong></th>
                                    <th width="130" align="left"><strong><?php echo BOOKING_LST_PHONE; ?></strong></th>
                                    <th width="113" align="left"><strong><?php echo BOOKING_LST_EMAIL; ?></strong></th>
                                    <th width="90" align="left"><strong><?php echo BOOKING_LST_ON; ?></strong></th>
                                    <th width="97" align="left"><strong><?php echo BOOKING_LST_DATES; ?></strong></th>
                                    <th width="29" align="left"><strong><?php echo BOOKING_LST_SPACES; ?></strong></th>
                                    <th width="29" align="center"><strong><?php echo BOOKING_LST_COUPON; ?></strong></th>
                                    <th width="174" align="left"><strong><?php echo BOOKING_LST_STATUS; ?></strong></th>
                                </tr>
                            </thead>
                            <?php echo $files_table; ?>
                            <!-- PAGING NAVIGATION LINKS ROW -->

                            <!-- PAGING NAVIGATION LINKS ROW END -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div style="display: none">
            <div id="popup">
                <div class="top">
                    <input type="checkbox" name="popup" id="_popup" value="1" />&nbsp;
                    <?php echo HSP1 ?>
                </div>
                <div class="cell left">
                    <h1><?php echo HSP2 ?></h1>
                    <p><?php echo HSP3 ?></p>
                    <p><?php echo HSP4 ?></p>
                    <p><?php echo HSP5 ?></p>

                </div>
                <div class="cell left nomr">
                    <h2><?php echo HSP6 ?></h2>
                    <img src="./images/schema.png" />
                </div>
                <div class="clear"></div>
                <div class="cell left">
                    <h2><?php echo HSP7 ?></h2>
                    <a href="http://codecanyon.net/item/bookingwizz-credit-card-payments/2424321?ref=Convergine" target="_blank">BookingWizz Credit Card Payments</a><br />
                    <a href="http://codecanyon.net/item/bookingwizz-for-wordpress/2602153?ref=Convergine" target="_blank">BookingWizz for Wordpress</a>
                    <a href="http://codecanyon.net/item/bookingwizz-sms-reminders/6593053?ref=Convergine" target="_blank">BookingWizz SMS Reminders</a>
                </div>
                <div class="cell left nomr">
                    <h2><?php echo HSP8 ?></h2>
                    <a href="http://www.convergine.com/questions-and-answers/" target="_blank"><?php echo HSP9 ?></a><br />
                    <a href="help/index.html"><?php echo HSP10 ?></a>
                </div>
            </div>
        </div>
    <?php
        include "includes/admin_footer.php";
    }
    ?>