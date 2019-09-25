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

function checkAvailableDay($datetocheck, $serviceID,$uiCalendar=false){
    
    $result = array("res"=>false,'price'=>0);
    $year = date("Y",strtotime($datetocheck));
    $date = '2000-'.  substr($datetocheck, 5);
    $date = $date=='2000-12-31'?date("Y-m-d",strtotime("$date -1 day")):$date;

    //print $date."<br>";
    $sSQL = "SELECT * FROM bs_schedule_days WHERE idService='{$serviceID}' AND (
		dateFrom <= '{$date}' AND dateTo ".($uiCalendar?">=":">")." '{$date}')";
    $res = $mysqli->query($sSQL) or die ("Error checking availability");
    if($res->num_rows>0){
        $row = $res->fetch_assoc();
        $daysBefore = getServiceSettings($serviceID, "daysBefore");
        $result['scheduleInfo']=array('from'=>  str_replace('2000', $year, $row['dateFrom']),'to'=>str_replace('2000', $year, $row['dateTo']));
        if($daysBefore>0){
            if(getDaysInterval(date("Y-m-d"),$datetocheck)>=$daysBefore){
                $result['res']=true;
                $result['price']=$row['price'];
                
            }
        }else{
            $result['res']=true;
            $result['price']=$row['price'];
        }
        
    }
    return $result;
    
    //print $date;
}
function checkForCheckoutDate($datetocheck,$serviceID){

    if($datetocheck==date("Y-m-d")) return false;


    $sSQL = "SELECT brt.id FROM `bs_reserved_time` brt
            WHERE brt.serviceID='{$serviceID}' AND
		brt.reserveDateFrom = '{$datetocheck}'";

    $res = $mysqli->query($sSQL);
    if($res->num_rows>0){
        $sSQL = "SELECT brt.id FROM `bs_reserved_time` brt
            WHERE brt.serviceID='{$serviceID}' AND
		brt.reserveDateTo = '{$datetocheck}'";

            $res = $mysqli->query($sSQL);
            if($res->num_rows<1){
            $sSQL = "SELECT bri.id FROM `bs_reservations_items` bri
                INNER JOIN bs_reservations br on bri.reservationID = br.id
                WHERE br.serviceID='{$serviceID}' AND
                            bri.reserveDateTo = '{$datetocheck}'
                            AND (br.status='1' OR br.status='4')";
            //print $sSQL;
            $res = $mysqli->query($sSQL);
            if($res->num_rows<1){
                return true;
            }
        }else{
            return false;
        }
    }
    $sSQL = "SELECT bri.id FROM `bs_reservations_items` bri
                INNER JOIN bs_reservations br on bri.reservationID = br.id
                WHERE br.serviceID='{$serviceID}' AND
                            bri.reserveDateFrom = '{$datetocheck}'
                            AND (br.status='1' OR br.status='4')";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if($res->num_rows>0){

        $sSQL = "SELECT bri.id FROM `bs_reservations_items` bri
                INNER JOIN bs_reservations br on bri.reservationID = br.id
                WHERE br.serviceID='{$serviceID}' AND
                            bri.reserveDateTo = '{$datetocheck}'
                            AND (br.status='1' OR br.status='4')";
        //print $sSQL;
        $res = $mysqli->query($sSQL);
        if($res->num_rows<1){


            if(checkForEndStartInterval($datetocheck,$serviceID)){
                $sSQL = "SELECT brt.id FROM `bs_reserved_time` brt
                    WHERE brt.serviceID='{$serviceID}' AND
                brt.reserveDateTo = '{$datetocheck}'";

                $res = $mysqli->query($sSQL);
                if($res->num_rows<1){
                    return true;
                }
            }
        }
    }


    return false;
}

