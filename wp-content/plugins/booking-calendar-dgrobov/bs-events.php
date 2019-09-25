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
    $serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();


    //"delete selected files" action processing.
    if (!empty($_REQUEST["files_delete"]) && $_REQUEST["files_delete"] == "yes") {

        if (is_array($_POST['bsid'])) {
            if (join(",", $_POST['bsid']) != '') {
                //delete record from database
                $sql = "SELECT * FROM bs_events WHERE id IN ('" . join("','", $_POST['bsid']) . "')";
                $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete images 1");
                while ($row = $result->fetch_assoc()) {
                    @unlink($_SERVER['DOCUMENT_ROOT'] . $baseDir . $row['path']);
                }

                $sql = "DELETE FROM bs_reservations_items WHERE eventID IN ('" . join("','", $_POST['bsid']) . "')";
                $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete events 1");
                $sql = "DELETE FROM bs_reservations WHERE eventID IN ('" . join("','", $_POST['bsid']) . "')";
                $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete events 2");
                $sql = "DELETE FROM bs_events WHERE id IN ('" . join("','", $_POST['bsid']) . "')";
                $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete events 3");

                addMessage(MSG_EVDELETED, "warning");
            }
        }
    }



    //PAGES TABLE  GENERATION TO SHOW IN HTML BELOW
    $sql = "SELECT * FROM bs_events WHERE serviceID={$serviceID} ORDER BY eventDate DESC";
    $result = $mysqli->query($sql) or die("error getting events from db");
    if ($result->num_rows > 0) {
        while ($rr = $result->fetch_assoc()) {

            $service = ($rr["serviceID"] == 0) ? "Default Service" : getService($rr["serviceID"], "name");
            $paymentButton = $requrringButton =  '<span class="icon"></span>';
            $attendeeButton = '<a href="bs-events-attendees.php?event=' . $rr['id'] . '"><span class="icon attendee"><i>View Attendees<b></b></i></span></a>';

            $bgClass = ($bgClass == "even" ? "odd" : "even");
            if ($rr["recurring"]) {
                $spaces = getEventRecurringSpots($rr['id'], $rr["spaces"]);
                $spaces = "<a href='javascript:;' class='tipTip' title='{$spaces}'>Show</a>";
                $requrringButton = '<span class="icon requrring"><i>Recurring<b></b></i></span>';
            } else {
                $spaces = getSpotsLeftForEvent($rr["id"]) . "</b> " . SYL_LEFT . " <b>" . $rr["spaces"] . "</b> " . SYL_TOTAL;
            }

            if ($rr['entryFee'] > 0) {
                $paymentButton = '<span class="icon payment"><i>Payment Required<b></b></i></span>';
            }

            $editButton = "<a href=\"bs-events-add.php?id=" . $rr["id"] . "\" class=\"greedButton\">Edit</a>";
            $editable = $paymentButton . $requrringButton . $attendeeButton . $editButton;

            $files_table .= "<tr class=\"" . $bgClass . "\">";
            $files_table .= "";
            $files_table .= "<td height=\"24\"><input name=\"bsid[]\" type=\"checkbox\" value=\"" . $rr["id"] . "\" /></td>";
            $files_table .= "<td>" . $rr["id"] . "</td>";
            $files_table .= "<td>" . $rr["title"] . "</td>";
            $files_table .= "<td>" . $service . "</td>";

            $files_table .= "<td>" . getDateFormat($rr["eventDate"]) . " " . SYL_AT . " " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDate"])) . "</td>";
            $files_table .= "<td>" . getDateFormat($rr["eventDateEnd"]) . " " . SYL_AT . " " . date((getTimeMode()) ? "g:i a" : "H:i", strtotime($rr["eventDateEnd"])) . "</td>";
            $files_table .= "<td  class=\"noBorderRight\"><b>" . $spaces . "</td>";

            $files_table .= "<td>" . $editable . "</td></tr>";
        } // end of all files from db query (end of while loop)

        //show button to complete file deletion if proper permissions.

        //$files_table .="<tr><td height=\"32\" colspan=\"7\"><input name=\"delete_files\" type=\"submit\" value=\"".BTN_DELETESEL."\"  /></td></tr>";

        $showTheCheckbox = true;
    } else {
        $showTheCheckbox = false;
        //0 files found in database. ( end of IF mysql_num_rows > 0 )
        //$files_table .="<tr><td></td><td></td><td>".ZERO_EVENT_DATABASE."</td><td></td><td></td><td></td><td></td><td></td></tr>";
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
                {
                    "bSortable": false
                },
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

            ],
            'oLanguage': {
                "sEmptyTable": " ",
                /* appears in the top middle of the empty table */
                "sInfoEmpty": "<?php echo ZERO_EVENT_DATABASE; ?>"
            }
        });
        $("#grid_wrapper").append('<div class="deleteBtton"><input name="delete_files" onclick="return checkServices();" type="submit" value="<?php echo BTN_DELETESEL ?>"  /></div>');
        $('select[name="grid_length"]').msDropdown({
            mainCSS: 'rows'
        });
    });

    function checkServices() {
        var $data = $("#ff2").serialize();
        $data += "&type=event";
        $.ajax({
            url: "ajax/checkDeletedServices.php",
            success: function(e) {
                console.log(e.mess)
                if (e.res) {
                    $("#ff2").submit();
                } else {
                    if (confirm(e.mess)) {
                        $("#ff2").submit();
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
    <div class="content_block small adj_contentv1">

        <h2 class="noMargSide"><?php echo BOOKING_LST_EVENTS ?></h2>
        <div class="bar">
            <?php
                $sql = "SELECT * FROM bs_services";
                $res = $mysqli->query($sql);
                if ($res->num_rows > 1) {
                    ?>
            <div class="servicesList">

                <form name="ff1" action="" id="ff1" method="get">
                    <div class="left mrgR">
                        <label>Select Service:</label>
                        <select name="serviceID" id="serviceID" class="select">

                            <?php
                                    $sql = "SELECT * FROM bs_services ";
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
        <h3 class="h3_to_left"><?php echo BOOKING_LST_EVENTS ?> for <?php echo getService($serviceID, 'name') ?></h3>


        <form enctype="multipart/form-data" action="bs-events.php" method="post" name="ff2" id="ff2">
            <input type="hidden" value="yes" name="files_delete" />
            <input type="hidden" value="<?php echo $serviceID ?>" name="serviceID" />
            <table class="style_checkboxes" width="100%" border="0" cellspacing="0" cellpadding="0" id="grid" class="dataTable">
                <thead>
                    <tr class="topRow">
                        <th width="4%" height="30" align="center"><?php if ($showTheCheckbox == true) : ?><input id="movetothedarksidetheyhavecookies" value="" name="selectALLCheckBoxes" type="checkbox" class="ToggleSelectAllCheckBoxes" /><?php endif; ?></th>
                        <th width="4%" align="left"><strong><?php echo EVENT_ID ?></strong></th>
                        <th width="14%" align="left"><strong><?php echo EVENT_TTL ?></strong></th>
                        <th width="10%" align="left"><strong><?php echo BOOKING_FRM_SERVICE ?></strong></th>
                        <th width="19%" align="left"><strong><?php echo EVENT_ST_DATE ?></strong></th>
                        <th width="19%" align="left"><strong><?php echo END_DATE ?></strong></th>
                        <th width="10%" align="left" class="noBorderRight"><strong><?php echo BOOKING_LST_SPACES ?></strong></th>
                        <th align="left" width="30%">&nbsp;</th>
                    </tr>
                </thead>
                <?php echo $files_table; ?>
                <tfoot>
                    <tr class="topRow">
                        <th width="4%" height="30" align="center">&nbsp;</th>
                        <th width="4%" align="left"><strong><?php echo EVENT_ID ?></strong></th>
                        <th width="14%" align="left"><strong><?php echo EVENT_TTL ?></strong></th>
                        <th width="10%" align="left"><strong><?php echo BOOKING_FRM_SERVICE ?></strong></th>
                        <th width="19%" align="left"><strong><?php echo EVENT_ST_DATE ?></strong></th>
                        <th width="19%" align="left"><strong><?php echo END_DATE ?></strong></th>
                        <th width="10%" align="left" class="noBorderRight"><strong><?php echo BOOKING_LST_SPACES ?></strong></th>
                        <th width="30%" align="left">&nbsp;</th>

                    </tr>
                </tfoot>
            </table>
        </form>
    </div>
    <div class="sidebar">
        <a href="bs-events-add.php" class="adj2_btn_long_orange"><span><?php echo EVENT_ADD ?></span></a>
        <br /><br /><br />
        <div class="info" style="margin-left: 5px;">
            <ul>
                <li><span class="icon payment-grey"></span> <?php echo EVENT_PAY ?></li>
                <li><span class="icon requrring-grey"></span> <?php echo EVENT_REC ?></li>
                <li><span class="icon attendee-grey"></span> <?php echo EVENT_ATT ?></li>


            </ul>
            <h3><?php echo EVENT_EVEN ?></h3>
            <p><?php echo EVENT_EVEN2 ?></p>
            <a href="help/index.html"><?php echo NEED_HELP ?></a>
        </div>
        <div style="clear: both"></div>
    </div>
    <?php include "includes/admin_footer.php"; ?>
    <?php } ?>