<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// require_once("../includes/config.php");
require_once("./functions.php");
$data = json_decode(file_get_contents("php://input"));
$date = new DateTime("$data->date");
$month = (int) $date->format('m');
$year = (int) $date->format('Y');
$serviceID = $data->serviceId;
$days_num = cal_days_in_month(0, $month, $year);
$serviceSettings = getServiceSettings($serviceID);
$days = [];
$now = new DateTime();
$now = $now->format('Y-m-d');
for ($day = 1; $day <= $days_num; $day++) {
    $date = new DateTime("$year-$month-$day");
    $date = $date->format('Y-m-d');
    if ($date > $now) {
        $times = getTimes($date, 2);
    } else {
        $times = [];
    }
    $currency = getOption('currency');
    $duration = (int) $serviceSettings["interval"];
    $price = (int) $serviceSettings["spot_price"];
    $days["$date"] = ["times" => $times, "currency" => $currency, "duration" => $duration, "price" => $price];
}
echo json_encode($days);
