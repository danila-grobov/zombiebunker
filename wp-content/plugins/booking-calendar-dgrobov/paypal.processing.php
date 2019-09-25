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

$orderID = (!empty($_GET["orderID"]))?strip_tags(str_replace("'","`",$_GET["orderID"])):'';
$serviceID = (!empty($_POST["serviceID"]))?strip_tags(str_replace("'","`",$_POST["serviceID"])):'';
//print  $orderID;

 bw_do_action("bw_load");

 require_once("includes/header.php"); //Load the configurations  
 
 ?>
<div style="width: 600px;margin: 20px auto">
<?php
    echo payment_paypal("Redirecting to PayPal",$orderID,'pay');
    ?>
</div>
<script>
    $(function(){
        $('#paypal_form').submit();
    })
</script>
<?php include "includes/footer.php"?>