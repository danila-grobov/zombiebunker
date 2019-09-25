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


    ######################### DO NOT MODIFY (UNLESS SURE) ########################
    require_once("../includes/config.php"); //Load the configurations
    
    $show = isset($_REQUEST['show'])?$_REQUEST['show']:"";
   
    if($show=='false'){
        
        updateOption('show_home_popup', 0);
    }
    
    ?>
