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
    #if(!empty($_POST)){die(print_r($_POST));}
    ########################################################################################################################################################
    //"delete selected attendees" action processing.
    if (!empty($_REQUEST["del"]) && $_REQUEST["del"] == "Delete") {
        $todelID = $_REQUEST["bsid"];
        if (!empty($todelID)) {
            if ($demo === false) {
                $errorDel = false;
                foreach ($todelID as $id => $toDelete) :
                    $sql = "DELETE FROM bs_coupons WHERE id='{$toDelete}'";
                    #die(print_r($todelID));
                    $res = $mysqli->query($sql) or $errorDel = true;
                endforeach;
                if ($errorDel == false) :
                    addMessage(TXT_COUPONS_DEL, "success");
                    $id = '';
                else :
                    addMessage(TXT_COUPONS_DEL_ERROR, "warning");
                endif;
            } else {

                addMessage(MSG_DEMO1, "warning");
            }
        }
    }

    $files_table = "";
    ###################################################################################################################################################
    //PAGES TABLE  GENERATION TO SHOW IN HTML BELOW
    $sql = "SELECT * FROM bs_coupons ORDER BY dateFrom DESC";
    $result = $mysqli->query($sql) or die("error getting attendees from db");
    if ($result->num_rows > 0) {
        while ($rr = $result->fetch_assoc()) {

            $editable = "<a href=\"bs-coupon-add.php?id=" . $rr['id'] . "\"  class=\"greedButton\">Edit</a>";;
            //$editable.="&nbsp;&nbsp;<a href='javascript:void(0)' onclick='if(confirm(\"Delete selected service?" . $note . "\")){(document.location.href=\"bs-coupons.php?id=" . $id . "&amp;del=yes&amp;bsid=" . $rr["id"] . "\")}'><img src='images/delete_16.png' border=\"0\"></a>";
            $bgClass = ($bgClass == "even" ? "odd" : "even");
            $servicesList = "";
            $services = explode(",", $rr['services']);
            foreach ($services as $s) {
                $servicesList .= "<div>" . getService($s, 'name') . "</div>";
            }

            $files_table .= "<tr class=\"" . $bgClass . "\">";
            $files_table .= "";

            $currency = getOption("currency");
            $files_table .= "<td align='center'><input type='checkbox' name='bsid[]' value='" . $rr["id"] . "' /></td>";
            $files_table .= "<td>" . $rr["title"] . "</td>";
            $files_table .= "<td>" . $servicesList . "</td>";
            $files_table .= "<td>" . $rr["dateFrom"] . "</td>";
            $files_table .= "<td>" . $rr["dateTo"] . "</td>";
            $files_table .= "<td>" . $rr["value"] . " " . ($rr['type'] == 'abs' ? $currency : "%") . "</td>";
            $files_table .= "<td class='noBorderRight '>" . $rr["code"] . "</td>";
            $files_table .= "<td valign='center'>" . $editable . "</td></tr>";
        } // end of all files from db query (end of while loop)
        $showTheCheckbox = true;
    } else {
        $showTheCheckbox = false;
        //0 files found in database. ( end of IF mysql_num_rows > 0 )
        //$files_table .="<tr><td></td><td>".TXT_COUPONS_NOT_FOUND."</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
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
                "sInfoEmpty": "<?php echo TXT_COUPONS_NOT_FOUND; ?>"
            }
        });
        $("#grid_wrapper").append('<div class="deleteBtton"><input name="del" type="submit" value="Delete"  /></div>');
        $('select[name="grid_length"]').css("top", "3px");
        $('select[name="grid_length"]').msDropdown({
            mainCSS: 'rows'
        });


    });
</script>
<div id="content">




    <?php getMessages(); ?>
    <div class="content_block small">
        <h2><?php echo TXT_COUPONS ?></h2>
        <form enctype="multipart/form-data" action="bs-coupons.php" method="post" name="ff2" style="margin-left:5px;">
            <input type="hidden" value="yes" name="attendees_edit" />
            <input value="<?php echo $id; ?>" name="id" type="hidden" />
            <table class="style_checkboxes" width="736" border="0" cellspacing="0" cellpadding="0" id="grid" class="dataTable" style="margin-left:10px;">
                <thead>
                    <tr class="topRow">

                        <th width="2%" height="30" align="center"><?php if ($showTheCheckbox == true) : ?><input id="alignchekboxCoupons" value="" name="selectALLCheckBoxes" type="checkbox" class="ToggleSelectAllCheckBoxes" /><?php endif; ?></th>
                        <th width="22%" align="left"><strong><?php echo LABEL_COUPON_TITLE ?></strong></th>
                        <th width="9%" align="left"><strong><?php echo LABEL_COUPON_CALENDARS ?></strong></th>
                        <th width="9%" align="left"><strong><?php echo LABEL_COUPON_VALID_FROM ?></strong></th>
                        <th width="9%" align="left"><strong><?php echo LABEL_COUPON_VALID_TO ?></strong></th>
                        <th width="9%" align="left"><strong><?php echo LABEL_COUPON_AMOUNT ?></strong></th>
                        <th width="17%" align="left"><strong><?php echo LABEL_COUPON_CODE ?></strong></th>
                        <th width="6%" height="30" align="center">&nbsp;</th>
                    </tr>
                </thead>
                <?php echo $files_table; ?>

                <tfoot>
                    <tr class="topRow">

                        <th width="3%" height="30" align="center">&nbsp;</th>
                        <th width="22%" align="left"><strong><?php echo LABEL_COUPON_TITLE ?></strong></th>
                        <th width="9%" align="left"><strong><?php echo LABEL_COUPON_CALENDARS ?></strong></th>
                        <th width="9%" align="left"><strong><?php echo LABEL_COUPON_VALID_FROM ?></strong></th>
                        <th width="9%" align="left"><strong><?php echo LABEL_COUPON_VALID_TO ?></strong></th>
                        <th width="9%" align="left"><strong><?php echo LABEL_COUPON_AMOUNT ?></strong></th>
                        <th width="17%" align="left"><strong><?php echo LABEL_COUPON_CODE ?></strong></th>
                        <th width="6%" height="30" align="center">&nbsp;</th>
                    </tr>
                </tfoot>

            </table>

        </form>

    </div>
    <div class="sidebar">
        <a href="bs-coupon-add.php" class="adj2_btn_long_orange"><span><?php echo TXT_COUPONS_NEW ?></span></a>
    </div>




    <?php include "includes/admin_footer.php"; ?>
    <?php } ?>