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
session_start();
if (!isset($_SESSION['logged_in'])) {
	die('no no no..');
}
header('Content-type: application/json'); /* comment for debug html */
include('../includes/config.php');


/* This must be set on all pages */
$service = isset($_GET['service']) && is_numeric($_GET['service']) ? $_GET['service']  : null;
$dateFrom = isset($_GET['dateFrom'])   ? date('Y-m-d', strtotime($_GET['dateFrom'])) : date('Y-m-d', strtotime(" today "));
$dateTo = isset($_GET['dateTo']) 	   ? date('Y-m-d', strtotime($_GET['dateTo'])) : date('Y-m-d', strtotime(" yesterday "));


####################################### Events JSON Start ##########################
if (isset($_GET['c']) && $_GET['c'] == "events") :
	/* helpers */
	function sanitizeArrForSQL($stringWithQuoutes)
	{
		$arr = explode(',', $stringWithQuoutes);
		$newArr = array();
		foreach ($arr as $k => $v) {
			if (is_numeric($v)) {
				$newArr[] = $v;
			}
		}
		return implode(',', $newArr);
	}
	function getAllEvents()
	{
		global $mysqli;
		$select_events = " SELECT id, title FROM bs_events";
		$select_result = $mysqli->query($select_events);
		$events = array();
		while ($row = $select_result->fetch_assoc()) {
			$events[] = $row['id'];
		}
		$events = implode(',', $events);

		return $events;
	}

	/* get event's id's */
	$eventsIDs = isset($_GET['eventsIDs']) ? sanitizeArrForSQL($_GET['eventsIDs'])  : getAllEvents();

	/* Select all events bookings by service, event id, and date from -> date to */

	$SQL = " SELECT bs_reservations.id, bs_reservations.name, bs_reservations.qty, bs_reservations.eventID, bs_events.title, bs_reservations.dateCreated FROM bs_reservations ";
	$SQL .= " LEFT JOIN bs_events ON bs_events.id=bs_reservations.eventID WHERE  bs_reservations.serviceID = {$service} AND ";
	$SQL .= " DATE(bs_reservations.dateCreated) BETWEEN '{$dateFrom}' AND '{$dateTo}' ";
	$SQL .= !empty($eventsIDs) ? " AND bs_reservations.eventID IN ($eventsIDs) " : "";
	$SQL .= " ORDER BY bs_reservations.dateCreated ASC";

	$result = $mysqli->query($SQL) or die("SQL:<br />" . $SQL . "<br />ERROR:" . $mysqli->error());

	$JSON = array();

	while ($row = $result->fetch_assoc()) {
		$row['dateCreated'] = strtotime($row['dateCreated']) * 1000;
		$row['eventID'] = $row['eventID'] * 1;
		$JSON[] = $row;
	}
	echo json_encode($JSON);

endif;
####################################### Events JSON END ##########################

####################################### Appointments JSON Start ##########################
if (isset($_GET['c']) && $_GET['c'] == "appointments") :

	/* Select all reservation items where service type is d by service id, date from -> date to */

	$SQL = " SELECT bs_reservations.id, bs_reservations.name, bs_reservations.qty, bs_reservations.dateCreated FROM bs_reservations  ";
	$SQL .= " LEFT JOIN bs_services ON bs_services.id=bs_reservations.serviceID AND bs_services.type='t' ";
	$SQL .= " WHERE bs_reservations.serviceID = {$service} AND bs_reservations.eventID IS NULL AND DATE(bs_reservations.dateCreated) BETWEEN '{$dateFrom}' AND '{$dateTo}' ORDER BY bs_reservations.dateCreated ASC";


	$result = $mysqli->query($SQL) or die("SQL:<br />" . $SQL . "<br />ERROR:" . $mysqli->error());

	while ($row = $result->fetch_assoc()) {
		$row['dateCreated'] = strtotime($row['dateCreated']) * 1000;
		$JSON[] = $row;
	}
	echo json_encode($JSON);

endif;
####################################### Appointments JSON END ##########################

####################################### Multi Days JSON Start ##########################
if (isset($_GET['c']) && $_GET['c'] == "multi_days") :

	/* Select all reservations where service type is t, by service id , date from -> date to */

	$SQL = " SELECT bs_reservations.id, bs_reservations.name, bs_reservations.qty, bs_reservations.dateCreated FROM bs_reservations  ";
	$SQL .= " LEFT JOIN bs_services ON bs_services.id=bs_reservations.serviceID AND bs_services.type='d' ";
	$SQL .= " WHERE bs_reservations.serviceID = {$service} AND bs_reservations.eventID IS NULL AND DATE(bs_reservations.dateCreated) BETWEEN '{$dateFrom}' AND '{$dateTo}' ORDER BY bs_reservations.dateCreated ASC";


	$result = $mysqli->query($SQL) or die("SQL:<br />" . $SQL . "<br />ERROR:" . $mysqli->error());

	while ($row = $result->fetch_assoc()) {
		$row['dateCreated'] = strtotime($row['dateCreated']) * 1000;
		$JSON[] = $row;
	}
	echo json_encode($JSON);
endif;
####################################### Multi Days JSON END ##########################
