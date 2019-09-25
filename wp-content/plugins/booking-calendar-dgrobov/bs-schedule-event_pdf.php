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

require_once('./includes/config.php');
require_once('./tcpdf/config/lang/eng.php');
require_once('./tcpdf/tcpdf.php');
global $date;
global $dateTo;
global $serviceID;

$date = (!empty($_REQUEST["selectedDay"])) ? strip_tags(str_replace("'", "`", $_REQUEST["selectedDay"])) : date("Y-m-d");
$serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();;

class MYPDF extends TCPDF {
    public $BW_date;
    public $BW_dateTo;
    public $BW_service;



    public function Header() {


        $header ="<h3>".SCHEDL." ".EVENTS_LIST_TITLE." <span style=\"display:inline-block;background-color:#FFFF99\">". getDateFormat($this->BW_date)."</span></h3><br/>";
        $this->writeHTMLCell(173,60 , 20, 5,$header ,'' , 1, 0, true, 'L', true);
    }
}


$data=array();
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'P', true, 'UTF-8', false);

$pdf->BW_date=$date;



// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('Schedule for '.$serviceName);
$pdf->SetSubject('Schedule for '.$serviceName);
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

$pdf->setPrintHeader(true);
$pdf->setPrintFooter(false);
$pdf->setHeaderMargin(0);


// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetMargins(10, 20, 10,true);
/*
//set margins
$pdf->SetMargins(5, 5, 5);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(5);*/

//set auto page breaks
$pdf->SetAutoPageBreak(true, 10);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 12);
$pdf->AddPage('P', 'A4',true);
// add a page
$style = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '0', 'phase' => 10, 'color' => array(100, 100, 100));

$pdf->SetLineStyle( $style);
$pdf->SetCellPadding(0);


global $baseDir;

$availability = "";
$availability .= "<table  border=\"1\" class=\"dataTable schedule\" align=\"left\" cellpadding=\"3\" cellspacing=\"0\">";
$availability .= "<thead>
                            <tr style=\"background-color:#ccc\">
                            <th>Event</th>
                            <th width=\"15%\">Spots</th>
                            <th>Time From</th>
                            <th>Time To</th>
                            <th>Entry Fee</th>

                            </tr></thead>";

$eventsList = getEventsByDate($date,$serviceID);
//bw_dump($eventsList);
$i=0;
foreach($eventsList as $event){
    $i=$i?0:1;
    $class=$i?"odd":"even";
    $_event = $event['event'];
    $timeFrom =getDateFormat($_event['eventDate'])." ". _time($_event['eventDate']);
    $timeTo= getDateFormat($_event['eventDateEnd'])." ". _time($_event['eventDateEnd']);
    $spaces = $event['qty']." out of ".$_event['spaces'];
    $fee = $_event['entryFee']>0?getCurrencyText($_event['entryFee']):"Free";

    $hasBookings = $_event['spaces']!=$event['qty']?true:false;
    if($hasBookings){
        $spaces = "<b style=\"color: #0000aa\">".$event['qty']."</b> out of ".$_event['spaces'];
    }
    $availability.="
        <tr class=\"{$class}\">
            <td>{$_event['title']}</td>
            <td width=\"15%\"><span class=\"space\">{$spaces}</span></td>
            <td>{$timeFrom}</td>
            <td>{$timeTo}</td>
            <td>{$fee}</td>


        </tr>
        ";
    if($hasBookings){

        $availability.="
                            <tr>
                                <td style=\"border-width: 0px\">&nbsp;</td>
                                <td style=\"background-color: #eee;color: #0000aa\">Spots</td>
                                <td style=\"background-color: #eee;color: #0000aa\">Customer Name</td>
                                <td style=\"background-color: #eee;color: #0000aa\">Customer Phone</td>
                                <td style=\"background-color: #eee;color: #0000aa\">Customer Email</td>
                            </tr>
                            ";
        $sql = "SELECT * FROM bs_reservations WHERE eventID='{$_event['id']}' AND date = '{$date}' AND status IN ('1','4')";
        $res = $mysqli->query($sql);
        $j=0;$_class = '';
        while($row = $res->fetch_assoc()){
            //bw_dump($row);
            if($j==0){$j=1;$_class="odd";}else{$j=0;$_class="even";}
            $availability.="<tr class=\"{$_class} bookings\" data-row=\"{$_event['id']}\">
                                <td>&nbsp;</td>
                                <td>{$row['qty']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['phone']}</td>
                                <td>{$row['email']}</td>
                                </tr>";
        }
        //$availability.="</table></td></tr>";
    }
}
$availability .="</table>";

    $text.="</table>";
    $pdf->writeHTMLCell('', '', '', '', $availability, '', 1, 0, false, 'L', true);


//print $text;exit();
##########################################################################################################################

//print  $availability;





$pdf->Output('example_001.pdf', 'I');
?>
