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
    session_start();
    require_once("includes/dbconnect.php"); //Load the settings
    require_once("includes/config.php"); //Load the functions
    $msg = "";
    $page = (isset($_GET['p']))?urldecode($_GET['p']):"";

    if ($_SESSION["logged_in"] != true) {
        header("Location: admin.php");
        exit();
    } else {

        bw_do_action("bw_load");
        bw_do_action("bw_admin");

        include "includes/admin_header.php";
        
        
?>
    <div id="content">
        
        
        <div class="content_block">
            
            <?php if(get_admin_page($page)){
                
            }else{?>
                Error
            <?php } ?>
        </div>
    </div>

<?php
        include "includes/admin_footer.php";
    }
?>