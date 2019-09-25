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
function getDefaultService()
{
    global $mysqli;
    $sql = "SELECT * FROM bs_services WHERE `default`='y'";
    $res = $mysqli->query($sql);
    if (!is_bool($res)) {
        if ($res->num_rows) {
            $res = $res->fetch_assoc();
        } else {

            $sql = "SELECT * FROM bs_services ORDER BY id ASC LIMIT 1";
            $res = $mysqli->query($sql);
            $res = $res->fetch_assoc();
        }
    } else {

        $sql = "SELECT * FROM bs_services ORDER BY id ASC LIMIT 1";
        $res = $mysqli->query($sql);
        $res = $res->fetch_assoc();
    }
    return $res['id'];
}
define("MAIN_PATH", dirname(dirname(__FILE__))); //main path of BookingWizz directory
require_once(MAIN_PATH . "/includes/dbconnect.php"); //Load the db connect
$serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();
$couponCode = (!empty($_REQUEST["couponCode"])) ? strip_tags(str_replace("'", "`", $_REQUEST["couponCode"])) : "";

$responce = array();

$sql = "SELECT * FROM bs_coupons WHERE code='{$couponCode}'";
$res = $mysqli->query($sql);
if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    if ($row['dateFrom'] <= date("Y-m-d") && $row['dateTo'] >= date("Y-m-d")) {
        $services = explode(",", $row['services']);
        if (in_array($serviceID, $services)) {
            $responce = array("responce" => true, "value" => $row['value'], "type" => $row['type']);
        } else {
            $responce = array("responce" => false, "message" => "This coupon not accepted fo this service");
        }
    } else {
        $responce = array("responce" => false, "message" => "This coupon out of date");
    }
} else {
    $responce = array("responce" => false, "message" => "Coupon not found");
}

print json_encode($responce);
