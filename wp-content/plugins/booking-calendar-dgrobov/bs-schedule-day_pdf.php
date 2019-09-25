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
 $dateTo = (!empty($_REQUEST["selectedDayTo"])) ? strip_tags(str_replace("'", "`", $_REQUEST["selectedDayTo"])) : date("Y-m-d",strtotime("+7 days"));
 $serviceID = (!empty($_REQUEST["serviceID"])) ? strip_tags(str_replace("'", "`", $_REQUEST["serviceID"])) : getDefaultService();;
 $serviceName = getService($serviceID,'name');
 
 class MYPDF extends TCPDF {
        public $BW_date;
        public $BW_dateTo;
        public $BW_service;
        
            
        
        public function Header() {
        

        $header ="<h3>".SCHEDL." for <span style=\"display:inline-block;background-color:#FFFF99\">". $this->BW_service."</span>
            <br/>from <span style=\"display:inline-block;background-color:#FFFF99\">". getDateFormat($this->BW_date)."</span>
                        to <span style=\"display:inline-block;background-color:#FFFF99\">". getDateFormat($this->BW_dateTo)."</span></h3><br/>";
        $this->writeHTMLCell(173,60 , 20, 5,$header ,'' , 1, 0, true, 'L', true);
        }
    }


$data=array();
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'P', true, 'UTF-8', false);

$pdf->BW_date=$date;
$pdf->BW_dateTo=$dateTo;
$pdf->BW_service=$serviceName;


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

    $i=0;
    for($a=$date;$a<=$dateTo;$a=date("Y-m-d",strtotime("$a +1 days"))){
        $text = "<table cellspacing=\"0\" cellpadding=\"4\" width=\"820\">";
        $bookings = checkSpotsForDayShedule($a,$serviceID);
        $events = getEventsByDate($a,$serviceID);
        $text .= "<tr style=\"".($bookings['type']=='notAvailable'?"background-color:#eee":"")."\">
        <td style=\"border:1px solid #ccc\" width=\"80\">" . getDateFormat($a) . "</td>
            <td style=\"border:1px solid #ccc\">

                {$bookings['message']}";

        if(count($events)>0){
            $text.="<br/><table border=\"0\" cellpadding=\"4\" >";
            foreach ($events as $event){
                //bw_dump($event); "
                $text.="<tr><td ><hr/>";
                $text.="<font color=\"#0000FF\">Event: {$event['event']['title']}</font><br/>";
                //$text.="<div>";
                if(_date($event['event']['eventDate'])!=_date($event['event']['eventDateEnd'])){
                    $text.="<span><label>Date From:</label>".getDateFormat($event['event']['eventDate'])." ".  _time($event['event']['eventDate'])."</span>";

                    $text.="<br/><span><label>Date To:</label>".getDateFormat($event['event']['eventDateEnd'])." ".  _time($event['event']['eventDateEnd'])."</span>";
                }else{
                    $text.="<span><label>Date:</label>".getDateFormat($event['event']['eventDateEnd'])." (".  _time($event['event']['eventDate'])." - ".  _time($event['event']['eventDateEnd']).")</span>";
                }

                //$text.="<span><label>Title:</label>{$event['event']['title']}</span>";
                $text.="</td></tr>";
            }
            $text.="</table>";
        }

        $text .="</td></tr>";

        if($i>100){
            $message = "error to match iterations 'function getScheduleTableDay
                         selectedDayFrom=$date
                         selectedDayTo=$dateTo
                         serviceID=$serviceID'";
            _error_log($message);
            break;

            }
        $i++;
        $text.="</table>";
        $pdf->writeHTMLCell('', '', '', '', $text, '', 1, 0, false, 'L', true);
    }
    
    //print $text;exit();
    ##########################################################################################################################

    //print  $availability;
   
    
    
    
    
    $pdf->Output('example_001.pdf', 'I');
?>
