<?php
require_once("dbconnect.php");

$result = $mysqli->query("SELECT email FROM `bs_reservations_items`, bs_reservations WHERE bs_reservations_items.`reservationID`=bs_reservations.id AND (`reserveDateFrom`>CURDATE() - INTERVAL 2 DAY) AND (`reserveDateFrom`<CURDATE())");

while ($row = $result->fetch_assoc()) {
    echo $row["email"];
    echo "<br>";
    echo "issiustas <hr>";


$to      =  $row["email"];
$subject = 'Ats.: Kaip Jums Zombie Bunker?';
$message = 'Sveiki,
ačiū, kad lankėtės "ZombieBunker". Esu Justina, vienas iš "ZombieBunker" įkūrėjų ir savininkių.
Noriu pasiteirauti, kaip Jums sekėsi? Kas patiko ir kas nepatiko? Ką galėtumėme pagerinti ateityje? Spauskite "reply" ir parašykite mums.

Būčiau labai dėkingas, jei paliktumėte atsiliepimą facebook sistemoje ir įvertinę mūsų darbą žvaigždutėmis, duotumėte puikų motyvacijos spyrį pramogų kūrybai. Nuoroda: https://www.facebook.com/zombiebunkerVilnius/reviews
Norite daugiau? Laukiame Jūsų čia: http://www.breakroom.lt/

Linkėjimai,
Justina

';

$headers = 'From: info@zombiebunker.lt' . "\r\n" .
    'Reply-To: info@zombiebunker.lt' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);














}





?>