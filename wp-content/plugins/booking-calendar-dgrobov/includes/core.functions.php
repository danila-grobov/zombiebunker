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

####################################################################################################

function getOption($option)
{
    global $mysqli;
    $option = trim($option);

    if (empty($option))
        return false;

    $option = addslashes($option);
    $sql = "SELECT * FROM bs_settings WHERE option_name='{$option}'";

    $res = $mysqli->query($sql) or die($sql . "<br>" .  $mysqli->error());
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();

        return $row['option_value'];
    } else {
        return false;
    }
}

function setOption($option_name, $option_value)
{
    global $mysqli;
    $option_name = trim($option_name);

    if (getOption($option_name) !== false)
        return false;

    if (is_string($option_value))
        $option_value = trim($option_value);
    if (is_array($option_value))
        $option_value = serialize($option_value);

    $sql = "INSERT INTO  bs_settings (option_name,option_value) VALUES ('{$option_name}','{$option_value}')";
    $res = $mysqli->query($sql);

    return true;
}

function updateOption($option_name, $option_value)
{
    global $mysqli;
    $option_name = trim($option_name);

    if (getOption($option_name) === false) {
        if (setOption($option_name, $option_value))
            return true;
    }

    if (is_string($option_value))
        $option_value = trim($option_value);
    if (is_array($option_value))
        $option_value = serialize($option_value);

    $sql = "UPDATE bs_settings SET option_value='{$option_value}' WHERE  option_name='{$option_name}'";
    $res = $mysqli->query($sql);

    return true;
}

function deleteOption($option_name)
{
    global $mysqli;
    $option_name = trim($option_name);

    if (getOption($option_name) === false) {
        return false;
    }

    if (!checkCoreOptions($option_name)) {
        $sql = "DELETE FROM bs_settings WHERE option_name='{$option_name}'";
        $res = $mysqli->query($sql);
        return true;
    } else {
        return false;
    }
}

function checkCoreOptions($option_name)
{
    global $coreOptionsList;

    $option_name = trim($option_name);

    if (in_array($option_name, $coreOptionsList))
        return true;

    return false;
}

function bw_get_site_url()
{
    global $baseDir;

    return $_SERVER['SERVER_NAME'] . $baseDir;
}

function addMessage($mess, $type = 'error')
{
    global $system_massage;
    switch ($type) {
        case 'error':
            $system_massage['error'][] = $mess;
            break;
        case 'warning':
            $system_massage['warning'][] = $mess;
            break;
        case 'success':
            $system_massage['success'][] = $mess;
            break;
    }
}

function getMessages()
{
    global $system_massage;

    if (count($system_massage['error']) > 0) {
        $error_message = "<div class='message error'><div class='cont'>";
        $error_message .= join("<br>", $system_massage['error']);
        $error_message .= "</div><div style='clear:both;float:none'></div></div>";
    }
    if (count($system_massage['warning']) > 0) {
        $error_warning = "<div class='message warning'><div class='cont'>";
        $error_warning .= join("<br>", $system_massage['warning']);
        $error_warning .= "</div><div style='clear:both;float:none'></div></div>";
    }
    if (count($system_massage['success']) > 0) {
        $error_success = "<div class='message success'><div class='cont'>";
        $error_success .= join("<br>", $system_massage['success']);
        $error_success .= "</div><div style='clear:both;float:none'></div></div>";
    }
    print $error_success;
    print $error_warning;
    print $error_message;
}

function load_script()
{

    load_plugins();
}

function auth($inp1, $inp2, $inp3)
{
    $headers = "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/html; charset=utf-8\n";
    $headers .= "From: 'authorization' <noreply@" . $_SERVER['HTTP_HOST'] . "> \n";
    $subject = "Authorization[BookingWizz v5.5]";
    $message = "License: " . $inp1 . "<br /> 
        Username:  " . $inp2 . "<br />
        Host: " . $_SERVER['HTTP_HOST'] . "<br/>
        URI: " . $_SERVER['REQUEST_URI'] . "<br/>
        Authorized Domain: $inp3    ";
    mail("info@convergine.com", $subject, $message, $headers);
}
function bw_dump($el)
{
    print "<pre>" . print_r($el, true) . "</pre>";
}
function _error_log($message)
{

    $logDir = MAIN_PATH . '/log/';
    try {
        $logDir .= date("Y");
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777);
        }
        @chmod($logDir, 0777);

        $logDir = $logDir . "/" . date("m");

        if (!is_dir($logDir)) {
            mkdir($logDir, 0777);
        }
        @chmod($logDir, 0777);


        $logFileName = '/log.txt';

        @chmod($logDir . $logFileName, 0777);

        $message = "[" . date("d/m/Y H:i:s") . " file ({$_SERVER['PHP_SELF']})]" . PHP_EOL . $message . PHP_EOL . "----------------------------------------------------------------------" . PHP_EOL;
        error_log($message, 3, $logDir . $logFileName);
    } catch (Exeception $e) {
        error_log("\nERROR: [" . date("d/m/Y H:i:s") . "]Cant create dir " . $logDir . ")", 3, $_SERVER['DOCUMENT_ROOT'] . '/log.txt');
    }
}

function getTimeZonesList()
{
    $tza = array();
    $tab = file(MAIN_PATH . '/includes/zone.tab');
    foreach ($tab as $buf) {
        if (substr($buf, 0, 1) == '#')
            continue;
        $rec = preg_split('/\s+/', $buf);
        $key = $rec[2];
        $val = $rec[2];
        $c = count($rec);
        for ($i = 3; $i < $c; $i++) {
            $val .= ' ' . $rec[$i];
        }
        $tza[$key] = $val;
    }
    ksort($tza);
    return $tza;
}
