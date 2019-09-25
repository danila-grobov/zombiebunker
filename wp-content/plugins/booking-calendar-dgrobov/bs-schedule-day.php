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
    $selectedDayFrom = (!empty($_REQUEST["selectedDayFrom"])) ? strip_tags(str_replace("'", "`", $_REQUEST["selectedDayFrom"])) : date("Y-m-d");
    $selectedDayTo = (!empty($_REQUEST["selectedDayTo"])) ? strip_tags(str_replace("'", "`", $_REQUEST["selectedDayTo"])) : date("Y-m-d",strtotime("+7 days"));
    $serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : 0;
    
    $servicesList = array();
    $sql = "SELECT * FROM bs_services WHERE type='d'";
    $res = $mysqli->query($sql);
    while ($row = $res->fetch_assoc()) {
        $servicesList[]=$row;
    }
    if(empty($serviceID)){
        $serviceID = !empty($servicesList[0]['id'])?$servicesList[0]['id']:"";
    }
    
    if (!empty($selectedDayFrom) && !empty($selectedDayTo) && !empty($serviceID)) {
        $availability = getScheduleTableDay($selectedDayFrom,$selectedDayTo, $serviceID);
    } elseif(empty($serviceID)){
        $availability = "No services found";
    }else{
        $availability = "Please select a day above";
    }
    ?>

    <?php include "includes/admin_header.php"; ?>
<link type="text/css" href="./js/datatable/css/jquery.dataTables.css" rel="stylesheet" />
    <script type="text/javascript">
        $(function() {

            $(".datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                showOn: "button",
                buttonImage: "images/new/calendar.png",
                buttonImageOnly: true,
                onSelect: updatePDFLink
            });
            //$('#reserveDateTo').datepicker('option', {dateFormat: "yy-mm-dd"})
        	
        $(".sh_event > .naw").bind("click",function(){
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
        $('select').selectbox();
        })
        function updatePDFLink(){
            var selDate = $("#reserveDateFrom").val();
            var selDateTo = $("#reserveDateTo").val();
            var serviceId= $("#serviceID").val();
            var url = "./bs-schedule-day_pdf.php";
                url +="?selectedDay="+selDate+"&selectedDayTo"+selDateTo+"&serviceID="+serviceId;
            $(".pdfExport").attr('href',url)
        }
        
    </script>
    
    <div id="content">


        


        <div class="content_block small">
            <h2 class="adj_schedule_title" ><?php echo SCHEDL_DAY ?><?php if(IS_WP_PLUGIN=='1'){?> <a href="bs-schedule.php"><?php echo SCHEDL ?></a> <?php }?></h2>
            
            <strong><?php echo $msg; ?></strong>
            <div class="bar bar100x100">

                <div class="servicesList adj_schedule_title">
                    <p>View schedule for the service on the selected date</p>
                        <form name="ff1" action="bs-schedule-day.php" id="ff1" method="post">
                            <div class="left mrgR">
                                <label>Select Service:</label>
                                <select name="serviceID" id="serviceID" onchange="updatePDFLink()">
                                    
                                    <?php
                                    $sql = "SELECT * FROM bs_services WHERE type='d'";
                                    $res = $mysqli->query($sql);
                                    while ($row = $res->fetch_assoc()) {
                                        ?>
                                        <option value="<?php echo $row['id'] ?>" <?php echo ($serviceID == $row['id']) ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                                    <?php } ?>
                                        
                                </select>
                            </div>
                            <div class="left mrgR" style="margin: 0px 20px;">
                                <label>Select Date From:</label>
                                <input type="text" name="selectedDayFrom" id="reserveDateFrom" class="dateInput datepicker left" value="<?php echo $selectedDayFrom  ?>" />
                            </div>
                            <div class="left mrgR">
                                <label>Select Date To:</label>
                                <input type="text" name="selectedDayTo" id="reserveDateTo" class="dateInput datepicker left" value="<?php echo $selectedDayTo ?>" />
                            </div>
                                
                            <div class="left">
                                <label>&nbsp;</label>
                                <button type="submit"><span>View</span></button>
                            </div>
                            
                            
                            <div class="clear"></div>
                        </form>
                    </div>

                <div style="clear:both"></div>

            </div>
            <h3 class="adj_schedule_title" ><?php echo SCHEDL ?> for <span>"<?php echo getService($serviceID, 'name') ?>"</span> from: <span><?php echo getDateFormat($selectedDayFrom) ?></span>  to: <span><?php echo getDateFormat($selectedDayTo) ?></span>
            <a href="./bs-schedule-day_pdf.php?selectedDay=<?php echo $selectedDayFrom ?>&selectedDayTo=<?php echo $selectedDayTo ?>&serviceID=<?php echo $serviceID ?>" 
                                   class="pdfExport" target="_blank" >
                                    Print to PDF
                                </a>
            </h3>
            <table width="784" border="0" cellspacing="0" cellpadding="0">

                
                    <tr>
                        <td height="25" align="right">&nbsp;</td>
                        <td height="25">&nbsp;</td>
                    </tr>

                    <tr>
                        <td height="25" colspan="2" align="left">
                            <div class="scheduleDays">
                 <?php echo $availability ?>
                            </div>
                        </td>
                    </tr>


                    <tr>
                        <td height="25" align="right">&nbsp;</td>
                        <td height="25">&nbsp;</td>
                    </tr>
           

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