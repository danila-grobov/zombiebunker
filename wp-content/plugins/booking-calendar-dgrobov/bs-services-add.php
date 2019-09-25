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

     $durationList=array(15=>MIN15,30=>MIN30,45=>MIN45,60=>H1,120=>H2,180=>H3,240=>H4,300=>H5,360=>H6,420=>H7,480=>H8,540=>H9,600=>H10,660=>H11,720=>H12);
    ######################### DO NOT MODIFY (UNLESS SURE) END ########################
    //print var_dump($_REQUEST);
    //show page only if admin access level
    //request all neccessary variables for user update action.
    $id = (!empty($_REQUEST["id"])) ? strip_tags(str_replace("'", "`", $_REQUEST["id"])) : '';
    $name = (!empty($_REQUEST["name"])) ? strip_tags(str_replace("'", "`", $_REQUEST["name"])) : '';
    //setting variables
    $spot_price = (!empty($_REQUEST["spot_price"])) ? strip_tags(str_replace("'", "`", $_REQUEST["spot_price"])) : '0';
    $spot_invoice = (!empty($_REQUEST["spot_invoice"])) ? strip_tags(str_replace("'", "`", $_REQUEST["spot_invoice"])) : '0';
    $payment_method = (!empty($_REQUEST["payment_method"])) ? strip_tags(str_replace("'", "`", $_REQUEST["payment_method"])) : '';
    $interval = (!empty($_REQUEST["interval"])) ? strip_tags(str_replace("'", "`", $_REQUEST["interval"])) : '15';
    $allow_times = (!empty($_REQUEST["allow_times"])) ? strip_tags(str_replace("'", "`", $_REQUEST["allow_times"])) : '';
    $allow_times_min = (!empty($_REQUEST["allow_times_min"])) ? strip_tags(str_replace("'", "`", $_REQUEST["allow_times_min"])) : '';
    $startDay = (!empty($_REQUEST["startDay"])) ? strip_tags(str_replace("'", "`", $_REQUEST["startDay"])) : '0';
    $time_before = (!empty($_REQUEST["time_before"])) ? intval($_REQUEST["time_before"]) : '0';

    $spaces_available = (!empty($_REQUEST["spaces_available"])) ? strip_tags(str_replace("'", "`", $_REQUEST["spaces_available"])) : 1;
    $show_spaces_left = (!empty($_REQUEST["show_spaces_left"])) ? strip_tags(str_replace("'", "`", $_REQUEST["show_spaces_left"])) : 0;
    $show_event_titles = (!empty($_REQUEST["show_event_titles"])) ? strip_tags(str_replace("'", "`", $_REQUEST["show_event_titles"])) : 0;
    $show_event_image = (!empty($_REQUEST["show_event_image"])) ? strip_tags(str_replace("'", "`", $_REQUEST["show_event_image"])) : 0;
    $show_multiple_spaces = (!empty($_REQUEST["show_multiple_spaces"])) ? strip_tags(str_replace("'", "`", $_REQUEST["show_multiple_spaces"])) : 0;
    $show_available_seats = (!empty($_REQUEST["show_available_seats"])) ? strip_tags(str_replace("'", "`", $_REQUEST["show_available_seats"])) : 0;
    
    $coupon = (isset($_REQUEST["coupon"])) ? strip_tags(str_replace("'", "`", $_REQUEST["coupon"])) : '0';
    $deposit = (isset($_REQUEST["deposit"])) ? strip_tags(str_replace("'", "`", $_REQUEST["deposit"])) : '100';
    $delBookings = (isset($_REQUEST["delBookings"])) ? strip_tags(str_replace("'", "`", $_REQUEST["delBookings"])) : 'n';

    
    
    $autoconfirm= (isset($_REQUEST["autoconfirm"])) ? strip_tags(str_replace("'", "`", $_REQUEST["autoconfirm"])) : '0';
    $fromName = (!empty($_REQUEST["fromName"])) ? strip_tags(str_replace("'", "`", $_REQUEST["fromName"])) : '';
    $fromEmail = (!empty($_REQUEST["fromEmail"])) ? strip_tags(str_replace("'", "`", $_REQUEST["fromEmail"])) : '';

    $bookingsToDelete = (!empty($_REQUEST["bookingsToDelete"])) ? strip_tags(str_replace("'", "`", $_REQUEST["bookingsToDelete"])) : '';

    $serviceInfo = getServiceSettings($serviceID);
    ########################################################################################################################################################
    //edit attendees processing.
    $sent = false;

    $action = (!empty($_REQUEST['action']))?$_REQUEST['action']:"";

    if($action=='setDefault'){

        $sql = "UPDATE bs_services SET `default`='n'";
        $result = $mysqli->query($sql) or die("err: " . $mysqli->error() . $sql);

        $sql = "UPDATE bs_services SET `default`='y' WHERE id='{$id}'";
        $result = $mysqli->query($sql) or die("err: " . $mysqli->error() . $sql);

    }


    if (!empty($_REQUEST["edit_page"]) && $_REQUEST["edit_page"] == "yes" && !empty($id)) {

        #cabcel bookings after change availability
        $bookingsToDel = json_decode($bookingsToDelete);
        if(count($bookingsToDel)){
            foreach($bookingsToDel as $del){


                $sql = "UPDATE bs_reservations SET status=4 WHERE id = '{$del}'";
                $res = $mysqli->query($sql);
            }
        }

        if (!empty($name) && !empty($allow_times) && !empty($allow_times_min) && !empty($interval)) {

            if ($deposit >=0 && $deposit <= 100) {



                if ($demo === false) {

                    $sql = "UPDATE bs_services SET
                    type='t',
                    name='" . $name . "' ,
                    autoconfirm='" . $autoconfirm . "' ,
                    fromName='" . $fromName . "' ,
                    fromEmail='" . $fromEmail . "',
                    `show_event_titles`='" . $show_event_titles . "',
                    `show_event_image`='" . $show_event_image . "',
                    `show_available_seats` = '" . $show_available_seats . "',
                    `deposit` = '" . ($deposit / 100) . "',
                    `delBookings` = '" . $delBookings. "'
                    WHERE id='" . $id . "'";

                    $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to update service.");

                    $sql = "UPDATE bs_service_settings SET
					spot_price=" . $spot_price . ",
					spot_invoice=" . $spot_invoice . ",
					allow_times='" . $allow_times . "',
					startDay='" . $startDay . "',
					allow_times_min='" . $allow_times_min . "',
                                        `payment_method`='" . $payment_method . "',
					`interval`='" . $interval . "',
					`spaces_available`='" . $spaces_available . "',
					`show_spaces_left`='" . $show_spaces_left . "',
					`show_multiple_spaces` ='" . $show_multiple_spaces . "',
                                        `coupon` ='" . $coupon . "',
                                        `time_before`='" . $time_before . "'
					WHERE serviceId='{$id}'";
                    $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to save settings." . $mysqli->error());


                    $sql = "DELETE FROM bs_schedule WHERE idService='{$id}'";
                    $res = $mysqli->query($sql);

                    for ($i = 0; $i < 7; $i++) {
                        $week_from_h = (!empty($_REQUEST["week_from_h_" . $i]) && is_array($_REQUEST["week_from_h_" . $i])) ? ($_REQUEST["week_from_h_" . $i]) : '';
                        $week_from_m = (!empty($_REQUEST["week_from_m_" . $i]) && is_array($_REQUEST["week_from_m_" . $i])) ? $_REQUEST["week_from_m_" . $i] : '';
                        $week_to_h = (!empty($_REQUEST["week_to_h_" . $i]) && is_array($_REQUEST["week_to_h_" . $i])) ? ($_REQUEST["week_to_h_" . $i]) : '';
                        $week_to_m = (!empty($_REQUEST["week_to_m_" . $i]) && is_array($_REQUEST["week_to_m_" . $i])) ? $_REQUEST["week_to_m_" . $i] : '';
                        for ($j = 0; $j < count($week_from_h); $j++) {
                            if (is_numeric($week_from_h[$j]) && is_numeric($week_from_m[$j]) && is_numeric($week_to_h[$j]) && is_numeric($week_to_m[$j])) {

                                if ($week_to_h[$j] == 0) {
                                    $week_to_h[$j] = 24;
                                }

                                $startTime = $week_from_h[$j] * 60 + $week_from_m[$j];
                                $endTime = $week_to_h[$j] * 60 + $week_to_m[$j];

                                $sql = "INSERT INTO bs_schedule (`idService`,`week_num`,`startTime`,`endTime`)
										VALUES ('{$id}','{$i}','{$startTime}','{$endTime}')";
                                $res = $mysqli->query($sql);

                            }
                        }
                    }


                    addMessage(MSG_SRVUPD, "success");
                } else {

                    addMessage(MSG_DEMO1, "warning");
                }

            } else {
                addMessage(INCORRECT_DEPOSIT);

            }
        } else {

            addMessage(ALLFIELDSREQ);
        }
    }
    ########################################################################################################################################################
    ########################################################################################################################################################
    //"edit page" action processing.
    if (!empty($_REQUEST["edit_page"]) && $_REQUEST["edit_page"] == "yes" && empty($id)) {

        if (!empty($name) && !empty($allow_times) && !empty($allow_times_min) && !empty($interval)) {

            if ($deposit >0 && $deposit <= 100) {

                if ($demo === false) {


                    if (empty($id)) {

                        $sql = "SELECT * FROM bs_services";
                        $res = $mysqli->query($sql);
                        if ($res->num_rows > 0) {
                            $default = 'n';
                        } else {
                            $default = 'y';
                        }
                        $deposit = $deposit/100;
                        $sql = "INSERT INTO bs_services (type,name,date_created,	autoconfirm,fromName,fromEmail,show_event_titles,show_event_image,show_available_seats,`default`,`deposit`)
                            VALUES ('t','" . $name . "','".DATE."','{$autoconfirm}','{$fromName}','{$fromEmail}',{$show_event_titles},{$show_event_image},{$show_available_seats},'{$default}','{$deposit}')";
                        $result = $mysqli->query($sql) or die("oopsy, error occured when tryin to create new service.");
                        $id = $mysqli->insert_id;

                        $query = "INSERT INTO `bs_service_settings` (`serviceId`,`payment_method`, `allow_times`, `allow_times_min`, `interval`,`startDay`,`spot_price`,`spot_invoice`, `spaces_available`,`show_spaces_left`,`show_multiple_spaces`) VALUES
					('{$id}','{$payment_method}', {$allow_times},{$allow_times_min}, {$interval}, '{$startDay}','{$spot_price}','{$spot_invoice}', '{$spaces_available}','{$show_spaces_left}','{$show_multiple_spaces}')";
                        $result = $mysqli->query($query) or die("oopsy, error occured when tryin to create new service settings.<br>");


                        for ($i = 0; $i < 7; $i++) {
                            $week_from_h = (!empty($_REQUEST["week_from_h_" . $i]) && is_array($_REQUEST["week_from_h_" . $i])) ? ($_REQUEST["week_from_h_" . $i]) : '';
                            $week_from_m = (!empty($_REQUEST["week_from_m_" . $i]) && is_array($_REQUEST["week_from_m_" . $i])) ? $_REQUEST["week_from_m_" . $i] : '';
                            $week_to_h = (!empty($_REQUEST["week_to_h_" . $i]) && is_array($_REQUEST["week_to_h_" . $i])) ? ($_REQUEST["week_to_h_" . $i]) : '';
                            $week_to_m = (!empty($_REQUEST["week_to_m_" . $i]) && is_array($_REQUEST["week_to_m_" . $i])) ? $_REQUEST["week_to_m_" . $i] : '';
                            for ($j = 0; $j < count($week_from_h); $j++) {
                                if (is_numeric($week_from_h[$j]) && is_numeric($week_from_m[$j]) && is_numeric($week_to_h[$j]) && is_numeric($week_to_m[$j])) {

                                    if ($week_from_h[$j] == 0) {
                                        $week_from_h[$j] = 24;
                                    } # track to output
                                    if ($week_to_h[$j] == 0) {
                                        $week_to_h[$j] = 24;
                                    }

                                    $startTime = $week_from_h[$j] * 60 + $week_from_m[$j];
                                    $endTime = $week_to_h[$j] * 60 + $week_to_m[$j];

                                    $sql = "INSERT INTO bs_schedule (`idService`,`week_num`,`startTime`,`endTime`)
										VALUES ('{$id}','{$i}','{$startTime}','{$endTime}')";
                                    $res = $mysqli->query($sql);

                                }
                            }
                        }
                        $name = $interval = $allow_times = $allow_times_min = '';
                        $x0_from = $x1_from = $x2_from = $x3_from = $x4_from = $x5_from = $x6_from = $x0_to = $x1_to = $x2_to = $x3_to = $x4_to = $x5_to = $x6_to = '';
                        $spot_price = $spot_invoice = 0;

                        addMessage(MSG_SRVSAVE, "success");

                    }
                } else {

                    addMessage(MSG_DEMO1, "warning");
                }
            } else {
                addMessage(INCORRECT_DEPOSIT);

            }
        } else {


        addMessage(ALLFIELDSREQ);
    }
}
    ########################################################################################################################################################
   
   
    ###################################################################################################################################################

    if (!empty($id)) {
        //select service settings and show it for editor.
        $sSQL = "SELECT payment_method,allow_times,allow_times_min,`interval`,
            spot_price, spot_invoice, startDay, spaces_available,show_spaces_left,
            show_multiple_spaces, use_popup,coupon ,time_before FROM bs_service_settings WHERE serviceId='" . $id . "'";
        $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
        if ($row = $result->fetch_assoc()) {
            foreach ($row as $key => $value) {
                $$key = $value;
            }
        }
        $result->free_result();
        //select service info and show it for editor.
        $sSQL = "SELECT * FROM bs_services WHERE id='" . $id . "'";
        $result = $mysqli->query($sSQL) or die("err: " . $mysqli->error() . $sSQL);
        if ($row = $result->fetch_assoc()) {
            foreach ($row as $key => $value) {
                $$key = $value;
            }
            $id = $row['id'];
        }
        $result->free_result();

        

        $week = array();
        $seArr = array();
        $sql = "select * from bs_schedule 
			Where idService={$id} ORDER BY startTime ASC"; //print $sql;
        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc()) {
            $m_from = $row['startTime'] % 60;
            $h_from = ($row['startTime'] - $m_from);
            $m_to = $row['endTime'] % 60;
            $h_to = $row['endTime'] - $m_to;
            $week[$row['week_num']][] = array("startHH" => $h_from, "startMM" => $m_from, "endHH" => $h_to, "endMM" => $m_to);
        	$day_of_the_week = $row['week_num'];
			
			$now = strtotime("00:00");
			$se_sm = date('i',strtotime("+ ".$row['startTime']." minutes",$now));
			$se_sh = date('H',strtotime("+ ".$row['startTime']." minutes",$now));
			$se_em = date('i',strtotime("+ ".$row['endTime']." minutes",$now));
			$se_eh = date('H',strtotime("+ ".$row['endTime']." minutes",$now));
			
			$seArr[$day_of_the_week][] = array($se_sh,$se_sm,$se_eh,$se_em);
		}
		#die(print_r($seArr));
		#echo $seArr[0][0][1];

		$deposit = $deposit*100;
    }


    ?>

    <?php include "includes/admin_header.php"; ?>
 <?php if (!$show_multiple_spaces) { ?>
        <style>
            .lock.recurring{
                display:block ;
            }
    
    </style>
    <?php } ?>
    <script type="text/javascript">
        $(function() {
            $("#eventDate").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd"
            });
            //$('#reserveDateFrom').datepicker('option', {dateFormat: "yy-mm-dd"});

    	
            $('#show_multiple_spaces').bind('change',function(){
    	
                if($(this).is(':checked')){
                    $('.recurring').hide();
                }else{
                    $('.recurring').show();
                }
            })

            $(".t_availabolity").find('input').change(function(){

                $("#ff2").val(true);
                console.log("change input")
            })

        });
        function noAlpha(obj){
            reg = /[^0-9.,]/g;
            obj.value =  obj.value.replace(reg,"");
        }	
        function addTime(week_number,el){
            $.get("includes/getTime.php",{week:week_number},function(data){
                $(el).before(data);
                findAndBindSE();
            },"html");
            
        }
        function updateDuration(el){
            
            var val = $(el).find("option:selected").attr('rel');
            console.log(val);
            
            $("#allow_times_min").find('option').each(function(){
                 var updEl = $(this);
                var text = updEl.text();
                var rp ="x "+val; 
                text = text.replace(/x\s[0-9]+\s[a-z]+$/,rp);
                updEl.html(text)
            })
            var dropEl =  $("#allow_times_min").parent().next();
            dropEl.find('li').each(function(){
                var updEl = $(this).find('span');
                var text = updEl.text();
                var rp ="x "+val; 
                text = text.replace(/x\s[0-9]+\s[a-z]+$/,rp);
                updEl.html(text)
            })
            dropEl.find('span.ddlabel').each(function(){
               
                var text = $(this).text();
                var rp ="x "+val; 
                text = text.replace(/x\s[0-9]+\s[a-z]+$/,rp);
                $(this).html(text)
            })
            $("#allow_times").find('option').each(function(){
                 var updEl = $(this);
                var text = updEl.text();
                var rp ="x "+val; 
                text = text.replace(/x\s[0-9]+\s[a-z]+$/,rp);
                updEl.html(text)
            })
             var dropEl =  $("#allow_times").parent().next();
            dropEl.find('li').each(function(){
                var updEl = $(this).find('span');
                var text = updEl.text();
                var rp ="x "+val; 
                text = text.replace(/x\s[0-9]+\s[a-z]+$/,rp);
                updEl.html(text)
            })
            dropEl.find('span.ddlabel').each(function(){
               
                var text = $(this).text();
                var rp ="x "+val; 
                text = text.replace(/x\s[0-9]+\s[a-z]+$/,rp);
                $(this).html(text)
            })
            
            
        }
        function checkBookings(){
            var $form = $("#ff2");
            //console.log($form);
            //console.log($form.val());
            if($form.val()=='true'){
                var $data = $("#ff1").serialize();
                $data+="&type=service";
                $.ajax({
                    url:"ajax/checkChangeAvailability.php",
                    success:function(e){
                        //console.log(e.mess)
                        if(e.res){
                            $("#ff1").submit();
                        }else{
                            if(confirm(e.mess)){
                                $("#bookingsToDelete").val(e.bookings);
                                $("#ff1").submit();
                            }
                        }
                    },
                    data:$data,
                    async:false,
                    dataType:"json"
                })

                return false;
            }else{
                $("#ff1").submit();
            }
        }
    </script>
    <div id="content">

     
