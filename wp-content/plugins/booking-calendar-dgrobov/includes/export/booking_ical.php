<?php

//error_reporting(E_ALL);
include_once('iCalGenerator.php');
$iCal = new iCalGenerator();


$_name = getServiceSettings($serviceID,'fromName');
$_email = getServiceSettings($serviceID,'fromEmail');

foreach($bookingData as $booking_data){

    $iCal->startDate =$booking_data['dateFrom'];
    $iCal->endDate =$booking_data['dateTo'];
    $iCal->name = "$serviceName | {$booking_data['dateFrom']}";
    $iCal->location = "";
    $iCal->description = "$serviceName | From :{$booking_data['dateFrom']} to {$booking_data['dateTo']}";
    $iCal->mailFrom = !empty($_name)?$_name:"Admin";
    $iCal->mailTo = !empty($_email)?$_email:getAdminMail();
    $iCal->url = $eventURL;
    $iCal->addEvent();
}

$ical_file = $iCal->renderIcal(); // output file as ics (xcs and rdf possible)
//print $file;
?>
