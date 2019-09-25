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

if (isset($_GET['q'])) {
	$required_cases = explode(',', $_GET['q']);
} else {
	$required_cases = array('all_events_signups', 'all_timed_booking_signups', 'all_multiday_booking_signups');
	#$required_cases = array('all_timed_booking_signups');
}
if (isset($_GET['calendar']) && is_numeric($_GET['calendar'])) {
	$serviceID = $_GET['calendar'];
} else {
	$serviceID = 2;
}
$json   = array();
foreach ($required_cases as $case) {
	$tojson = getArrayDataForJSON($case, $serviceID, $days);
	if (!empty($tojson)) {
		$json[] = $tojson;
	}
}

echo json_encode($json);


function getArrayDataForJSON($case, $serviceID, $days)
{
	global $mysqli;
	if (isset($_GET['month']) && is_numeric($_GET['month']) && $_GET['month'] <= 12) {
		$days = cal_days_in_month(CAL_GREGORIAN, $_GET['month'], date('Y'));
		$month = $_GET['month'];
	} else {
		$days = date('t');
		$month = date('m');
	}
	$DATA = array();
	$LINE = array();
	$yDATA = array();
	$xDATA  = array();
	$ttipX = array();
	$color = null;

	/* add more graph lines */
	switch ($case) {
		case 'all_events_signups':
			/* sql and series customization */
			$label = 'Events Signups';
			$color = '#c3c3c3';

			$SQL = " SELECT bs_reservations.id as ID, bs_reservations.dateCreated as X_AXIS, bs_reservations.name as NAME, bs_reservations.email as EMAIL FROM bs_reservations ";
			$SQL .= " WHERE bs_reservations.serviceID = {$serviceID} AND bs_reservations.eventID > 0 ORDER BY dateCreated ASC ";
			break;
		case 'all_timed_booking_signups':
			$label = 'Hourly Services Signups';
			$color = '#d80000';

			$SQL = " SELECT bs_reservations.id as ID, bs_reservations.dateCreated as X_AXIS, bs_reservations.name as NAME, bs_reservations.email as EMAIL FROM bs_reservations ";
			$SQL .= " LEFT JOIN bs_services ON bs_reservations.serviceID=bs_services.id WHERE bs_services.type='t'  AND bs_reservations.eventID IS NULL AND bs_reservations.serviceID = {$serviceID} ORDER BY dateCreated ASC ";
			break;
		case 'all_multiday_booking_signups':
			$label = 'Multi-Day Services Signups'; /* the length of description can be longer, it will fit */
			$color = '#0066cc';

			$SQL = " SELECT bs_reservations.id as ID, bs_reservations.dateCreated as X_AXIS, bs_reservations.name as NAME, bs_reservations.email as EMAIL FROM bs_reservations ";
			$SQL .= " LEFT JOIN bs_services ON bs_reservations.serviceID=bs_services.id WHERE bs_services.type='d' AND bs_reservations.eventID IS NULL AND bs_reservations.serviceID = {$serviceID}  ORDER BY dateCreated ASC ";																							/* AND bs_reservations.eventID < 1 */
			break;																																	   /* show only for this calendar ? */
	}

	$result = $mysqli->query($SQL) or die('SQL :' . $SQL . ' ;ERROR :' . $mysqli->error());

	if ($result->num_rows > 0) :
		/* if results loop trough them and store id for count and date for reference */
		while ($row = $result->fetch_assoc()) {
			if (isset($row['X_AXIS']) && isset($row['ID'])) {
				$DATA[$row['X_AXIS']] = array($row['ID'], $row['NAME'], $row['EMAIL']);
				/*         time    	    booking details   */
			}
		}
		/* loop and count */
		$dataWasHere = false;
		$ttip = 0;
		for ($i = 1; $i <= $days; $i++) {



			if (!isset($xDATA[$i][strtotime(date('Y-m') . "-$i")])) {
				/* build the x axis for whole month, with y = 0 if no data and y = null for data from today till the end of the month */
				$xyDATA[] = array((strtotime(date('Y-m') . "-$i") * 1000), $i > date('d') ? null : 0);
			}

			foreach ($DATA as $X => $Y) {
				if (substr($X, 8, 2) == $i && intval(substr($X, 5, 2)) == $month) { /* if the date exist's in result set! */
					/* construct plot data and tip */
					$ttip++;


					//* extra data can be inserted by adding new array for below and called in javascript item Object like this : */
					/*  item.series.data[item.dataIndex][toolTipIndexInDataArray].toolTip */
					$xyDATA[] = array((strtotime($X) * 1000), $ttip, array('toolTip' => $Y[1] . '(' . $Y[2] . ')' . '<br /> booked on ' . getDateFormat($X)));


					//* this is pointless and useless, but i can use it for grabbing another kind of data and use it yes i can */
					#$xyDATA[] = array((strtotime($X)*1000),$ttip) ;
					#$xTT = (strtotime($X)*1000)."_".$ttip;
					#$ttipX[$xTT]  = $Y[1].'('.$Y[2].')'.'<br /> booked on '.getDateFormat($X);

					$dataWasHere = true;
				}
			}
		}

		$LINE['label'] = $label;
		$LINE['data']  = $xyDATA;
		if ($color != null) {
			$LINE['color']  = $color;
		}
		$LINE['ttipX'] = $ttipX;

		if ($dataWasHere == true) {
			return $LINE;
		} else {
			return array();
		} else :
		/* if no results return empty for parsing */
		return array();
	endif;
}
/*  full series example. [[serie1],[serie2]]
 * {
    color: color or number
    data: rawdata
    label: string
    lines: specific lines options
    bars: specific bars options
    points: specific points options
    xaxis: number
    yaxis: number
    clickable: boolean
    hoverable: boolean
    shadowSize: number
    highlightColor: color or number
}
 * 
 * */
