<?php
define("MAIN_PATH", dirname(dirname(__FILE__))); //main path of BookingWizz directory
require_once(MAIN_PATH . "/includes/dbconnect.php"); //Load the db connect
function getService($id, $field = null)
{
    global $mysqli;
    $sql = "SELECT * FROM bs_services WHERE id='{$id}'";
    $res = $mysqli->query($sql);
    if ($field == null) {
        return $res->fetch_assoc();
    } else {
        $row = $res->fetch_assoc();
        return $row[$field];
    }
}
function getOption($option)
{
    global $mysqli;
    $option = trim($option);

    if (empty($option))
        return false;

    $option = addslashes($option);
    $sql = "SELECT * FROM bs_settings WHERE option_name='{$option}'";

    $res = $mysqli->query($sql) or die($sql . "<br>" .  $mysqli->error());
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();

        return $row['option_value'];
    } else {
        return false;
    }
}
function getServiceSettings($id, $field = null)
{
    global $mysqli;
    $serviceType = getService($id, 'type');
    if ($serviceType == 't') {
        $sql = "SELECT * FROM bs_service_settings bss
                INNER JOIN bs_services bs ON bss.serviceId  = bs.id
                WHERE bss.serviceID='{$id}'";
    } else {
        $sql = "SELECT * FROM  bs_service_days_settings bsds
                INNER JOIN bs_services bs ON bsds.idService  = bs.id
                WHERE bsds.idService='{$id}'";
    }
    $res = $mysqli->query($sql);
    $row = $res->fetch_assoc();
    $row['type'] = $serviceType;
    if ($field == null) {
        return $row;
    } else {

        return $row[$field];
    }
}
function getInterval($serviceID = 1)
{
    global $mysqli;
    $q = "SELECT `interval` FROM bs_service_settings WHERE serviceId ='{$serviceID}'";
    $res = $mysqli->query($q);
    $rr = $res->fetch_assoc(); //print $rr["interval"];
    return $rr["interval"];
}
function getEventsByDate($datetocheck, $serviceID = null)
{
    global $mysqli;
    $where = "";
    if (!empty($serviceID)) {
        $where = " AND serviceID='{$serviceID}'";
    }
    $query = "SELECT * FROM bs_events WHERE eventDate <= '" . $datetocheck . " 23:59' AND recurringEndDate>={$datetocheck} $where AND recurring=1 ORDER BY eventDate ASC ";
    $result = $mysqli->query($query);
    $events = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $startDate = date("Y-m-d", strtotime($row['eventDate']));
            $startTime = date("H:i", strtotime($row['eventDate']));
            $endDate = date("Y-m-d", strtotime($row['eventDateEnd']));
            $endTime = date("H:i", strtotime($row['eventDateEnd']));
            $interval = strtotime($row['eventDateEnd']) - strtotime($row['eventDate']);
            $st = $startDate;
            $j = 0;

            for ($i = $st; $i <= $row['recurringEndDate'] . " 23:59:59"; $i = date("Y-m-d", strtotime($i . " +{$row['repeate_interval']} {$row['repeate']}"))) {
                //print $i;
                $reserveDateFrom = $i;
                $reserveDateTo = date("Y-m-d", strtotime("$i +$interval seconds"));


                if (strtotime($datetocheck) <= strtotime($reserveDateTo) && strtotime($datetocheck) >= strtotime($reserveDateFrom)) {
                    $row['eventDate'] = "$reserveDateFrom $startTime";
                    $row['eventDateEnd'] = "$reserveDateTo $endTime";
                    $events[] = array("event" => $row, "qty" => getSpotsLeftForEvent($row['id'], $reserveDateFrom));
                }

                //$i=$b;
                $j++;
                if ($j > 1000) {
                    $message = "error to match iterations 'function getEventsByDate 
                         from=$datetocheck
                         serviceID=$serviceID'
                         startDate=$startDate";
                    _error_log($message);
                    break;
                }
            }
        }
    }
    $query = "SELECT * FROM bs_events WHERE eventDate <= '" . $datetocheck . " 23:59' AND eventDateEnd >= '" . $datetocheck . " 00:00' $where  AND recurring=0 ORDER BY eventDate ASC ";
    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $events[] = array("event" => $row, "qty" => getSpotsLeftForEvent($row['id']));
        }
    }
    return $events;
}
function checkForEvents($from, $to, $serviceID)
{

    $date = date("Y-m-d", strtotime($from));
    $eventsList = getEventsByDate($date, $serviceID);
    $to = strtotime($to . ":00");
    $from = strtotime($from . ":00");
    //print "$from $to<br>";
    //bw_dump($event);
    if (count($eventsList) > 0) {
        foreach ($eventsList as $event) {

            $event['eventDate'] = strtotime($event['event']['eventDate']);
            $event['eventDateEnd'] = strtotime($event['event']['eventDateEnd']);

            if (($event['eventDate'] < $to and $event['eventDateEnd'] >= $to) or ($event['eventDateEnd'] > $from and $event['eventDate'] <= $from) or ($event['eventDate'] <= $from and $event['eventDateEnd'] >= $to) or ($event['eventDate'] >= $from and $event['eventDateEnd'] <= $to)
            ) {
                return true;
            }
        }
    }
    return false;
}
function checkForAdminReserv($from, $to, $serviceID)
{
    global $mysqli;
    //print $from." - ".$to."<br>";
    $qty = 0;
    $qtyTmp = 0;
    $recurring = array();
    $date = date("Y-m-d", strtotime($from)); //print $date;
    $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=1 AND reserveDateTo>='{$to}'"; //print $sSQL;
    $res = $mysqli->query($sSQL);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {

            $startDate = date("Y-m-d", strtotime($row['reserveDateFrom']));
            $endDate = date("Y-m-d", strtotime($row['reserveDateTo']));
            $startTime = date("H:i", strtotime($row['reserveDateFrom']));
            $endTime = date("H:i", strtotime($row['reserveDateTo']));
            $st = $startDate;
            $en = $endDate;
            $j = 0;
            for ($i = $st; $i <= $date . " 23:59:59"; $i = date("Y-m-d", strtotime($i . " +{$row['repeate_interval']} {$row['repeate']}"))) {
                //print $i;
                $reserveDateFrom = $date . " " . $startTime;
                $reserveDateTo = $date . " " . $endTime;
                if ($date == date("Y-m-d", strtotime($i))) {

                    if (($reserveDateFrom < $to and $reserveDateTo >= $to) or ($reserveDateTo > $from and $reserveDateFrom <= $from) or ($reserveDateFrom <= $from and $reserveDateTo >= $to) or ($reserveDateFrom > $from and $reserveDateTo <= $to)
                    ) {
                        $recurring[$row['qty']] = array("start" => $reserveDateFrom, "end" => $reserveDateTo);
                        $qtyTmp += intval($row['qty']);
                    }
                }

                //$i=$b;
                $j++;
                if ($j > 1000) {
                    $message = "error to match iterations 'function checkForAdminReserv 
                         from=$from
                         to=$to
                         serviceID=$serviceID'";
                    _error_log($message);
                    break;
                }
            }
        }
    }
    //bw_dump($recurring);
    //print $qtyTmp."-";
    $qty = $qtyTmp;
    $sSQL = "SELECT * FROM bs_reserved_time WHERE serviceID='{$serviceID}' AND recurring=0 AND(
				(reserveDateFrom < '{$to}' AND reserveDateTo >= '{$to}') OR
				(reserveDateTo > '{$from}' AND reserveDateFrom <= '{$from}') OR
				(reserveDateFrom <= '{$from}' AND reserveDateTo >= '{$to}') OR
				(reserveDateFrom >= '{$from}' AND reserveDateTo <= '{$to}'))";
    //print $sSQL;
    $res = $mysqli->query($sSQL);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $qty += $row['qty'];
        }
    } else {
        //return false;
    }

    return $qty;
}
function checkForUserReserv($from, $to, $serviceID)
{
    global $mysqli;
    $qty = 0;
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
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $qty += $row['qty'];
        }
        return $qty;
    } else {
        return false;
    }
}
function getScheduleService($idService, $date)
{
    global $mysqli;
    $availabilityArr = array();
    $events = array();
    $admins = array();
    $users = array();
    $int = getInterval($idService);

    $dayOfWeek = date("w", strtotime($date));
    $sql = "SELECT * FROM bs_schedule
			WHERE idService='{$idService}' AND week_num='{$dayOfWeek}' ORDER BY startTime ASC"; //print $sql;
    $res = $mysqli->query($sql) or die($mysqli->error() . "<br>" . $sql);
    $n = 0;
    while ($row = $res->fetch_assoc()) {
        //$schedule[]=array("start"=>$row['startTime'],"end"=>$row['endTime']);

        $st = date("Y-m-d H:i", strtotime($date . " +" . $row['startTime'] . " minutes"));
        //TODO 
        //for afternight bookings
        //$row['endTime'] = ($row['startTime']<$row['endTime'])?$row['endTime']+720:$row['endTime'];
        $et = date("Y-m-d H:i", strtotime($date . " +" . $row['endTime'] . " minutes"));
        $a = $st;

        //layout counter
        $b = date("Y-m-d H:i", strtotime($a . " +" . $int . " minutes")); //default value for B is start time.
        $j = 0;
        for ($a = $st; $b <= $et; $b = date("Y-m-d H:i", strtotime($a . " +" . $int . " minutes"))) {
            //echo "a: ".$a." // "."b: ".$b."<br />";
            if (checkForEvents($a, $b, $idService)) {
                $events[date("Y-m-d", strtotime($a))][] = date("H:i", strtotime($a));
            }
            $qtyAdminReservation = checkForAdminReserv($a, $b, $idService); //print "<br>".$qtyAdminReservation."<br>";
            if ($qtyAdminReservation > 0) {
                $admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] = isset($admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]) ? $admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] += $qtyAdminReservation : $admins[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] += $qtyAdminReservation;
            }
            $qtyUserReservation = checkForUserReserv($a, $b, $idService);
            if ($qtyUserReservation !== false) {
                //$users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] = $qtyUserReservation;
                $users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] = isset($users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))]) ? $users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] += $qtyUserReservation : $users[date("Y-m-d", strtotime($a))][date("H:i", strtotime($a))] += $qtyUserReservation;
            }
            $availabilityArr[date("Y-m-d", strtotime($a))][] = date("H:i", strtotime($a));
            $a = $b;
            $n++;
            $j++;
            if ($j > 1000) {
                $message = "error to match iterations 'function getScheduleService 
                         date=$date
                         idService=$idService'
                         st=$st";
                _error_log($message);
                break;
            }
        }
    }
    return array("availability" => $availabilityArr, "events" => $events, "admins" => $admins, "users" => $users, "countItems" => $n);
}
function getTimes($date, $serviceID = 1)
{
    $serviceSettings = getServiceSettings($serviceID);
    $show_multiple_spaces = $serviceSettings['show_multiple_spaces']; //check option for multiple timeBooking
    $availebleSpaces = $show_multiple_spaces ? $serviceSettings['spaces_available'] : 1;
    $availability = [];
    $schedule = getScheduleService($serviceID, $date);
    $availabilityArr = $schedule['availability'];
    $admins = $schedule['admins'];
    $users = $schedule['users'];
    foreach ($availabilityArr as $k => $v) {
        foreach ($v as $kk => $vv) {
            if ((isset($admins[$k]) && array_key_exists($vv, $admins[$k]))) {
                $spacesBookedUser = isset($users[$k][$vv]) ? $users[$k][$vv] : 0;
                $spacesBooked = $admins[$k][$vv];
                $spacesAllowed = $availebleSpaces - $spacesBooked - $spacesBookedUser;
                if ($spacesAllowed >= 1) {
                    $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                    array_push($availability, $msm);
                }
            } elseif ((isset($users[$k]) && array_key_exists($vv, $users[$k]))) {
                $spacesBooked = $users[$k][$vv];
                $spacesAllowed = $availebleSpaces - $spacesBooked;
                if ($spacesAllowed >= 1) {
                    $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                    array_push($availability, $msm);
                }
            } else {
                $msm = ((int) substr($vv, 0, 2)) * 60 + ((int) substr($vv, -2)); //minutes since miodnight of current day.
                array_push($availability, $msm);
            }
        }
    };
    return $availability;
}
