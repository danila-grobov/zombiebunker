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




if ($_SESSION["logged_in"] != true) {
    header("Location: admin.php");
} else {

    include "includes/plugins_grid.php";


    //bw_dump($BW_actions);
    ######################### DO NOT MODIFY (UNLESS SURE) END ########################

   
    $lang = (!empty($_REQUEST["lang"])) ? strip_tags(str_replace("'", "`", $_REQUEST["lang"])) : 'english';

    $langList = getLangList();


//print $months;

    ?>
    <?php include "includes/admin_header.php"; ?>


    <div id="content">
        


         

<?php  getMessages(); ?>
        <div class="content_block">
            <h2><?php echo ADDONS_TITLE?></h2>
           <p><?php echo BS_ADDONS_DESCR ?></p>
            <hr/>
            <div class="form-row addons">
                <?php echo $plugin_list; ?>
                     <?php/*
                     $pluginsMenu = unserialize(getOption("custom_menu"));
               // print_r($pluginsMenu);
                if(is_array($pluginsMenu)){
                     foreach ($pluginsMenu as $key => $value) {
                            echo "<a href=\"".$value['menu_link']."\">".$value['menu_title']."</a>";
                        }
                 }
                     */?>
                 </div>

        </div>
        <?php include "includes/admin_footer.php"; ?>
    <?php } ?>