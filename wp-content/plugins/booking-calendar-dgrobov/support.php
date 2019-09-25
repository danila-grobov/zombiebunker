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
    require_once("includes/config.php"); //Load the configurations
    $msg = "";

    if ($_SESSION["logged_in"] != true) {
        header("Location: admin.php");
        exit();
    } else {
        bw_do_action("bw_load");
        bw_do_action("bw_admin");

        $page = isset($_REQUEST['page'])?$_REQUEST['page']:"index";
        $subpage = isset($_REQUEST['subpage'])?$_REQUEST['subpage']:"";

        $page = str_replace(" ","",$page);
        $page = str_replace("'","",$page);
        $page = str_replace('"','',$page);
        $page = str_replace('..','',$page);
        $page = str_replace('.','',$page);
        $page = str_replace('/','',$page);
        $page = str_replace('\\','',$page);

        $subpage = str_replace(" ","",$subpage);
        $subpage = str_replace("'","",$subpage);
        $subpage = str_replace('"','',$subpage);
        $subpage = str_replace('..','',$subpage);
        $subpage = str_replace('.','',$subpage);
        $subpage = str_replace('/','',$subpage);
        $subpage = str_replace('\\','',$subpage);

        $postdata = http_build_query(
            array(
                'page' => $page,
                'subpage' => $subpage
            )
        );
        $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
        );
        $context  = stream_context_create($opts);
        $result = @file_get_contents('http://www.convergine.com/support/bookingwizz/'.$page.'.php', false, $context);


        include "includes/admin_header.php";
?>
<link type="text/css" href="./js/datatable/css/jquery.dataTables.css" rel="stylesheet" />
     <div id="content">

        <?php if(!empty($result)){ echo $result; } else { echo SUPPORT_FAILED_TO_LOAD; } ?>

     </div>

<?php  include "includes/admin_footer.php";
    }
?>