function checkForEndStartInterval($datetocheck,$serviceID){
    $_datetocheck = date("2000-m-d",strtotime($datetocheck));
    $_datetocheckPrev = date("2000-m-d",strtotime($datetocheck." +1 day"));

    $sSQL = "SELECT * FROM bs_schedule_days WHERE dateFrom ='$_datetocheck' AND idService='$serviceID'";
    $res = $mysqli->query($sSQL);
    if($res->num_rows>0){
        $sSQL = "SELECT * FROM bs_schedule_days WHERE dateTo ='$_datetocheckPrev' AND idService='$serviceID'";
        $res = $mysqli->query($sSQL);
        if($res->num_rows<1){
            return false;
        }
    }
    return true;
}
function checkSpotsForDay($datetocheck,$serviceID,$type = false){
    
    # check for admin reserve
    if($type){
        $sSQL = "SELECT brt.id FROM `bs_reserved_time` brt
            WHERE brt.serviceID='{$serviceID}' AND (
		(brt.reserveDateFrom < '{$datetocheck}' AND brt.reserveDateTo >= '{$datetocheck}') )";
    }else{
        $sSQL = "SELECT brt.id FROM `bs_reserved_time` brt
            WHERE brt.serviceID='{$serviceID}' AND (
		(brt.reserveDateFrom <= '{$datetocheck}' AND brt.reserveDateTo > '{$datetocheck}') )";
    }
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if($res->num_rows>0){
        return false;
    }else{
        # check for customer reserve
        if($type){
            $sSQL = "SELECT bri.id FROM `bs_reservations_items` bri
                INNER JOIN bs_reservations br on bri.reservationID = br.id
                WHERE br.serviceID='{$serviceID}' AND (
                            (bri.reserveDateFrom < '{$datetocheck}' AND bri.reserveDateTo >= '{$datetocheck}') )
                            AND (br.status='1' OR br.status='4')  
                            ORDER BY bri.reserveDateFrom ASC";
        }else{
            $sSQL = "SELECT bri.id FROM `bs_reservations_items` bri
                INNER JOIN bs_reservations br on bri.reservationID = br.id
                WHERE br.serviceID='{$serviceID}' AND (
                            (bri.reserveDateFrom <= '{$datetocheck}' AND bri.reserveDateTo > '{$datetocheck}') )
                            AND (br.status='1' OR br.status='4')
                            ORDER BY bri.reserveDateFrom ASC";
        }
        //print $sSQL;
        $res = $mysqli->query($sSQL);
        if($res->num_rows>0){

                return false;

        }else{
            return true;
        }
    }
    return false;
}

function checkSpotsForDayInterval($from,$to,$serviceID){
    # check for admin reserve

    $sSQL = "SELECT bri.id FROM `bs_reserved_time` bri
            WHERE bri.serviceID='{$serviceID}' AND (
				(bri.reserveDateFrom < '{$to}' AND bri.reserveDateTo >= '{$to}') OR
				(bri.reserveDateTo > '{$from}' AND bri.reserveDateFrom <= '{$from}') OR
				(bri.reserveDateFrom <= '{$from}' AND bri.reserveDateTo >= '{$to}') OR
				(bri.reserveDateFrom >= '{$from}' AND bri.reserveDateTo <= '{$to}'))";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if($res->num_rows>0){
        return 0;
    }else{

        # check for customer reserve

        $sSQL = "SELECT bri.* FROM `bs_reservations_items` bri
                INNER JOIN bs_reservations br on bri.reservationID = br.id
                WHERE br.serviceID='{$serviceID}' AND (
                                    (bri.reserveDateFrom < '{$to}' AND bri.reserveDateTo >= '{$to}') OR
                                    (bri.reserveDateTo > '{$from}' AND bri.reserveDateFrom <= '{$from}') OR
                                    (bri.reserveDateFrom <= '{$from}' AND bri.reserveDateTo >= '{$to}') OR
                                    (bri.reserveDateFrom >= '{$from}' AND bri.reserveDateTo <= '{$to}'))
                                    AND (br.status='1' OR br.status='4')
                                    ORDER BY bri.reserveDateFrom ASC";
        //print $sSQL;
        $res = $mysqli->query($sSQL);
        if($res->num_rows>0){
            return 0;
        }else{
            return 1;
        }
    }
    return 0;
}

