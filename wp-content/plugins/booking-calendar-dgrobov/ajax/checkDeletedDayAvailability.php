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
require_once("../includes/config.php"); //Load the configurationsa

$response = array("res"=>true,"mess"=>"");
$message = "Please note, following confirmed or paid reservations will be removed:\n\n";
$result = true;
$year = date("Y");

$id = (!empty($_REQUEST["ids"])) ? $_REQUEST["ids"] : "";
$idService = (!empty($_REQUEST["id"])) ? $_REQUEST["id"] : "";

$service = getService($idService);

$sql = "SELECT * FROM bs_schedule_days WHERE idItem='{$id}'";
$res = $mysqli->query($sql);
$schedule = $res->fetch_assoc();

$scheduleFrom =  str_replace('2000', $year, $schedule['dateFrom']);
$scheduleTo =  str_replace('2000', $year, $schedule['dateTo']);

$sql = "SELECT bs.id,bs.name,bs.dateCreated FROM bs_reservations_items br
        INNER JOIN bs_reservations bs ON bs.id=br.reservationID
        WHERE bs.serviceID='{$idService}' AND br.reserveDateFrom >= '{$scheduleFrom}' AND br.reserveDateTo <='{$scheduleTo}' AND bs.status IN('1','4')";
$res = $mysqli->query($sql);
if($res->fetch_assoc()){

    if($res->num_rows){
        while($row = $res->fetch_assoc()){
            $message.="#{$row['id']} {$row['name']}  - ".getDateFormat($row['dateCreated'])."  ({$service['name']})\n";
            $result = false;
        }
    }
}

$response['res']=$result;
$response['mess']=$message;

echo json_encode($response);
