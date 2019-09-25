<?php
$zinute = urlencode("Informuojame, kad Jusu registracija patvirtinta");
$sms = "http://smsplus1.routesms.com:8080/bulksms/bulksms?username=supersmslt&password=k7bd7y&type=0&dlr=1&destination=37067246854&source=BreakRoom&message=".$zinute;
$ch = curl_init($sms);
curl_exec($ch);

?>