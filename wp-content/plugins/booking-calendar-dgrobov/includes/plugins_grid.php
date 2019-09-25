<?php
ob_start();
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
$do = (isset($_GET['do']))?addslashes($_GET['do']):"";
$n = (isset($_GET['n']))?addslashes($_GET['n']):"";

if(!empty ($do) && $do == "activate"){
    if(bw_activate_plugin($n)) addMessage ("Plugin $n was successfully activated", "success");
}
if(!empty ($do) && $do == "deactivate"){
    if( bw_deactivate_plugin($n)) addMessage("Plugin $n was successfully deactivated", "success");
}

bw_do_action("bw_load");
bw_do_action("bw_admin");
$files_table='';

$pluginsList = get_plugins_list();//bw_dump($pluginsList);
$activePlugins = is_array(unserialize(getOption('active_plugins')))?unserialize(getOption('active_plugins')):array();//bw_dump($activePlugins);


    
  
$pluginsMenu = unserialize(getOption("custom_menu"));//bw_dump($pluginsMenu);
foreach ($pluginsList as $plugin){
    
    if(in_array($plugin['name'], $activePlugins)){
        $editable = "<a href='?do=deactivate&n=".  urlencode($plugin['name'])."' class='deactivate' alt='Deactivate'>
            <img src='./images/activate.png' border='0'/>
        </a>";
    }else{
        $editable = "<a href='?do=activate&n=".  urlencode($plugin['name'])."' class='activate' alt='Activate'>
            <img src='./images/deactivate.png' border='0'/>
        </a>";
    }
    
    
    
    $files_table .= "<h3>" . $plugin['plugin_name'] . "</h3>";
    $files_table .= "<p>" . $plugin['plugin_description'] . "</p>";
    $files_table .=  $editable.'<div class="clear"></div>' ;
    if(isset($pluginsMenu[$plugin['name']]))
        $files_table .= "<p><a href='" . $pluginsMenu[$plugin['name']]['menu_link'] . "'>".PLUGIN_SETTINGS."</a></p>";
}

///bw_activate_plugin('credit_card_payment');
if(count($pluginsList)>0){
?>

     
        <?php echo $files_table; ?>
   

<?php
}
$plugin_list =ob_get_contents();
ob_clean()
?>