function _checkSpotsForDayInterval($from,$to,$serviceID,$id=null){
    # check for admin reserve

    $sSQL = "SELECT bri.id,bri.reason FROM `bs_reserved_time` bri
            WHERE bri.serviceID='{$serviceID}' AND (
				(bri.reserveDateFrom < '{$to}' AND bri.reserveDateTo >= '{$to}') OR
				(bri.reserveDateTo > '{$from}' AND bri.reserveDateFrom <= '{$from}') OR
				(bri.reserveDateFrom <= '{$from}' AND bri.reserveDateTo >= '{$to}') OR
				(bri.reserveDateFrom >= '{$from}' AND bri.reserveDateTo <= '{$to}'))";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if($res->num_rows>0){
        $row = $res->fetch_assoc();
        return ERROR_RESERVED_BY_ADMIN." <a href='bs-reserve-day.php?id={$row['id']}'>#{$row['id']} ". REASON .": {$row['reason']}</a>";
    }else{

        # check for customer reserve

        $sSQL = "SELECT bri.* FROM `bs_reservations_items` bri
                INNER JOIN bs_reservations br on bri.reservationID = br.id
                WHERE br.serviceID='{$serviceID}' AND (
                                    (bri.reserveDateFrom < '{$to}' AND bri.reserveDateTo >= '{$to}') OR
                                    (bri.reserveDateTo > '{$from}' AND bri.reserveDateFrom <= '{$from}') OR
                                    (bri.reserveDateFrom <= '{$from}' AND bri.reserveDateTo >= '{$to}') OR
                                    (bri.reserveDateFrom >= '{$from}' AND bri.reserveDateTo <= '{$to}'))
                                    AND (br.status='1' OR br.status='4') AND  bri.reservationID<>{$id}
                                    ORDER BY bri.reserveDateFrom ASC";
        //print $sSQL;
        $res = $mysqli->query($sSQL);
        if($res->num_rows>0){
            $row = $res->fetch_assoc();
            return ERROR_RESERVED_ALREADY." <a href='bs-bookings_day-edit.php?id={$row['reservationID']}'>#{$row['reservationID']}</a>";


        }else{
            return 1;
        }
    }
    return 0;
}

function getDayPrice($date,$serviceID){
    $date = '2000-'.  substr($date, 5);
    $sSQL = "SELECT * FROM bs_schedule_days WHERE idService='{$serviceID}' AND (
		dateFrom <= '{$date}' AND dateTo >= '{$date}')";
                
    $res = $mysqli->query($sSQL) or die ("Error checking availability");
    if($res->num_rows>0){
        $row= $res->fetch_assoc();
        
        return $row['price'];
    }else{
        return 0;
    }
}

function getDaysInterval($dateFrom,$dateTo){
    $dateFrom = substr($dateFrom, 0,10);
    $dateTo = substr($dateTo, 0,10);
    $interval = strtotime("$dateTo 00:00:00")-strtotime("$dateFrom 00:00:00");
    
    return ceil($interval/(60*60*24));
}

function checkForAvailability($from,$to,$serviceID){
    $from = '2000-'.  substr($from, 5);
    $to = '2000-'.  substr($to, 5);
    $sSQL = "SELECT * FROM bs_schedule_days WHERE idService='{$serviceID}' AND (
		dateFrom <= '{$from}' AND dateTo >= '{$to}')";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if($res->num_rows>0){
        return 1;
    }else{
        return 0;
    }
}

