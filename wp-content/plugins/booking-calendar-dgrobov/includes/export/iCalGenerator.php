<?php


class iCalGenerator {

public $prodId = "-//BookingWizz//iCalGenerator Class MIMEDIR//EN";
public $currDate = '';//current timestamp
public $startDate ='';
public $endDate ='';
public $name = "";
public $location = "";
public $description = "";
public $mailFrom = "";
public $mailTo = "";
public $url = "";
public $countEvents = 0;

public $eventsList = "";

    function __construct(){
        $this->currDate = date('Ymd').'T'.date('His');
    }

    function getDateFormat($date){

     return gmdate('Ymd\THi00\Z',strtotime($date));
    }

    public function getStartTime(){
        return $this->getDateFormat($this->startDate);
    }

    public function getEndTime(){
        return $this->getDateFormat($this->endDate);
    }

    public function escapeString($string) {
        return preg_replace('/([\,;])/','\\\$1', $string);
    }

    public function sanitizeStrings(){
        $this->description = $this->escapeString($this->description);
        $this->location = $this->escapeString($this->location);
        $this->name = $this->escapeString($this->name);
        $this->description = $this->escapeString($this->description);

    }

    public function addEvent(){
        $output = '';
        $output .= "BEGIN:VEVENT\r\n";
        $output .= "ORGANIZER;CN={$this->mailFrom}:MAILTO:{$this->mailTo}\r\n";
        $output .= "UID:".gmdate('Ymd\THi00\Z')."-".rand()."-BookingWizz\r\n"; // required by Outlok
        $output .= "DTSTAMP:".gmdate('Ymd\THis')."\r\n"; // required by Outlook
        $output .= "DTSTART:".$this->getStartTime()."\r\n";
        $output .= "DTEND:".$this->getEndTime()."\r\n";
        $output .= !empty($this->url)?"LOCATION;LANGUAGE=en;ENCODING=QUOTED-PRINTABLE:{$this->location}\r\n":"";
        $output .= "SUMMARY;LANGUAGE=en;ENCODING=QUOTED-PRINTABLE:{$this->name}\r\n";
        $output .= "PRIORITY:5\r\n";
        $output .= "CLASS:CONFIDENTIAL\r\n";
        $output .= !empty($this->url)?"URL:{$this->url}\r\n":"";
        $output .= "STATUS:CONFIRMED\r\n";
        $output .= "DESCRIPTION: {$this->description}\r\n";
        $output .= "END:VEVENT\r\n";

        $this->eventsList .=$output;
        $this->countEvents++;
    }

    public function renderIcal($type='string'){

        if($type=='file'){
            header('Content-Type: text/Calendar');
            header('Content-Disposition: attachment; filename=iCalendar_dates_' . date('Y-m-d_H-m-s') . '.ics');
        }
        $output = '';
        $output .= "BEGIN:VCALENDAR\r\n";
        $output .= "VERSION:2.0\r\n";
        $output .= "PRODID:{$this->prodId}\r\n";
        $output .= "METHOD:REQUEST\r\n"; // requied by Outlook
        $output .= $this->eventsList;
        $output .= "END:VCALENDAR\r\n";
        if($type=='file'){
            print $output;
        }else{
            return $output;
        }
    }


} 