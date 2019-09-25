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
    //get access level
    ######################### DO NOT MODIFY (UNLESS SURE) END ########################
    $filter = "";  //default filter variable. getting rid of undefined variable exception.
    $bgClass = "even"; // default first row highlighting CSS class
    $files_table = ""; //var with php generated html table.
    //get service id
    $serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();;


    //"delete selected files" action processing.
    if (!empty($_REQUEST["files_delete"]) && $_REQUEST["files_delete"] == "yes") {
        $filesToDel = (!empty($_REQUEST["filesToDel"])) ?  $_REQUEST["filesToDel"] : '';
        if (is_array($_POST['filesToDel'])) {

            if (join(",", $_POST['filesToDel']) != '') {
                $sql = "DELETE FROM bs_reserved_time_items WHERE reservedID IN ('" . join("','", $_POST['filesToDel']) . "')";
                $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete reserved time items");
                $sql = "DELETE FROM bs_reserved_time WHERE id IN ('" . join("','", $_POST['filesToDel']) . "')";
                $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete bookings");
                //$msg = "Selected manual bookings were deleted.";
                addMessage(MSG_MAN_DEL, "warning");
            }
        }
    }

    //"delete selected files" action processing.
    if (!empty($_REQUEST["del"]) && $_REQUEST["del"] == "yes") {
        $filesToDel = (!empty($_REQUEST["id"])) ? strip_tags(str_replace("'", "`", $_REQUEST["id"])) : '';

        $sql = "DELETE FROM bs_reserved_time_items WHERE reservedID ={$filesToDel}";
        $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete reserved time items");
        $sql = "DELETE FROM bs_reserved_time WHERE id={$filesToDel}";
        $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete bookings");
        //$msg = "Selected manual bookings were deleted.";
        addMessage(MSG_MAN_DEL, "warning");
    }


    //PAGES TABLE  GENERATION TO SHOW IN HTML BELOW
    $sql = "SELECT br.*,bs.name as sname,bs.type FROM bs_reserved_time br INNER JOIN bs_services bs ON bs.id=br.serviceID AND br.serviceID={$serviceID} ORDER BY dateCreated DESC";
    $result = $mysqli->query($sql) or die("error getting bookings from db");
    if ($result->num_rows > 0) {
        while ($rr = $result->fetch_assoc()) {

            //PERMISSION CHECK - for showing EDIT FILE icon.

            if ($rr['type'] == 'd') {
                $editable = "<a href=\"bs-reserve-day.php?id=" . $rr["id"] . "\" class=\"greedButton\">Edit</a>";
            } else {
                $editable = "<a href=\"bs-reserve.php?id=" . $rr["id"] . "\" class=\"greedButton\">Edit</a>";
            }

            //$editable.="&nbsp;&nbsp;<a href='bs-reserve-view.php?id=" . $rr["id"] . "&amp;del=yes&amp;serviceID=".$serviceID."'><img src='images/delete_16.png' border=\"0\"></a>";

            $bgClass = ($bgClass == "even" ? "odd" : "even");

            $files_table .= "<tr class=\"" . $bgClass . "\">";
            $files_table .= "";
            $files_table .= "<td height=\"24\"><input name=\"filesToDel[]\" type=\"checkbox\" value=\"" . $rr["id"] . "\" /></td>";
            $files_table .= "<td>" . $rr["reason"] . "</td>";
            $files_table .= "<td>" . ($rr["recurring"] ? "yes" : "no") . "</td>";
            $files_table .= "<td>" . $rr["sname"] . "</td>";
            $files_table .= "<td>" . getDateFormat($rr["reserveDateFrom"]) . date(((getTimeMode()) ? " g:i a" : " H:i"), strtotime($rr["reserveDateFrom"])) . "</td>";
            $files_table .= "<td class='noBorderRight'>" . getDateFormat($rr["reserveDateTo"]) . date(((getTimeMode()) ? " g:i a" : " H:i"), strtotime($rr["reserveDateTo"])) . "</td>";
            $files_table .= "<td>" . $editable . "</td></tr>";
        } // end of all files from db query (end of while loop)
        //show button to complete file deletion if proper permissions.

        //$files_table .="<tr><td height=\"32\" colspan=\"7\"><input name=\"delete_files\" type=\"submit\" value=\"".BTN_DELETESEL."\"  /></td></tr>";
        $showTheCheckbox = true;
    } else {
        $showTheCheckbox = false;
        //0 files found in database. ( end of IF mysql_num_rows > 0 )
        //$files_table .="<tr><td></td><td>".ZERO_MAN_FOUND."</td><td></td><td></td><td></td><td></td><td></td></tr>";
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
                {
                    "bSortable": false
                }

            ],
            'oLanguage': {
                "sEmptyTable": " ",
                /* appears in the top middle of the empty table */
                "sInfoEmpty": "<?php echo ZERO_MAN_FOUND; ?>"
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

    <div class="content_block adj_cbs_01">
        <h2><?php echo MAN_BOOK ?></h2>
        <div class="bar">
            <?php
                $sql = "SELECT * FROM bs_services";
                $res = $mysqli->query($sql);
                if ($res->num_rows > 1) {
                    ?>
            <div class="servicesList">
                <p><?php echo SCHEDL_VIEW ?></p>
                <form name="ff1" action="" id="ff1" method="post">
                    <div class="left mrgR">
                        <label><?php echo SCHEDL_SELECT_SERV ?></label>
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
        <h3><?php echo MAN_BOOK ?> for <? echo getService($serviceID, 'name') ?></h3>
        <form enctype="multipart/form-data" action="bs-reserve-view.php" method="post" name="ff2">
            <input type="hidden" value="yes" name="files_delete" />
            <input type="hidden" value="<?php echo $serviceID ?>" name="serviceID" />
            <table class="style_checkboxes" width="100%" border="0" cellspacing="0" cellpadding="0" id="grid" class="dataTable">
                <thead>
                    <tr class="topRow">
                        <th width="5%" height="30" align="center"><?php if ($showTheCheckbox == true) : ?><input value="" name="selectALLCheckBoxes" type="checkbox" class="ToggleSelectAllCheckBoxes" /><?php endif; ?></th>
                        <th width="20%" align="left"><strong><?php echo MANUAL_BK_DESC ?></strong></th>
                        <th width="10%" align="left"><strong><?php echo RECURRING ?></strong></th>
                        <th width="20%" align="left"><strong><?php echo BOOKING_FRM_SERVICE ?></strong></th>
                        <th width="20%" align="left"><strong><?php echo DATE_FORM_RES ?></strong></th>
                        <th width="15%" align="left" class="noBorderRight "><strong><?php echo DATE_RES_TO ?></strong></th>
                        <th width="5%" height="30" align="center">&nbsp;</th>
                    </tr>
                </thead>
                <?php echo $files_table; ?>
                <tfoot>
                    <tr class="topRow">
                        <th width="5%" height="30" align="center">&nbsp;</th>
                        <th width="20%" align="left"><strong><?php echo MANUAL_BK_DESC ?></strong></th>
                        <th width="10%" align="left"><strong><?php echo RECURRING ?></strong></th>
                        <th width="20%" align="left"><strong><?php echo BOOKING_FRM_SERVICE ?></strong></th>
                        <th width="20%" align="left"><strong><?php echo DATE_FORM_RES ?></strong></th>
                        <th width="15%" align="left" class="noBorderRight "><strong><?php echo DATE_RES_TO ?></strong></th>
                        <th width="5%" height="30" align="center">&nbsp;</th>
                    </tr>
                </tfoot>
            </table><br />

        </form>
    </div>

    <?php include "includes/admin_footer.php"; ?>
    <?php
    } ?>