function _checkForAvailability($from,$to,$serviceID){
    $from = _date($from);
    $to = _date($to);
    $result = array('res'=>false,'message'=>'','totalPrice'=>0);
    $totalCost = 0;
    $intervals = array();
    
    $maxDays = getServiceSettings($serviceID, "maxDays");
    $minDays = getServiceSettings($serviceID, "minDays");
    $days = getDaysInterval($from,$to);
    if($days>$maxDays ||$days<0){
        $result['message']=MULTI_DAY_TXT1." {$maxDays} ".MULTI_DAY_TXT2;
        return $result;
    }elseif($minDays>$days){
        $result['message']=MULTI_DAY_TXT1." {$minDays} ".MULTI_DAY_TXT2_1;
        return $result;
    }
    $i = 0;
    $j=0;
    $currSchedule="";
    $scheduleInfo = array();
    for($a=$from;$a<$to;$a=date("Y-m-d",strtotime("$a +1 days"))){
        $availability = checkAvailableDay($a,$serviceID);
        if($availability['res']){
            $totalCost+=$availability['price'];
            
            if($from>$availability['scheduleInfo']['from']){
                $_From = $from;
            }else{
                $_From =$availability['scheduleInfo']['from'];
            }
             if($to<$availability['scheduleInfo']['to']){
                $_To = $to;
            }else{
                $_To =$availability['scheduleInfo']['to'];
            }
            $_To = strpos($_To,"12-31") && (date("Y",strtotime($from))!=date("Y",strtotime($to)))?date("Y-m-d",strtotime("$_To +1 day")):$_To;
            $schedule = "{$_From}:{$_To}";
            $scheduleInfo[$schedule]=array('from'=>$_From,'to'=>$_To);
            
            if($currSchedule!=$schedule)$j++;
            $currSchedule = $schedule;
            
            isset($intervals[$schedule])?$intervals[$schedule]+=$availability['price']:$intervals[$schedule]=$availability['price'];
            $scheduleInfo[$schedule]['price']=$intervals[$schedule];
            
            $scheduleInfo[$schedule]['_price']=$availability['price'];
            
            $j++;
        }else{
           $result['message']=MULTI_DAY_TXT3;
           return $result;
        }
        if($i>100){
            $message = "error to match iterations 'function _checkForAvailability 
                         selectedDayFrom=$from
                         selectedDayTo=$to
                         serviceID=$serviceID'";
            _error_log($message);
            break;
            
            }
        $i++;
    }
    foreach($intervals as $k=>$v){
        $v=  number_format($v,2);
        $result['message'].="<div>{$k} - $v$</div>";
    }
    $result['totalPrice'] = $totalCost;
    $result['res'] = true;
    $result['intervals']=count($intervals);
    $result['info']=$scheduleInfo;
    
    return $result;
}

