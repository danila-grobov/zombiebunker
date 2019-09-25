<?php
/******************************************************************************
#                         BookingWizz v5.5
#******************************************************************************
#      Author:     Convergine (http://www.convergine.com)
#      Website:    http://www.convergine.com
#      Support:    http://support.convergine.com
#      Version:    5.5
#
#      Copyright:   (c) 2009 - 2014  Convergine.com
#
#******************************************************************************/
require_once("../includes/config.php"); //Load the configurationsa

$response = array("res"=>true,"mess"=>"");
$message = "Please note, following confirmed or paid reservations will be canceled:\n\n";
$result = true;

$id = (!empty($_REQUEST["id"])) ? $_REQUEST["id"] : '';

$interval = getServiceSettings($id,'interval');
$bookingsList = $bookings = array();
for ($i = 0; $i < 7; $i++) {
    $week_from_h = (!empty($_REQUEST["week_from_h_" . $i]) && is_array($_REQUEST["week_from_h_" . $i])) ? ($_REQUEST["week_from_h_" . $i]) : '';
    $week_from_m = (!empty($_REQUEST["week_from_m_" . $i]) && is_array($_REQUEST["week_from_m_" . $i])) ? $_REQUEST["week_from_m_" . $i] : '';
    $week_to_h = (!empty($_REQUEST["week_to_h_" . $i]) && is_array($_REQUEST["week_to_h_" . $i])) ? ($_REQUEST["week_to_h_" . $i]) : '';
    $week_to_m = (!empty($_REQUEST["week_to_m_" . $i]) && is_array($_REQUEST["week_to_m_" . $i])) ? $_REQUEST["week_to_m_" . $i] : '';

    $_week_from_h = (!empty($_REQUEST["_week_from_h_" . $i]) && is_array($_REQUEST["_week_from_h_" . $i])) ? ($_REQUEST["_week_from_h_" . $i]) : '';
    $_week_from_m = (!empty($_REQUEST["_week_from_m_" . $i]) && is_array($_REQUEST["_week_from_m_" . $i])) ? $_REQUEST["_week_from_m_" . $i] : '';
    $_week_to_h = (!empty($_REQUEST["_week_to_h_" . $i]) && is_array($_REQUEST["_week_to_h_" . $i])) ? ($_REQUEST["_week_to_h_" . $i]) : '';
    $_week_to_m = (!empty($_REQUEST["_week_to_m_" . $i]) && is_array($_REQUEST["_week_to_m_" . $i])) ? $_REQUEST["_week_to_m_" . $i] : '';



    for ($j = 0; $j < count($_week_from_h); $j++) {
        if (isset($_week_from_h[$j])) {
            if ( !isset($week_from_h[$j])||
                !isset($week_from_m[$j])||
                !isset($week_to_h[$j])||
                !isset($week_to_m[$j])||

                $_week_from_h[$j] != $week_from_h[$j] ||
                $_week_from_m[$j] != $week_from_m[$j] ||
                $_week_to_h[$j] != $week_to_h[$j] ||
                $_week_to_m[$j] != $week_to_m[$j]
            ) {

                //print "{$_week_from_h[$j]}:{$_week_from_m[$j]} - {$_week_to_h[$j]}:{$_week_to_m[$j]}\n";

                $ii=0;for($a = "2000-01-01 {$_week_from_h[$j]}:{$_week_from_m[$j]}";$a<"2000-01-01 {$_week_to_h[$j]}:{$_week_to_m[$j]}";$a = date("Y-m-d H:i",strtotime("$a +{$interval} minutes"))){

                    $from = explode(" ",$a);
                    $from = end($from);

                    $to = explode(" ",date("Y-m-d H:i",strtotime("$a +{$interval} minutes")));
                    $to = end($to);

                    $weekday = $i==0?6:$i-1;
                    $sql = "SELECT br.id,bri.reserveDateFrom,bri.reserveDateTo FROM bs_reservations_items bri
                            INNER JOIN bs_reservations br ON br.id=bri.reservationID
                            WHERE br.status IN(1,4) AND bri.reserveDateFrom >= '".DATETIME."' AND
                            bri.reserveDateFrom LIKE '%{$from}%' AND
                            bri.reserveDateTo LIKE '%{$to}%' AND
                            WEEKDAY(bri.reserveDateFrom) = '$weekday' GROUP BY br.id";//print "$sql\n\n";
                     $res = $mysqli->query($sql);
                     while($row = $res->fetch_assoc($res)){
                         $bookingsList[$row['id']]= "#{$row['id']} ( ".getDateFormat($row['reserveDateFrom'])." "._time($row['reserveDateFrom'])." - "._time($row['reserveDateTo'])." )\n";
                         $result = false;
                     }

                            if($ii>100){print "to match iterations"; break;}
                            $ii++;
                }
            }
        }
    }
}
//bw_dump($bookingsList);

foreach($bookingsList as $k=>$booking){
    $message.=$booking;
    $bookings[]=$k;
}


$response['res']=$result;
$response['mess']=$message;
$response['bookings']=json_encode($bookings);

echo json_encode($response);
