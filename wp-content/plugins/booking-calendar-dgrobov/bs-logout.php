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
    require_once("includes/dbconnect.php"); //Load the settings
	$msg="";
	
	if($_SESSION["logged_in"]!=true){ 
	header("Location: index.php");
	} else {
	$_SESSION['idUser']="";
	$_SESSION['username']= "";
	$_SESSION['accesslevel']= "";
	$_SESSION['logged_in'] = false;
	session_destroy();
	header("Location: index.php");
	}
?>