function checkSpotsForDayShedule($datetocheck,$serviceID){
    $responce = array("type"=>"available",'message'=>MULTI_DAY_TXT4);
    
    $availability = checkAvailableDay($datetocheck,$serviceID);
    if(!$availability['res']){
        $responce = array("type"=>"notAvailable",'message'=>"<span style=\"color:red\">".MULTI_DAY_NOT_AV."</span>");
        return $responce;
     }
    # check for admin reserve
    
    $sSQL = "SELECT brt.* FROM `bs_reserved_time` brt
            WHERE brt.serviceID='{$serviceID}' AND (
		(brt.reserveDateFrom <= '{$datetocheck}' AND brt.reserveDateTo >= '{$datetocheck}') )";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if($res->num_rows>0){
        $row= $res->fetch_assoc();
        $responce = array("type"=>"admin",'message'=>MULTI_DAY_BK_ADM."<br>
                                                       <span class=\"bookInfo\">[ <a href=\"bs-reserve-day.php?id={$row['id']}\">".MULTI_DAY_BK_ADM2." {$row['reason']}</a> ]</span>");

    }else{
        # check for customer reserve
        
        $sSQL = "SELECT bri.*,br.* FROM `bs_reservations_items` bri
                INNER JOIN bs_reservations br on bri.reservationID = br.id
                WHERE br.serviceID='{$serviceID}' AND (
                            (bri.reserveDateFrom <= '{$datetocheck}' AND bri.reserveDateTo >= '{$datetocheck}') )
                            AND (br.status='1' OR br.status='4')  
                            ORDER BY bri.reserveDateFrom ASC";
        //print $sSQL;
        $res = $mysqli->query($sSQL);
        if($res->num_rows>0){
                $row = $res->fetch_assoc();
                $bookInfo = "{$row['name']}<br/>".BOOKING_FRM_PHONE.":{$row['phone']}<br/> ".strtolower(TXT_DAY_SRV_FROM).":&nbsp;&nbsp;".getDateFormat($row['reserveDateFrom'])." <br/> ".strtolower(TXT_DAY_SRV_TO).":&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".getDateFormat($row['reserveDateTo']) ;
                $responce = array("type"=>"user",'message'=>"This day booked <br> <div style=\"margin:5px 0;border-left:1px solid #ccc;border-right:1px solid #ccc;padding:0 5px;\"> <a href='bs-bookings_day-edit.php?id={$row['id']}'>{$bookInfo}</a> </div>");

        }
    }
    return $responce;
}
function _checkSpotsForDayShedule($datetocheck,$serviceID,$class){
    $_datetocheck = getDateFormat($datetocheck);
    $responce = $responce = "<tr  class=\"$class\"><td><span class=\"date\">" . $_datetocheck . "</span></td><td>N/A</td><td>N/A</td><td class=\"noBorderRight\">N/A</td><td><a href='bs-reserve-day.php?reserveDateFrom={$datetocheck}' class='greedButton'>".MULTI_DAY_TXT5."</a></td></tr>";
    
    $availability = checkAvailableDay($datetocheck,$serviceID);
    if(!$availability['res']){
        $responce = "<tr  class=\"$class\"><td><span class=\"date\">" . $_datetocheck . "</span></td><td>N/A</td><td>N/A</td><td class=\"noBorderRight\">N/A</td><td></td></tr>";
        return $responce;
     }
    # check for admin reserve
    
    $sSQL = "SELECT brt.* FROM `bs_reserved_time` brt
            WHERE brt.serviceID='{$serviceID}' AND (
		(brt.reserveDateFrom <= '{$datetocheck}' AND brt.reserveDateTo >= '{$datetocheck}') )";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if($res->num_rows>0){
        $row= $res->fetch_assoc();
        /*$responce = array("type"=>"admin",'message'=>"This day booked by admin <br>
                                                       <span class=\"bookInfo\">[ <a href=\"bs-reserve-day.php?id={$row['id']}\">reason: {$row['reason']}</a> ]</span>");*/
         $responce = "<tr  class=\"$class\"><td><span class=\"date\">" . $_datetocheck . "</span></td>
             <td colspan=\"4\"><a href=\"bs-reserve-day.php?id={$row['id']}\">".MULTI_DAY_TXT6."</a><img src='images/info_small.png' border=\"0\" class=\" tipTip imgCenter\"  title=\"".ucfirst(MULTI_DAY_BK_ADM2).":{$row['reason']} \"/></td>
             </tr>";
    }else{
        # check for customer reserve
        
        $sSQL = "SELECT bri.*,br.* FROM `bs_reservations_items` bri
                INNER JOIN bs_reservations br on bri.reservationID = br.id
                WHERE br.serviceID='{$serviceID}' AND (
                            (bri.reserveDateFrom <= '{$datetocheck}' AND bri.reserveDateTo >= '{$datetocheck}') )
                            AND (br.status='1' OR br.status='4')  
                            ORDER BY bri.reserveDateFrom ASC";
        //print $sSQL;
        $res = $mysqli->query($sSQL);
        if($res->num_rows>0){
                $row = $res->fetch_assoc();
                $bookInfo = "{$row['name']}<br/>".BOOKING_FRM_PHONE.":{$row['phone']}<br/> ".strtolower(TXT_DAY_SRV_FROM).":&nbsp;&nbsp;".getDateFormat($row['reserveDateFrom'])." <br/> ".strtolower(TXT_DAY_SRV_TO).":&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".getDateFormat($row['reserveDateTo']) ;
                $responce = array("type"=>"user",'message'=>MULTI_DAY_TXT7."<br> <div style=\"margin:5px 0;border-left:1px solid #ccc;border-right:1px solid #ccc;padding:0 5px;\"> <a href='bs-bookings_day-edit.php?id={$row['id']}'>{$bookInfo}</a> </div>");
                 $responce = "<tr  class=\"$class\"><td><span class=\"date\">" . $_datetocheck . "</span></td><td>{$row['name']}</td><td>{$row['phone']}</td><td class=\"noBorderRight\"><a href='bs-bookings_day-edit.php?id={$row['id']}'>{$row['email']}</a></td><td></td></tr>";
        }
    }
    return $responce;
}
function getScheduleTableDay($selectedDayFrom,$selectedDayTo, $serviceID){
    $text = "";
    $i=0;
    $class = 'odd';
    $text .= "<table style=\"width:756px\" border=\"0\" class=\"dataTable schedule\" align=\"left\" cellpadding=\"0\" cellspacing=\"0\">";
    $text .= "<thead><tr class=\"topRow\"><th>".SCHEDL_CUSTOMER_NAME."</th><th>Name</th><th>".SCHEDL_CUSTOMER_PHONE."</th><th  class=\"noBorderRight\">".SCHEDL_CUSTOMER_EMAIL."</th><th></th>&nbsp;</tr></thead>";
    for($a=$selectedDayFrom;$a<=$selectedDayTo;$a=date("Y-m-d",strtotime("$a +1 days"))){
        $class=$class=='even'?"odd":"even";
        $bookings = _checkSpotsForDayShedule($a,$serviceID,$class);
        $events = getEventsByDate($a,$serviceID);
        $text .= $bookings;   
        /*if(count($events)>0){
            foreach ($events as $event){
                //bw_dump($event);
                $text.="<div class='sh_event'><a class='naw' href='javascript:;'>&blacktriangledown;</a>";
                $text.="<h3>Event: <a href='bs-events-add.php?id={$event['event']['id']}'>{$event['event']['title']}</a></h3>";
                $text.="<div class='info'>";
                if(_date($event['event']['eventDate'])!=_date($event['event']['eventDateEnd'])){
                    $text.="<span><label>Date From:</label>".getDateFormat($event['event']['eventDate'])." ".  _time($event['event']['eventDate'])."</span>";
                    
                    $text.="<span><label>Date To:</label>".getDateFormat($event['event']['eventDateEnd'])." ".  _time($event['event']['eventDateEnd'])."</span>";
                }else{
                    $text.="<span><label>Date:</label>".getDateFormat($event['event']['eventDateEnd'])." (".  _time($event['event']['eventDate'])." - ".  _time($event['event']['eventDateEnd']).")</span>";
                }
                
                //$text.="<span><label>Title:</label>{$event['event']['title']}</span>";
                $text.="</div></div>";
            }
        }  */
          
        
        
        if($i>100){
            $message = "error to match iterations 'function getScheduleTableDay 
                         selectedDayFrom=$selectedDayFrom
                         selectedDayTo=$selectedDayTo
                         serviceID=$serviceID'";
            _error_log($message);
            break;
            
            }
        $i++;
    }
    $text .="</table>";
    return $text;
}
function _getScheduleTableDay($selectedDayFrom,$selectedDayTo, $serviceID){
    $text = "";
    $i=0;
    for($a=$selectedDayFrom;$a<=$selectedDayTo;$a=date("Y-m-d",strtotime("$a +1 days"))){
        $bookings = checkSpotsForDayShedule($a,$serviceID);
        $events = getEventsByDate($a,$serviceID);
        $text .= "<div class='{$bookings['type']} day'>
        <label>" . getDateFormat($a) . "</label>
            <div class='inner'>
                
                {$bookings['message']}
                </div>".($bookings['type']=='available'?"<a href='bs-reserve-day.php?reserveDateFrom={$a}' class='book'></a>":"")."
                <div style='clear:both'></div>";
               
        if(count($events)>0){
            foreach ($events as $event){
                //bw_dump($event);
                $text.="<div class='sh_event'><a class='naw' href='javascript:;'>&blacktriangledown;</a>";
                $text.="<h3>".TBL_EVENT.": <a href='bs-events-add.php?id={$event['event']['id']}'>{$event['event']['title']}</a></h3>";
                $text.="<div class='info'>";
                if(_date($event['event']['eventDate'])!=_date($event['event']['eventDateEnd'])){
                    $text.="<span><label>".MULTI_DAY_TXT8.":</label>".getDateFormat($event['event']['eventDate'])." ".  _time($event['event']['eventDate'])."</span>";
                    
                    $text.="<span><label>".MULTI_DAY_TXT9.":</label>".getDateFormat($event['event']['eventDateEnd'])." ".  _time($event['event']['eventDateEnd'])."</span>";
                }else{
                    $text.="<span><label>".TBL_DATE.":</label>".getDateFormat($event['event']['eventDateEnd'])." (".  _time($event['event']['eventDate'])." - ".  _time($event['event']['eventDateEnd']).")</span>";
                }
                
                //$text.="<span><label>Title:</label>{$event['event']['title']}</span>";
                $text.="</div></div>";
            }
        }  
          
        $text .="</div>";
        
        if($i>100){
            $message = "error to match iterations 'function getScheduleTableDay 
                         selectedDayFrom=$selectedDayFrom
                         selectedDayTo=$selectedDayTo
                         serviceID=$serviceID'";
            _error_log($message);
            break;
            
            }
        $i++;
    }
    return $text;
}

