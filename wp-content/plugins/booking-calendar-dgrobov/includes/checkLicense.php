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

$item_name = "87919";

$envato_apikey = 'kvtkbq11x97wj9jmo8cum6drjsm4sw97';

$envato_username = "Convergine";

$license_to_check = preg_replace('/[^a-zA-Z0-9_ -]/s', '', !empty($license) ? $license : "");
$continue = false;

if (!empty($username) && !empty($domain)) {
    if (!empty($license_to_check) &&!empty($envato_apikey) &&!empty($envato_username) ) {

        //Initialize curl
        
        $api_url = 'http://marketplace.envato.com/api/edge/' . $envato_username . '/' . $envato_apikey . '/verify-purchase:' . $license_to_check . '.json';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_USERAGENT, "BookingWizz License Validator (during install)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $ch_data = curl_exec($ch);

        curl_close($ch);

        if (!empty($ch_data)) {

            $json_data = json_decode($ch_data, true);

            if (isset($json_data['verify-purchase']) && count($json_data['verify-purchase']) > 0) {

                //echo "License Type: " . $json_data['verify-purchase']['licence'] . "<br />";
                //echo "Item Name (ID): " . $json_data['verify-purchase']['item_name'] . "(".$json_data['verify-purchase']['item_id'].")<br />";
                //echo "Buyer Username: " . $json_data['verify-purchase']['buyer'] . "<br />";
                //echo "Purchase Date: " . $json_data['verify-purchase']['created_at'] . "<br />";
                $continue = true;

                if ($json_data['verify-purchase']['item_id'] != $item_name) {
                    $tt .= "<div class=error>Wrong product</div>";
                    $continue = false;
                }
                if ($json_data['verify-purchase']['buyer'] != $username) {

                    $tt .= "<div class=error>Wrong username</div>";
                    $continue = false;
                }

                //$continue = true;
            } else {

                //echo "Error fetching the info. Possible reason: license key invalid. Here's the curl return: ";
                $tt .= "<div class=error>Error fetching the info. Possible reason: license key invalid. Here's the curl return: </div>";
                print_r($json_data);
            }
        } else {

            //echo 'Something went terribly wrong!';
            $tt .= "<div class=error>Something went terribly wrong!</div>";
        }
    } else {

        //echo 'You either didn`t pass the license key into the url or didn`t enter your envato username/apikey into configuration';
        $tt .= "<div class=error>You either didn`t pass the license key into the url or didn`t enter your envato username/apikey into configuration</div>";
    }
} else {
  $tt .= "<div class=error>License Key, Username and Domain fields are required</div>";  
}
?>