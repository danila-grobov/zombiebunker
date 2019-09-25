<?php



//error_reporting(E_ALL);
include_once('iCalGenerator.php');
$iCal = new iCalGenerator();


$_name = getServiceSettings($serviceID,'fromName');
$_email = getServiceSettings($serviceID,'fromEmail');


$iCal->startDate =$eventInf['eventDate'];
$iCal->endDate =$eventInf['eventDateEnd'];
$iCal->name = $eventInf['title'];
$iCal->location = $eventInf['location'];
$iCal->description = $eventInf['description'];
$iCal->mailFrom = !empty($_name)?$_name:"Admin";
$iCal->mailTo = !empty($_email)?$_email:getAdminMail();
$iCal->url = $eventURL;

$iCal->addEvent();

$file = $iCal->renderIcal(); // output file as ics
//print $file;
?>
