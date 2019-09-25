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

$response = array("res" => true, "mess" => "");
$message = "Please note, following confirmed or paid reservations will be removed:\n\n";
$result = true;

$bsid = (!empty($_REQUEST["bsid"])) ? $_REQUEST["bsid"] : array();
$type = (!empty($_REQUEST["type"])) ? $_REQUEST["type"] : 'service';

$where = $type == "event" ? "eventID" : "serviceID";

foreach ($bsid as $id) {
    if ($type == 'service') {
        $service = getService($id);
        $name = $service['name'];
    } else {
        $event = getEventInfo($id);
        $name = $event['title'];
    }
    $sql = "SELECT * FROM bs_reservations WHERE {$where}='{$id}' AND status IN('1','4')";

    $res = $mysqli->query($sql);
    if ($res->num_rows) {
        while ($row = $res->fetch_assoc()) {
            $message .= "#{$row['id']} {$row['name']}  - " . getDateFormat($row['dateCreated']) . "  ({$name})\n";
            $result = false;
        }
    }
}


$response['res'] = $result;
$response['mess'] = $message;

echo json_encode($response);
