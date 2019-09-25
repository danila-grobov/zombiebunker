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
} else {
    //get access level

    bw_do_action("bw_load");
    bw_do_action("bw_admin");

    ######################### DO NOT MODIFY (UNLESS SURE) END ########################
    $filter = "";  //default filter variable. getting rid of undefined variable exception.
    $bgClass = "even"; // default first row highlighting CSS class
    $files_table = ""; //var with php generated html table.

    //get service id
    $event = (!empty($_REQUEST["event"])) ? strip_tags(str_replace("'", "`", $_REQUEST["event"])) : '';


    //"delete selected files" action processing.
    if (!empty($_REQUEST["files_delete"]) && $_REQUEST["files_delete"] == "yes") {

        if (is_array($_POST['filesToDel'])) {
            if (join(",", $_POST['filesToDel']) != '') {
                //delete record from database
                $sql = "DELETE FROM bs_reservations_items WHERE reservationID IN ('" . join("','", $_POST['filesToDel']) . "')";
                $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete bookings");

                $sql = "DELETE FROM bs_reservations WHERE id IN ('" . join("','", $_POST['filesToDel']) . "')";
                $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete events 3");

                addMessage(MSG_ATDELETED, "warning");
            }
        }
    }
    if (!empty($_REQUEST["del"]) && $_REQUEST["del"] == "yes") {
        $filesToDel = (!empty($_REQUEST["id"])) ? strip_tags(str_replace("'", "`", $_REQUEST["id"])) : '';

        $sql = "DELETE FROM bs_reservations_items WHERE reservationID= '{$filesToDel}'";
        $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete bookings");

        $sql = "DELETE FROM bs_reservations WHERE id='$filesToDel'";
        $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete events 3");

        addMessage(MSG_ATDELETED, "warning");
    }

    $filter = !empty($event) ? " WHERE eventID='$event' " : " WHERE eventID IS NOT NULL ";
    //PAGES TABLE  GENERATION TO SHOW IN HTML BELOW
    $sql = "SELECT * FROM bs_reservations $filter ORDER BY dateCreated DESC";
    $result = $mysqli->query($sql) or die("error getting events from db");
    if ($result->num_rows > 0) {
        while ($rr = $result->fetch_assoc()) {

            $service = ($rr["serviceID"] == 0) ? "Default Service" : getService($rr["serviceID"], "name");

            $editable = "<a href=\"bs-bookings_event-edit.php?id=" . $rr["id"] . "\"  class=\"greedButton\">Edit</a>";
            //$editable.="&nbsp;&nbsp;<a href='bs-events.php?id=" . $rr["id"] . "&amp;del=yes'><img src='images/delete_16.png' border=\"0\"></a>";

            $bgClass = ($bgClass == "even" ? "odd" : "even");
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
            $files_table .= "<tr class=\"" . $bgClass . "\">";
            $files_table .= "";
            $files_table .= "<td height=\"24\"><input name=\"filesToDel[]\" type=\"checkbox\" value=\"" . $rr["id"] . "\" /></td>";
            $files_table .= "<td>" . $rr["name"] . "</td>";
            $files_table .= "<td>" . $rr["email"] . "</td>";
            $files_table .= "<td>" . $rr["qty"] . "</td>";
            $files_table .= "<td>" . $rr["date"] . "</td>";
            $files_table .= "<td>" . $status . $editable . "</td></tr>";
        } // end of all files from db query (end of while loop)

        //show button to complete file deletion if proper permissions.
        $showTheCheckbox = true;
    } else {
        $showTheCheckbox = false;
        //0 files found in database. ( end of IF mysql_num_rows > 0 )
        //$files_table .="<tr><td></td><td>".ZERO_ATTENDEES_DATABASE."</td><td></td><td></td><td></td><td></td></tr>";
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
                null

            ],
            'oLanguage': {
                "sEmptyTable": " ",
                /* appears in the top middle of the empty table */
                "sInfoEmpty": "<?php echo ZERO_ATTENDEES_DATABASE; ?>"
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
    <div class="content_block small">

        <h2><?php echo ATTENDEES ?>
            <a href="bs-events.php"><?php echo BACK_TO_LIST ?></a>
        </h2>
        <div class="bar">

            <div class="servicesList">
                <p><?php echo ATTEND_VIEW_EVENT ?></p>
                <form name="ff1" action="" id="ff1" method="post">
                    <div class="left mrgR">
                        <label><?php echo ATTEND_CHOISE_EVENT ?>:</label>
                        <select name="event" id="event" class="select">
                            <option value="">Select Event</option>
                            <?php
                                $sql = "SELECT * FROM bs_events ";
                                $res = $mysqli->query($sql);
                                while ($row = $res->fetch_assoc()) {
                                    ?>
                            <option value="<?php echo $row['id'] ?>" <?php echo ($event == $row['id']) ? "selected" : "" ?>><?php echo $row['title'] ?></option>
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

        </div>

        <form enctype="multipart/form-data" action="bs-events-attendees.php" method="post" name="ff2">
            <input type="hidden" value="yes" name="files_delete" />
            <input type="hidden" value="<?php echo $event ?>" name="event" />
            <table width="100%" border="0" cellspacing="0" cellpadding="0" id="grid" class="dataTable">
                <thead>
                    <tr class="topRow">
                        <th width="4%" height="30" align="center">&nbsp;</th>
                        <th width="12%" align="left"><strong><?php echo ATTEND_LST_NAME ?></strong></th>
                        <th width="16%" align="left"><strong><?php echo ATTEND_LST_EMAIL ?></strong></th>
                        <th width="16%" align="left"><strong><?php echo ATTEND_LST_SPACES ?></strong></th>

                        <th width="20%" align="left"><strong><?php echo ATTEND_LST_DATES ?></strong></th>
                        <th width="20%" align="left"><strong><?php echo BOOKING_LST_STATUS ?></strong></th>


                    </tr>
                </thead>
                <?php echo $files_table; ?>
                <tfoot>
                    <tr class="topRow">
                        <th width="4%" height="30" align="center">&nbsp;</th>
                        <th width="12%" align="left"><strong><?php echo ATTEND_LST_NAME ?></strong></th>
                        <th width="16%" align="left"><strong><?php echo ATTEND_LST_EMAIL ?></strong></th>
                        <th width="16%" align="left"><strong><?php echo ATTEND_LST_SPACES ?></strong></th>
                        <th width="20%" align="left"><strong><?php echo ATTEND_LST_DATES ?></strong></th>
                        <th width="20%" align="left"><strong><?php echo BOOKING_LST_STATUS ?></strong></th>


                    </tr>
                </tfoot>
            </table>
        </form>
    </div>
    <div class="sidebar">
        <a href="bs-bookings_event-edit.php?eventID=<?php echo isset($event) ? $event : "" ?>" class="adj2_btn_long_orange"><span><?php echo ATTEND_LST_BTN ?></span></a>
        <h3>Attendees</h3>
        <p><?php echo HELP_SIDE1 ?></p>
        <a href="help/index.html"><?php echo NEED_HELP ?></a>
    </div>
    <div style="clear: both"></div>
    <?php include "includes/admin_footer.php"; ?>
    <?php } ?>