function checkManualDay($from, $to, $serviceID, $qty, $id) {
    $sSQL = "SELECT bri.id FROM `bs_reserved_time` bri
            WHERE bri.serviceID='{$serviceID}' AND id<>'{$id}' AND (
				(bri.reserveDateFrom < '{$to}' AND bri.reserveDateTo >= '{$to}') OR
				(bri.reserveDateTo > '{$from}' AND bri.reserveDateFrom <= '{$from}') OR
				(bri.reserveDateFrom <= '{$from}' AND bri.reserveDateTo >= '{$to}') OR
				(bri.reserveDateFrom >= '{$from}' AND bri.reserveDateTo <= '{$to}'))";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if($res->num_rows>0){
        return false;
    }else{
        return true;
    }
}
function checkManualDayByUserBooking($from, $to, $serviceID, $qty) {
    $result = array();
     $sSQL = "SELECT br.* FROM `bs_reservations_items` bri
             INNER JOIN bs_reservations br ON br.id=bri.reservationID
            WHERE br.serviceID='{$serviceID}' AND (
				(bri.reserveDateFrom < '{$to}' AND bri.reserveDateTo >= '{$to}') OR
				(bri.reserveDateTo > '{$from}' AND bri.reserveDateFrom <= '{$from}') OR
				(bri.reserveDateFrom <= '{$from}' AND bri.reserveDateTo >= '{$to}') OR
				(bri.reserveDateFrom >= '{$from}' AND bri.reserveDateTo <= '{$to}')) AND status IN ('1','4')";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    while($row = $res->fetch_assoc()){
        $result[]=$row;
    }
    return $result;
}