<?php  getMessages(); ?>
        <div class="content_block">

            <h2><?php if (!empty($id)) { echo  EDIT_SERV;}else{echo ADD_SERV;}?>

            <?php if($default!='y'){
            if(!empty($id)){?>
            <form class="formServiceDefault" action="" method="post">
                <input type="hidden" name="id" value="<?php echo $id?>"/>
                <input type="hidden" name="action" value="setDefault"/>
                <button type="submit"><span><?php echo MAKE_DAFAULT?></span></button>
            </form>
            <?php }}else{?>
                <span>( <?php echo THIS_DEFAULT_SERVICE?>)</span>
            <?php }?>
             <a href="bs-services.php"><?php echo MSG_BACK ?></a></h2>

             <p><?php echo BS_SERV_TXT?></p>
<hr/>
            
           
            <strong><?php echo $msg; ?></strong>
            
            <form action="bs-services-add.php" data-change="false" enctype="multipart/form-data" method="post" id="ff1" name="ff1" >
<input type="hidden" name="avalability" value="false" id="ff2">




                
                <div class="form-row">
                        <div class="cell third">
                            <label><?php echo SERV_TTL ?></label>
                            <input type="text" name="name" id="title" value="<?php echo $name ?>" />
                        </div>
                        <div class="cell medium">
                            <label><?php echo PRICE_SPOT ?>&nbsp;(<?php echo getOption('currency') ?>)</label>
                            <input type="text" name="spot_price" class="small1 left" id="spot_price" style=" width:60px;" onkeyup="noAlpha(this)" value="<?php echo $spot_price ?>" />
                            <img src='images/info.png' border="0"  class="left tipTip imgCenter" title="<?php echo TIME_MSG ?>"/>
                        </div>
                    <div class="cell short1">
                        <label><?php echo REQUIRED_DEPOSIT?>&nbsp;%</label>

                        <input type="text" name="deposit" id="deposit" value="<?php echo $deposit?>" class="small1 left"
                               onkeyup="noAlpha(this)"/>
                        <img src='images/info.png' border="0" class="left tipTip imgCenter"  title="<?php echo NUMB_PLZ ?>"/>


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
                            <img src='images/info.png' border="0"  class="tipTip adj_add_events_tip" title="<?php echo PAYMENT_MSG ?>"/>
                        </div>
                            
                        <div class="clear"></div>
                    </div>
                <div class="form-row">
                    <div class="cell third">
                        <label><?php echo ALLOW_DEL_BOOKINGS?>&nbsp;<img src='images/info.png' border="0"  class="tipTip" title="<?php echo htmlspecialchars(ALLOW_DEL_BOOKINGS_NOTES)?>"/></label>
                        <input type="radio" name="delBookings" id="delBookings" value="y" <?php echo $delBookings == "y" ? "checked" : "" ?>/> <?php echo YES;?> &nbsp;
                        <input type="radio" name="delBookings" id="delBookings" value="n" <?php echo $delBookings == "n" ? "checked" : "" ?> /> <?php echo NO;?> &nbsp;

                    </div>
                    <div class="cell third">
                        <label><?php echo AUTOCONFIRM?>&nbsp;<img src='images/info.png' border="0"  class="tipTip" title="<?php echo AUTOCONFIRM_MSG?>"/></label>
                        <input type="radio" name="autoconfirm" id="autoconfirm" value="1" <?php echo $autoconfirm == "1" ? "checked" : "" ?>/> <?php echo YES;?> &nbsp;
                        <input type="radio" name="autoconfirm" id="autoconfirm" value="0" <?php echo $autoconfirm == "0" ? "checked" : "" ?> /> <?php echo NO;?>

                    </div>
                    <div class="cell third">
                        <div class="valign">
                            
                            <input type="checkbox" name="coupon" id="coupon" value="1" <?php echo $coupon ? "checked" : "" ?>/>
                            <?php echo MXM_COUPON ?>
                                <img src='images/info.png' border="0"  class="tipTip" title="<?php echo TTIP_1 ?>"/>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                    
                    <hr/>
                    <div class="form-row">
                        <div class="cell third">
                                <label><?php echo BOOK_TIME_INTRV ?></label>
                                <select name="interval" class="select" onchange="updateDuration(this)">
                                    <option value="15" <?php echo $interval == "15" ? "selected" : "" ?> rel="<?php echo MIN15 ?>"><?php echo MIN15 ?></option>
                                    <option value="30" <?php echo $interval == "30" ? "selected" : "" ?> rel="<?php echo MIN30 ?>"><?php echo MIN30 ?></option>
                                    <option value="45" <?php echo $interval == "45" ? "selected" : "" ?> rel="<?php echo MIN45 ?>"><?php echo MIN45 ?></option>
                                    <!-- 45 minutes interval causing wrong display of manual bookings by admin. use at your own discretion -->
                                    <option value="60" <?php echo $interval == "60" ? "selected" : "" ?> rel="<?php echo H1 ?>"><?php echo H1 ?></option>
                                    <option value="120" <?php echo $interval == "120" ? "selected" : "" ?> rel="<?php echo H2 ?>"><?php echo H2 ?></option>
                                    <option value="180" <?php echo $interval == "180" ? "selected" : "" ?> rel="<?php echo H3 ?>"><?php echo H3 ?></option>
                                    <option value="240" <?php echo $interval == "240" ? "selected" : "" ?> rel="<?php echo H4 ?>"><?php echo H4 ?></option>
                                    <option value="300" <?php echo $interval == "300" ? "selected" : "" ?> rel="<?php echo H5 ?>"><?php echo H5 ?></option>
                                        
                                    <option value="360" <?php echo $interval == "360" ? "selected" : "" ?> rel="<?php echo H6 ?>"><?php echo H6 ?></option>
                                    <option value="420" <?php echo $interval == "420" ? "selected" : "" ?> rel="<?php echo H7 ?>"><?php echo H7 ?></option>
                                    <option value="480" <?php echo $interval == "480" ? "selected" : "" ?> rel="<?php echo H8 ?>"><?php echo H8 ?></option>
                                    <option value="540" <?php echo $interval == "540" ? "selected" : "" ?> rel="<?php echo H9 ?>"><?php echo H9 ?></option>
                                    <option value="600" <?php echo $interval == "600" ? "selected" : "" ?> rel="<?php echo H10 ?>"><?php echo H10 ?></option>
                                    <option value="660" <?php echo $interval == "660" ? "selected" : "" ?> rel="<?php echo H11 ?>"><?php echo H11 ?></option>
                                    <option value="720" <?php echo $interval == "720" ? "selected" : "" ?> rel="<?php echo H12 ?>"><?php echo H12 ?></option>
                                        
                                </select> <img src='images/info.png' border="0"  class=" tipTip imgCenter" title="<?php echo INTERV_MSG ?>"/>
                            </div>
                         <div class="cell third">
                             <label><?php echo TIME_BEFORE ?></label>
                             <input type="text" name="time_before" class="small1 " id="time_before" onkeyup="onlyDigits(this)" value="<?php echo $time_before ?>" /> <?php echo HOURS?>
                             <img src='images/info.png' border="0"  class=" tipTip imgCenter" title="<?php echo TTIP_2 ?>"/>
                         </div>
                        <div class="clear"></div>
                    </div>
                    
                    <div class="form-row">
                            <div class="cell third">
                                
                                <div class="valign">
                                    <input type="checkbox" name="show_multiple_spaces" id="show_multiple_spaces" value="1" <?php echo $show_multiple_spaces ? "checked" : "" ?> />
                                    &nbsp;<?php echo ALLOW_MULT_SPACES ?><br/><br/>
                                    <input type="checkbox" name="show_spaces_left" id="show_spaces_left" value="1" <?php echo $show_spaces_left == "1" ? "checked" : "" ?>/>
                                &nbsp;<?php echo SHOW_SPAC?>
                                </div>
                            </div>
                            <div class="cell third">
                                <div class="disabled left">
                                    <div class="lock passive recurring" ></div>
                                    <div class="valign">
                                        <input type="text" name="spaces_available" id="spaces_available" class="small1 " onkeyup="onlyDigits(this)" value="<?php echo $spaces_available ?>" /> 
                                        &nbsp;<?php echo SPACES_INTRV ?>
                                    </div>
                                </div>
                            </div>
                        <div class="clear"></div>
                        </div>
                     <div class="form-row" style="margin-top:10px">
                         <div class="cell third">
                             <label class="uc-word"><?php echo SPOT_MSG?></label>
                             <select name="allow_times_min" id="allow_times_min" class="select left">
                                 
                                <option value="">Please Select</option>
                                <option value="1" <?php echo $allow_times_min == "1" ? "selected='selected'" : "" ?>>1&nbsp;x&nbsp;<?php echo $durationList[$interval]?></option>
                                <option value="2" <?php echo $allow_times_min == "2" ? "selected='selected'" : "" ?>>2&nbsp;x&nbsp;<?php echo $durationList[$interval]?></option>
                                <option value="3" <?php echo $allow_times_min == "3" ? "selected='selected'" : "" ?>>3&nbsp;x&nbsp;<?php echo $durationList[$interval]?></option>
                                <option value="4" <?php echo $allow_times_min == "4" ? "selected='selected'" : "" ?>>4&nbsp;x&nbsp;<?php echo $durationList[$interval]?></option>
                                <option value="99" <?php echo $allow_times_min == "99" ? "selected='selected'" : "" ?>><?php echo UNLM_SPOT?></option>
                            
                             </select>
                             <img src='images/info.png' border="0"  class=" tipTip imgCenter" title=" <?php echo OFFL_INVC_MSG?>"/>
                         </div>
                         <div class="cell third">
                             <label class="uc-word"><?php echo SPOT_MSG_MAX?></label>
                             <select name="allow_times" id="allow_times" class="select left">
                                 
                                <option value="">Please Select</option>
                                <option value="1" <?php echo $allow_times == "1" ? "selected='selected'" : "" ?>>1&nbsp;x&nbsp;<?php echo $durationList[$interval]?></option>
                                <option value="2" <?php echo $allow_times == "2" ? "selected='selected'" : "" ?>>2&nbsp;x&nbsp;<?php echo $durationList[$interval]?></option>
                                <option value="3" <?php echo $allow_times == "3" ? "selected='selected'" : "" ?>>3&nbsp;x&nbsp;<?php echo $durationList[$interval]?></option>
                                <option value="4" <?php echo $allow_times == "4" ? "selected='selected'" : "" ?>>4&nbsp;x&nbsp;<?php echo $durationList[$interval]?></option>
                                <option value="99" <?php echo $allow_times == "99" ? "selected='selected'" : "" ?>><?php echo UNLM_SPOT?></option>
                            
                             </select>
                             <img src='images/info.png' border="0"  class=" tipTip imgCenter" title=" <?php echo OFFL_INVC_MSG?>"/>
                         </div>
                         <div class="clear"></div>
                     </div>
                    <hr/>
                    
                    <div class="form-row">
                        <div class="cell half">
                            <div class="valign">
                                <?php echo CALND_WEEK_STARTS?>
                                <input type="radio" name="startDay" id="startDay" value="0" <?php echo $startDay == "0" ? "checked" : "" ?> /> <?php echo SUN?>&nbsp;
                                <input type="radio" name="startDay" id="startDay" value="1" <?php echo $startDay == "1" ? "checked" : "" ?>/> <?php echo MON?>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    
                    <div class="form-row">
                        <h3><?php echo BOOK_MSG_DAY;?></h3>
                        <table cellpadding="5px" cellspacing="0" width="565" class="t_availabolity">
                         <?php
    for ($i = 0; $i < 7; $i++) {
        $step = $j = 0;
        $step = 15;
        $items = (isset($week[$i]) && (count($week[$i]) > 0)) ? count($week[$i]) : 1;
        //print $items;
        ?>
                        <tr class="<?php echo !($i%2)?"odd":""?>">
                            <td align=left valign="center" width="100"><?php echo getWeek($i) ?>&nbsp;</td>
                            <td class="availability">
        <?php for ($j = 0; $j < $items; $j++) { ?>
                                    <div class="row_a">
                                        <span class="left lh30" ><?php echo TXT_DAY_SRV_FROM?>&nbsp;&nbsp;</span>
                                        <input type="text" name="week_from_h_<?php echo $i ?>[]" value="<?php if(isset($seArr["{$i}"]["{$j}"])){echo $seArr["{$i}"]["{$j}"][0];}else{ echo '  - -' ;} ?>" class="adjStartEnd adj_hrs_0" />
                                        <span class="left lh30" >&nbsp;:&nbsp;</span>
                                        <input type="text" name="week_from_m_<?php echo $i ?>[]" value="<?php if(isset($seArr["{$i}"]["{$j}"])){echo $seArr["{$i}"]["{$j}"][1];}else{ echo '  - -' ;} ?>" class="adjStartEnd adj_mins_0" />
                                        <span class="left lh30" >&nbsp;&nbsp;&nbsp;<?php echo TXT_DAY_SRV_TO?>&nbsp;&nbsp;</span>
                                        <input type="text" name="week_to_h_<?php echo $i ?>[]" value="<?php if(isset($seArr["{$i}"]["{$j}"])){echo $seArr["{$i}"]["{$j}"][2];}else{ echo '  - -' ;} ?>" class="adjStartEnd adj_hrs_0" />
                                        <span class="left lh30" >&nbsp;:&nbsp;</span>
                                        <input type="text" name="week_to_m_<?php echo $i ?>[]" value="<?php if(isset($seArr["{$i}"]["{$j}"])){echo $seArr["{$i}"]["{$j}"][3];}else{ echo '  - -' ;} ?>" class="adjStartEnd adj_mins_0" />



                                    <?php if($j > 0): ?>
                                    	<a href="#" class="adj_SE_remove" >delete this</a>
                                    <?php else: ?>
                                        <a href="#" class="adj_SE_clear" >delete this</a>
                                        <?php endif;?>
                                    </div>
                                        <?php if(isset($seArr["{$i}"]["{$j}"]) ){?>
                                            <input type="hidden" name="_week_from_h_<?php echo $i ?>[]" value="<?php echo $seArr["{$i}"]["{$j}"][0]?>">
                                            <input type="hidden" name="_week_from_m_<?php echo $i ?>[]" value="<?php echo $seArr["{$i}"]["{$j}"][1]?>">
                                            <input type="hidden" name="_week_to_h_<?php echo $i ?>[]" value="<?php echo $seArr["{$i}"]["{$j}"][2]?>">
                                            <input type="hidden" name="_week_to_m_<?php echo $i ?>[]" value="<?php echo $seArr["{$i}"]["{$j}"][3]?>">
                                        <?php }?>
                                        <?php } ?>
                                <a href="javascript:;" onclick="addTime(<?php echo $i ?>,this)" class="buttonAddSmall"><span>add</span></a>
                            </td>
                        </tr>
    <?php } ?>
                        </table>
                    </div>
                    
                    <hr/>

                    
                    <div class="form-row">
                        <h3><?php echo EVENT_DISP_SETT?></h3>
                        <div class="cell half">
                            <label><?php echo SHOW_TTL?></label>
                             <input type="radio" name="show_event_titles" id="show_event_titles" value="1" <?php echo $show_event_titles == "1" ? "checked" : "" ?>/>&nbsp; <?php echo YES;?>
                             <input type="radio" name="show_event_titles" id="show_event_titles" value="0" <?php echo $show_event_titles == "0" ? "checked" : "" ?> /> <?php echo NO;?>
                        </div>
                        <div class="cell half">
                            <label><?php echo SHOW_SEATS?></label>
                            <input type="radio" name="show_available_seats" id="show_available_seats" value="1" <?php echo $show_available_seats == "1" ? "checked" : "" ?>/> <?php echo YES;?> &nbsp;
                            <input type="radio" name="show_available_seats" id="show_available_seats" value="0" <?php echo $show_available_seats == "0" ? "checked" : "" ?> /> <?php echo NO;?>
                            
                        </div>
                        <div class="clear"></div>
                    </div>
                    
                    <div class="form-row">
                        
                        <div class="cell half">
                            <label><?php echo SHOW_IMG?></label>
                            <input type="radio" name="show_event_image" id="show_event_image" value="1" <?php echo $show_event_image == "1" ? "checked" : "" ?>/> <?php echo YES;?> &nbsp;
                            <input type="radio" name="show_event_image" id="show_event_image" value="0" <?php echo $show_event_image == "0" ? "checked" : "" ?> /> <?php echo NO;?> &nbsp;
                            
                        </div>

                        <div class="clear"></div>
                    </div>

                    <hr/>
                    <div class="form-row">
                        <h3><?php echo EMAIL_SETTINGS?></h3>
                        <div class="cell third">
                            <label><?php echo EMAIL_FROM_NAME?> </label>
                            <input type="text" name="fromName" id="fromName" value="<?php echo $fromName?>" /><img src='images/info.png' border="0"  class=" tipTip imgCenter" title="<?php echo TTIP_3 ?>"/>
                        </div>
                        <div class="cell half">
                            <label><?php echo EMAIL_FROM_EMAIL?></label>
                                <input type="text" name="fromEmail" id="fromEmail" value="<?php echo $fromEmail ?>" /><img src='images/info.png' border="0"  class=" tipTip imgCenter" title="<?php echo TTIP_4 ?>"/>
                            </div>
                        <div class="clear"></div>
                    </div>
                    
                    
                    
                <hr/>
                    <button class="save" type="submit" onclick="checkBookings();return false;"><span><?php echo ADM_BTN_SUBMIT;?></span></button>
                            <input value="yes" name="edit_page" type="hidden" />
                            <input value="<?php echo $id; ?>" name="id" type="hidden" />
                            <input value="" name="bookingsToDelete" id="bookingsToDelete" type="hidden"/>
            </form>
        </div>

        



    <?php include "includes/admin_footer.php"; ?>
<?php } ?>