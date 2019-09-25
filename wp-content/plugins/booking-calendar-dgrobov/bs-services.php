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

    ########################################################################################################################################################
    //"delete selected attendees" action processing.
    if (!empty($_REQUEST["delete_files"])) {
        $todelID = $_REQUEST["bsid"];
        if (!empty($todelID)) {
            if ($demo === false) {
                foreach ($todelID as $service => $toDelete) :
                    /*if($toDelete==1){
                    addMessage(MSG_SRVDEL_DEFAULT,"warning");
                    continue;
                }*/

                    if ($toDelete == getDefaultService()) {
                        $sql = "UPDATE bs_services SET `default`='y' WHERE id!='{$toDelete}' LIMIT 1";
                        $result = $mysqli->query($sql);
                    }

                    $sql = "SELECT * FROM bs_events WHERE serviceID='{$toDelete}'";
                    $res = $mysqli->query($sql);
                    while ($row = $res->fetch_assoc()) {
                        $sql = "DELETE FROM bs_reservations_items WHERE eventID='{$row['id']}'";
                        $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete events 1");
                        $sql = "DELETE FROM bs_reservations WHERE eventID='{$row['id']}'";
                        $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete events 2");
                    }
                    $sql = "DELETE FROM bs_events WHERE serviceID='{$toDelete}'";
                    $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete events 3");

                    $sql = "DELETE FROM bs_reservations WHERE serviceID='{$toDelete}'";
                    $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete reservations 2");
                    ################################################################################
                    $sql = "SELECT * FROM bs_reserved_time WHERE serviceID='{$toDelete}'";
                    $res = $mysqli->query($sql);
                    while ($row = $res->fetch_assoc()) {
                        $sql = "DELETE FROM bs_reserved_time_items WHERE reservedID='{$row['id']}'";
                        $result = $mysqli->query($sql) or die("oopsy, error when tryin to resrved times");
                    }
                    $sql = "DELETE FROM bs_reserved_time WHERE serviceID='{$toDelete}'";
                    $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete events 3");
                    ##################################################################################

                    $sql = "DELETE FROM bs_schedule WHERE idService='" . $toDelete . "'";
                    $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete bs_schedule");

                    $sql = "DELETE FROM  bs_schedule_days WHERE idService='" . $toDelete . "'";
                    $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete  bs_schedule_days");

                    $sql = "DELETE FROM bs_services WHERE id='" . $toDelete . "'";
                    $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete service 1");

                    $sql = "DELETE FROM bs_service_settings WHERE serviceId='" . $toDelete . "'";
                    $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete service settings 1");

                    $sql = "DELETE FROM bs_service_days_settings WHERE idService='" . $toDelete . "'";
                    $result = $mysqli->query($sql) or die("oopsy, error when tryin to delete service day settings 1");


                    $id = '';
                endforeach;

                addMessage(MSG_SRVDEL, "success");
            } else {

                addMessage(MSG_DEMO1, "warning");
            }
        }
    }

    $files_table = "";
    ###################################################################################################################################################
    //PAGES TABLE  GENERATION TO SHOW IN HTML BELOW
    $sql = "SELECT * FROM bs_services ORDER BY date_created DESC";
    $result = $mysqli->query($sql) or die("error getting attendees from db");
    $countRows = $result->num_rows;

    if ($countRows > 0) {
        while ($rr = $result->fetch_assoc()) {
            $serviceData = getServiceSettings($rr["id"]);
            $note = MSG_NOTE;

            $editable = ($rr["type"] == 't') ? "{$serviceData['interval']} min" : "{$serviceData['maxDays']} days max";
            $editable .= ($rr["type"] == 't') ? "<a href=\"bs-services-add.php?id=" . $rr["id"] . "\" class=\"greedButton\">" : "<a href=\"bs-services_days-add.php?id=" . $rr["id"] . "\"  class=\"greedButton\">";
            $editable .= "Edit</a>";
            //$editable.=($rr["id"] != 1) ?"&nbsp;&nbsp;<a href='javascript:void(0)' onclick='if(confirm(\"Delete selected service?" . $note . "\")){(document.location.href=\"bs-services.php?id=" . $id . "&amp;del=yes&amp;bsid=" . $rr["id"] . "\")}'><img src='images/delete_16.png' border=\"0\"></a>":"";

            $bgClass = ($bgClass == "even" ? "odd" : "even");



            $files_table .= "<tr class=\"" . $bgClass . "\">";
            $files_table .= "";

            $files_table .= "<td><input name=\"bsid[]\" type=\"checkbox\" value=\"" . $rr["id"] . "\" /></td>";
            //$files_table .= "<td><input name=\"bsid[]\" type=\"checkbox\" value=\"" . $rr["id"] . "\" ".($countRows==1?"disabled='disabled'":"")."/></td>";
            if (IS_WP_PLUGIN == '1') {
                $files_table .= "<td>" . $rr["id"] . "</td>";
            }
            $files_table .= "<td>" . $rr["name"] . "</td>";
            $files_table .= "<td>" . ($rr["default"] == 'y' ? YES : NO) . "</td>";
            $files_table .= "<td>" . ($rr["type"] == 't' ? "Hourly" : "Multi-Day") . "</td>";
            $files_table .= "<td>" . ($serviceData['spot_price'] == 0 ? FREE_BOOKING : getCurrencyText($serviceData['spot_price'])) . "</td>";
            $files_table .= "<td valign='center'>" . $editable . "</td></tr>";
        } // end of all files from db query (end of while loop)
        $showTheCheckbox = true;
    } else {
        $showTheCheckbox = false;
        //0 files found in database. ( end of IF mysql_num_rows > 0 )
        //$files_table .="<tr><td colspan=\"4\">".ZEO_FOUND_BS."</td></tr>";
    }

    ########################################################################################################################################################
    //prepare attendees page.
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
                <?php if (IS_WP_PLUGIN == '1') { ?>
                null,
                <?php } ?>
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
                "sInfoEmpty": "<?php echo ZEO_FOUND_BS; ?>"
            }
        });
        $("#grid_wrapper").append('<div class="deleteBtton"><input name="delete_files" onclick="return checkServices();" type="submit" value="<?php echo BTN_DELETESEL ?>"  /></div>');
        $('select[name="grid_length"]').msDropdown({
            mainCSS: 'rows'
        });
    });

    function checkServices() {
        var $data = $("#ff1").serialize();
        $data += "&type=service";
        $.ajax({
            url: "ajax/checkDeletedServices.php",
            success: function(e) {
                console.log(e.mess)
                if (e.res) {
                    $("#ff1").submit();
                } else {
                    if (confirm(e.mess)) {
                        $("#ff1").submit();
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
    <div class="content_block small">
        <h2><?php echo SERVICES ?></h2>

        <form enctype="multipart/form-data" action="bs-services.php" method="post" name="ff2" id="ff1" style="margin-left:5px;">
            <input type="hidden" value="yes" name="delete_files" />
            <input value="<?php echo $id; ?>" name="id" type="hidden" />
            <table class="style_checkboxes" width="737" border="0" cellspacing="0" cellpadding="0" id="grid" class="dataTable" style="margin-left:10px;">
                <thead>
                    <tr class="topRow">

                        <th width="5%" height="30" align="center">
                            <?php if ($showTheCheckbox == true) : ?>
                            <input value="" name="selectALLCheckBoxes" type="checkbox" class="ToggleSelectAllCheckBoxes" <?php echo $countRows == 1 ? "disabled='disabled'" : "" ?> />
                            <?php endif; ?></th>
                        <?php if (IS_WP_PLUGIN == '1') { ?>
                        <th width="5%" align="left"><?php echo SERVICE_ID ?></th>
                        <?php } ?>

                        <th width="25%" align="left"><strong><?php echo BOOKING_FRM_NAME ?></strong></th>
                        <th width="10%" align="left"><strong><?php echo DEFAULT_SERVICE ?></strong></th>
                        <th width="18%" align="left"><strong><?php echo BOOKING_FRM_TYPE ?></strong></th>
                        <th width="18%" align="left"><strong><?php echo PRICE_PER_BOOKING ?></strong></th>
                        <th width="25" height="30" align="left"><strong><?php echo SERVICE_DURATION ?></strong></th>
                    </tr>
                </thead>
                <?php echo $files_table; ?>
                <tfoot>
                    <tr class="topRow">

                        <th height="30" align="center">&nbsp;</th>
                        <?php if (IS_WP_PLUGIN == '1') { ?>
                        <th width="5%" align="left"><?php echo SERVICE_ID ?></th>
                        <?php } ?>

                        <th align="left"><strong><?php echo BOOKING_FRM_NAME ?></strong></th>
                        <th align="left"><strong><?php echo DEFAULT_SERVICE ?></strong></th>
                        <th align="left"><strong><?php echo BOOKING_FRM_TYPE ?></strong></th>
                        <th align="left"><strong><?php echo PRICE_PER_BOOKING ?></strong></th>
                        <th height="30" align="left"><strong><?php echo SERVICE_DURATION ?></strong></th>
                    </tr>
                </tfoot>
            </table>

        </form>

    </div>
    <div class="sidebar">
        <a href="bs-services-add.php" class="adj2_btn_long_orange"><span><?php echo BS_SERV_BTN1 ?></span></a>
        <a href="bs-services_days-add.php" class="adj2_btn_long_orange"><span><?php echo BS_SERV_BTN2 ?></span></a>
        <div class="adj2-sidebar-content">
            <b>Service = Calendar</b>
            <?php echo TXT_SM_R_ABOUT; ?>
            <a class="help" href="help/index.html"><?php echo NEED_HELP ?></a>

        </div>
    </div>
    <div style="clear: both"></div>



    <?php include "includes/admin_footer.php"; ?>
    <?php } ?>