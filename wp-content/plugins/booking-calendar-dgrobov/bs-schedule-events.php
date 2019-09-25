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

    bw_do_action("bw_load");
    bw_do_action("bw_admin");

    ######################### DO NOT MODIFY (UNLESS SURE) END ########################
    //show page only if admin access level
    //request all neccessary variables for user update action.
    $selectedDay = (!empty($_REQUEST["selectedDay"])) ? strip_tags(str_replace("'", "`", $_REQUEST["selectedDay"])) : date("Y-m-d");
    $serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();;


    if (!empty($selectedDay)) {
        $availability = getScheduleEventsTable($selectedDay, $serviceID);
    } else {
        $availability = "Please select a day above";
    }
    ?>

    <?php include "includes/admin_header.php"; ?>
    <link type="text/css" href="./js/datatable/css/jquery.dataTables.css" rel="stylesheet" />
    <script type="text/javascript">
        $(function() {

            $("#reserveDateFrom").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                showOn: "button",
                buttonImage: "images/new/calendar.png",
                buttonImageOnly: true,
                onSelect: updatePDFLink
            });

            //$('#reserveDateTo').datepicker('option', {dateFormat: "yy-mm-dd"})

            $(".mBooking > .naw").bind("click",function(){
                var parent = $(this).parent();
                if(parent.hasClass('hover')){
                    parent.removeClass("hover");
                    parent.find(".info").slideUp();
                    $(this).html("&blacktriangledown;");
                }else{
                    parent.addClass("hover");
                    parent.find(".info").slideDown();
                    $(this).html("&blacktriangle;");
                }
            });

            $(".bookingsList").click(function(){
                var el = $(this);
                var $rows = $("table.schedule").find("tr[data-row='"+el.data('event')+"']");
                if($rows.is(":visible")){
                    $rows.hide()
                }else{
                    $rows.show()
                }
            })
        });
        function updatePDFLink(){
            var selDate = $("#reserveDateFrom").val();
            var serviceId= $("#serviceID").val();
            var url = "./bs-schedule_pdf.php";
            url +="?selectedDay="+selDate+"&serviceID="+serviceId;
            $(".pdfExport").attr('href',url)
        }
        function collapseRows(el,_class){
            if($(el).hasClass('expanded')){
                $(el).text('Expand to view all');
            }else{
                $(el).text('Hide All');
            }
            $(el).toggleClass('expanded');
            $("."+_class).toggleClass('hide')
        }
    </script>

    <div id="content">





    <div class="content_block small">
        <h2 class="adj_schedule_title" ><?php echo SCHEDL ?><?php if(IS_WP_PLUGIN=='1'){?> <a href="bs-schedule-day.php"><?php echo SCHEDL_DAY ?></a> <?php }?></h2>

        <strong><?php echo $msg; ?></strong>
        <div class="bar bar100x100">

            <div class="servicesList adj_schedule_title">
                <p><?php echo SCHEDL_VIEW ?></p>
                <form name="ff1" action="bs-schedule-events.php" id="ff1" method="post">
                    <div class="left mrgR">
                        <label><?php echo SCHEDL_SELECT_SERV ?></label>
                        <select name="serviceID" id="serviceID" class="select" onchange="updatePDFLink()">

                            <?php
                            $sql = "SELECT bs.id,bs.name FROM bs_services bs
                                    INNER JOIN bs_events e ON e.serviceID=bs.id
                                    GROUP BY bs.id ORDER BY name ASC";
                            $res = $mysqli->query($sql);
                            while ($row = $res->fetch_assoc()) {
                                ?>
                                <option value="<?php echo $row['id'] ?>" <?php echo ($serviceID == $row['id']) ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                            <?php } ?>

                        </select>
                    </div>
                    <div class="left mrgR" style="margin: 0 5px 0 30px;" >
                        <label><?php echo SCHEDL_DATE ?></label>
                        <input type="text" name="selectedDay" id="reserveDateFrom" class="dateInput left" value="<?php echo $selectedDay ?>" />
                    </div>

                    <div class="left">
                        <label>&nbsp;</label>
                        <button type="submit"><span><?php echo SCHEDL_BTN_VIEW ?></span></button>
                    </div>
                    <div class="left">


                    </div>

                    <div class="clear"></div>
                </form>
            </div>

            <div style="clear:both"></div>

        </div>
        <h3 class="adj_schedule_title" ><?php echo SCHEDL?> <?php echo SCHEDL_FOR?> <span><?php echo getService($serviceID,'name')?></span> <?php echo SCHEDL_FOR?> <span><?php echo getDateFormat($selectedDay)?></span>
            <a href="./bs-schedule-event_pdf.php?selectedDay=<?php echo $selectedDay ?>&serviceID=<?php echo $serviceID ?>"
               class="pdfExport" target="_blank" >
                Print to PDF
            </a>
        </h3>
        <table style="margin: 0px auto;" width="740" border="0" cellspacing="0" cellpadding="0">

            <?php if (!empty($selectedDay)) { ?>
                <tr>
                    <td height="25" align="right">&nbsp;</td>
                    <td height="25">&nbsp;</td>
                </tr>

                <tr>
                    <td height="25" colspan="2" align="left">

                        <?php echo $availability ?>

                    </td>
                </tr>


                <tr>
                    <td height="25" align="right">&nbsp;</td>
                    <td height="25">&nbsp;</td>
                </tr>
            <?php } ?>

        </table>

    </div>
    <div class="sidebar">
        <a href="bs-services-add.php" class="adj2_btn_long_orange"><span><?php echo SCHEDL_BTN1?></span></a>
        <a href="bs-services_days-add.php" class="adj2_btn_long_orange"><span><?php echo SCHEDL_BTN2?></span></a>
        <a href="bs-events-add.php" class="adj2_btn_long_orange"><span><?php echo SCHEDL_BTN3?></span></a>
    </div>
    <div style="clear: both"></div>

    <?php include "includes/admin_footer.php"; ?>
<?php } ?>