function getDaysAvailableByDate($date,$serviceID){
    $date = date("2000-m-d",strtotime($date));
    $sql = "SELECT * FROM bs_schedule_days WHERE dateFrom <= '{$date}' AND dateTo >='{$date}' AND idService={$serviceID}";
    
    $res = $mysqli->query($sql);
    $row = $res->fetch_assoc();
    $interval = (strtotime($row['dateTo'])-strtotime($date))/(60*60*24);
    return $interval;
}

function getAvailableDates($serviceID,$type=false){
    $days = array();
    $now = date("Y-m-d");
    $to = date("Y-m-d",strtotime("+360 days"));
    $i=0;
    for($a = $now;$a<$to;$a=date("Y-m-d",strtotime("$a +1 days"))){
        //print "$a<br>";
        $availability = checkAvailableDay($a,$serviceID,true);
        if($availability['res']){
            if(checkSpotsForDay($a,$serviceID,$type)){
                /*if((date("Y",strtotime($a))!=date("Y",strtotime("$a -1 days"))) && $type){
                    if($type)unset($days[count($days)-1]);
                }*/
                //$days[]="'".date("n-j-Y",strtotime($type?"$a +1 days":$a))."'";
                $days[]="'".date("n-j-Y",strtotime($a))."'";
            }
        }
        if($i>361){
            $message = "error to match iterations 'function getAvailableDates";

            _error_log($message);
            break;
        }
    }
    if($type) unset($days[0]);


    $jsArray = implode(",", $days);
    return $jsArray;
}

?>