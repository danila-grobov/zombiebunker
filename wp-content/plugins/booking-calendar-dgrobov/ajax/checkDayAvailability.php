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
$dateFrom = (!empty($_REQUEST["dateFrom"])) ? strip_tags(str_replace("'", "`", $_REQUEST["dateFrom"])) : '';
$dateTo = (!empty($_REQUEST["dateTo"])) ? strip_tags(str_replace("'", "`", $_REQUEST["dateTo"])) : '';
$serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();;
$couponCode = (!empty($_REQUEST["couponCode"]))?strip_tags(str_replace("'","`",$_REQUEST["couponCode"])):'';

$availeble = _checkForAvailability($dateFrom, $dateTo, $serviceID);
if(!$availeble['res']){
    print json_encode(array("responce" =>$availeble['res'],"message"=>$availeble['message'] ));
    exit();
}

$booked = checkSpotsForDayInterval($dateFrom, $dateTo, $serviceID);
if($booked<1){
    print json_encode(array("responce" =>$booked,"message"=>"Some days of this day interval are booked" ));
    exit();
}

$price_old = 0;
$price = $availeble['totalPrice'];
if (!empty($couponCode)) {
    $couponData = checkCoupon($couponCode, $serviceID);
    if ($couponData['responce']) {
        $price_old = $price;
        $couponValue = $couponData['value'];
        $couponType = $couponData['type'];

        if ($couponType == 'abs') {
            $price = $price - $couponValue;

        } else {
            $price = $price * (1-$couponValue/100);

        }

    }
}





print json_encode(array("responce" =>true,"message"=>$availeble['message'],'price'=>$price,'price_old'=>$price_old,'qty'=>$availeble['intervals'] ));
?>
