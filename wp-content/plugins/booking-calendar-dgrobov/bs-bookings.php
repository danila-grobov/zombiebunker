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
    //get access level
    bw_do_action("bw_load");
    bw_do_action("bw_admin");

    //get service id
    $serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();;

    ######################### DO NOT MODIFY (UNLESS SURE) END ########################

    $files_table = ""; //var with php generated html table.
    //"delete selected " action processing.
    if (!empty($_REQUEST["files_delete"]) && $_REQUEST["files_delete"] == "yes") {
        $filesToDel = (!empty($_REQUEST["filesToDel"])) ?  $_REQUEST["filesToDel"] : '';
        if (is_array($_POST['filesToDel'])) {
            if (join(",", $_POST['filesToDel']) != '') {
                //delete booking from database
                $sql = "DELETE FROM bs_reservations_items WHERE reservationID IN ('" . join("','", $_POST['filesToDel']) . "')";
                $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete bookings");
                $sql = "DELETE FROM bs_reservations WHERE id IN ('" . join("','", $_POST['filesToDel']) . "')";
                $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete bookings");

                addMessage(ADM_MSG3, "warning");
            }
        }
    }
    if (!empty($_REQUEST["del"]) && $_REQUEST["del"] == "yes") {
        $filesToDel = (!empty($_REQUEST["id"])) ? strip_tags(str_replace("'", "`", $_REQUEST["id"])) : '';

        //delete booking from database
        $sql = "DELETE FROM bs_reservations_items WHERE reservationID= '{$filesToDel}'";
        $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete bookings");
        $sql = "DELETE FROM bs_reservations WHERE id= '{$filesToDel}'";
        $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete bookings");

        addMessage(ADM_MSG3, "warning");
    }


    //PAGES TABLE  GENERATION TO SHOW IN HTML BELOW
    $sql = "SELECT br.*,e.title,s.type FROM bs_reservations br
                          LEFT JOIN bs_events e ON e.id=br.eventID
                          INNER JOIN bs_services s ON s.id=br.serviceID
			  WHERE br.serviceID={$serviceID} 
			  ORDER BY br.dateCreated DESC";
    $result = $mysqli->query($sql) or die("error getting bookings from db");
    if ($result->num_rows > 0) {
        while ($rr = $result->fetch_assoc()) {

            if (empty($rr['eventID'])) {
                if ($rr["type"] == 't') {
                    $editable = "<a class=\"greedButton\" href=\"bs-bookings-edit.php?id=" . $rr["id"] . "\">Edit</a>";
                } else {
                    $editable = "<a class=\"greedButton\" href=\"bs-bookings_day-edit.php?id=" . $rr["id"] . "\">Edit</a>";
                }
            } else {

                $editable = "<a class=\"greedButton\" href=\"bs-bookings_event-edit.php?id=" . $rr["id"] . "\">Edit</a>";
            }
            //$editable.="&nbsp;&nbsp;<a href='bs-bookings.php?id=" . $rr["id"] . "&amp;del=yes&amp;serviceID=".$serviceID."'><img src='images/delete_16.png' border=\"0\"></a>";

            $bgClass = ($bgClass == "even" ? "odd" : "even");

            $files_table .= "<tr class=\"" . $bgClass . "\">";
            $files_table .= "";
            $files_table .= "<td height=\"24\"><input name=\"filesToDel[]\" type=\"checkbox\" value=\"" . $rr["id"] . "\" /></td>";
            $files_table .= "<td>" . $rr["title"] . "</td>";
            $files_table .= "<td>" . $rr["name"] . "</td>";

            $files_table .= "<td><a href='mailto:" . $rr["email"] . "' class='adj_mail_link' >" . $rr["email"] . "</a></td>";
            $files_table .= "<td>" . $rr["phone"] . "</td>";
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
                        $i = 0;
                        while ($r2 = $res->fetch_assoc()) {
                            $files_table .= ($i == 0 ? "<b>" . getDateFormat($r2["reserveDateFrom"]) . "</b><br/>" : "") .
                                date(((getTimeMode()) ? " g:i a" : " H:i"), strtotime($r2["reserveDateFrom"])) . " to " .
                                date((getTimeMode()) ? "g:i a" : "H:i", strtotime($r2["reserveDateTo"])) . "<br/>";
                            $i++;
                        }
                    } else {
                        $r2 = $res->fetch_assoc();
                        $files_table .= getDateFormat($r2["reserveDateFrom"]) . " to " . getDateFormat($r2["reserveDateTo"]) . "<br/>";
                    }
                }
            } else {
                if ($rr["date"] != "0000-00-00") {
                    $files_table .= "<b>" . getDateFormat($rr["date"]) . "</b>";
                }
            }
            $files_table .= "</td>";
            $files_table .= "<td align='center'>" . $rr["qty"] . "</td>";
            $files_table .= "<td align='center'>" . (($rr["young"] === '1') ? "yes" : "no") . "</td>";
            $files_table .= "<td align='center'>" . $rr["coupon"] . "</td>";
            $files_table .= "<td>" . $status . $editable . "</td>";
            //$files_table .= "<td>" . $editable . "</td></tr>";
        } // end of all records from db query (end of while loop)
        //show button to complete record deletion if proper permissions.

        //$files_table .= "<tr><td height=\"32\" colspan=\"7\"><input name=\"delete_files\" type=\"submit\" value=\"" . ADM_BTN_DELETE . "\"  /></td></tr>";
        $showTheCheckbox = true;
    } else {
        $showTheCheckbox = false;
        //0 files found in database. ( end of IF mysql_num_rows > 0 )

        //$files_table .="<tr><td></td><td >".ADM_MSG4."</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
    }
    ?>
        <?php include "includes/admin_header.php"; ?>
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
                        null,
                        null,
                        null,
                        {
                            "bSortable": false
                        }

                    ],
                    'oLanguage': {
                        "sEmptyTable": " ",
                        /* appears in the top middle of the empty table */
                        "sInfoEmpty": "<?php echo ADM_MSG4; ?>"
                    }

                });
                $("#grid_wrapper").append('<div class="deleteBtton"><input name="delete_files" type="submit" value="<?php echo BTN_DELETESEL ?>"  /></div>');
                $('select[name="grid_length"]').msDropdown({
                    mainCSS: 'rows'
                });
            });
        </script>
        <div id="content">

            <?php getMessages(); ?>
            <div class="content_block">
                <div class="adj_bookings_905">
                    <h2><?php echo PAGE_TITLE1 ?></h2>

                    <div class="bar">
                        <?php
                            $sql = "SELECT * FROM bs_services";
                            $res = $mysqli->query($sql);
                            if ($res->num_rows > 1) {
                                ?>
                            <div class="servicesList">
                                <p>View all bookings for the service</p>
                                <form name="ff1" action="" id="ff1" method="post">
                                    <div class="left mrgR">
                                        <label>Select Service:</label>
                                        <select name="serviceID" id="serviceID" class="select">

                                            <?php
                                                    $sql = "SELECT * FROM bs_services";
                                                    $res = $mysqli->query($sql);
                                                    while ($row = $res->fetch_assoc()) {
                                                        ?>
                                                <option value="<?php echo $row['id'] ?>" <?php echo ($serviceID == $row['id']) ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                                            <?php } ?>

                                        </select>
                                    </div>

                                    <div class="left">
                                        <label>&nbsp;</label>
                                        <button type="submit"><span><?php echo SCHEDL_BTN_VIEW ?></span></button>
                                    </div>

                                    <div class="clear"></div>
                                </form>
                            </div>

                            <div style="clear:both"></div>
                        <?php } ?>
                    </div>
                    <h3 class="pleft8"><?php echo PAGE_TITLE1 ?> for <? echo getService($serviceID, 'name') ?></h3>
                    <form enctype="multipart/form-data" action="bs-bookings.php" method="post" name="ff2">
                        <input type="hidden" value="yes" name="files_delete" />
                        <input type="hidden" value="<?php echo $serviceID ?>" name="serviceID" />
                        <table class="style_checkboxes" width="100%" border="0" cellspacing="0" cellpadding="0" id="grid" class="dataTable">
                            <thead>
                                <tr class="topRow">
                                    <th width="2%" height="30" align="center"><?php if ($showTheCheckbox == true) : ?><input value="" name="selectALLCheckBoxes" type="checkbox" class="ToggleSelectAllCheckBoxes" /><?php endif; ?></th>
                                    <th width="1%" align="left"><strong><?php echo BOOKING_LST_EVENT ?></strong></th>
                                    <th width="9%" align="left"><strong><?php echo BOOKING_LST_NAME ?></strong></th>
                                    <th width="16%" align="left"><strong><?php echo BOOKING_LST_EMAIL ?></strong></th>
                                    <th width="9%" align="left"><strong><?php echo BOOKING_LST_PHONE ?></strong></th>
                                    <th width="12%" align="left"><strong><?php echo BOOKING_LST_ON ?></strong></th>
                                    <th width="15%" align="left"><strong><?php echo BOOKING_LST_DATES ?></strong></th>
                                    <th width="1%" align="left"><strong><?php echo BOOKING_LST_SPACES ?></strong></th>
                                    <th width="1%" align="left"><strong><?php echo BOOKING_LST_YOUNG ?></strong></th>
                                    <th width="7%" align="center"><strong><?php echo BOOKING_LST_COUPON ?></strong></th>
                                    <th width="20%" align="left"><strong><?php echo BOOKING_LST_STATUS ?></strong></th>

                                </tr>
                            </thead>
                            <?php echo $files_table; ?>
                            <!-- PAGING NAVIGATION LINKS ROW -->
                            <tfoot>
                                <tr class="topRow">
                                    <th width="2%" height="30" align="center">&nbsp;</th>
                                    <th width="1%" align="left"><strong><?php echo BOOKING_LST_EVENT ?></strong></th>
                                    <th width="9%" align="left"><strong><?php echo BOOKING_LST_NAME ?></strong></th>
                                    <th width="16%" align="left"><strong><?php echo BOOKING_LST_EMAIL ?></strong></th>
                                    <th width="9%" align="left"><strong><?php echo BOOKING_LST_PHONE ?></strong></th>
                                    <th width="12%" align="left"><strong><?php echo BOOKING_LST_ON ?></strong></th>
                                    <th width="15%" align="left"><strong><?php echo BOOKING_LST_DATES ?></strong></th>
                                    <th width="1%" align="left"><strong><?php echo BOOKING_LST_SPACES ?></strong></th>
                                    <th width="1%" align="left"><strong><?php echo BOOKING_LST_YOUNG ?></strong></th>
                                    <th width="7%" align="center"><strong><?php echo BOOKING_LST_COUPON ?></strong></th>
                                    <th width="20%" align="left"><strong><?php echo BOOKING_LST_STATUS ?></strong></th>

                                </tr>
                            </tfoot>
                            <!-- PAGING NAVIGATION LINKS ROW END -->
                        </table>
                    </form>
                </div>
            </div>

            <?php include "includes/admin_footer.php"; ?>
        <?php } ?>