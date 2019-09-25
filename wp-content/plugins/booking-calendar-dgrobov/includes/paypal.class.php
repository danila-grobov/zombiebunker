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

class paypal_class {

   var $response;                
   var $pp_data = array(); 
   var $fields = array();           
 
 
 
 
   function paypal_class() {   
      // constructor.  
      $this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
      //$this->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
      $this->response = '';
      $this->add_field('rm','2');           
      $this->add_field('cmd','_xclick');    
   }
   
   
   
   
   
   function add_field($field, $value) {
	   //form field pair creator
      $this->fields["$field"] = $value;
   }


   function validate_ipn() {
      // parse the paypal URL
      $url_parsed=parse_url($this->paypal_url);        
	  
      $post_string = '';    
      foreach ($_POST as $field=>$value) { 
         $this->pp_data["$field"] = $value;
         $post_string .= $field.'='.urlencode(stripslashes($value)).'&'; 
      }
      $post_string.="cmd=_notify-validate"; 
      // open the connection to paypal
      $fp = fsockopen($url_parsed[host],"80",$err_num,$err_str,30); 
      if(!$fp) {
         return false;
      } else { 
         // Post the data back to paypal
         fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n"); 
         fputs($fp, "Host: $url_parsed[host]\r\n"); 
         fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
         fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
         fputs($fp, "Connection: close\r\n\r\n"); 
         fputs($fp, $post_string . "\r\n\r\n"); 
         // loop through the response from the server and append to variable
         while(!feof($fp)) { 
            $this->response .= fgets($fp, 1024); 
         } 
         fclose($fp); // close connection
      }
      if (preg_match("/VERIFIED/i",$this->response)) {
         // Valid IPN transaction.
         return true;          
      } else {
         // Invalid IPN transaction.
         return false;    
      } 
   }
   
}  //class end       
?>
