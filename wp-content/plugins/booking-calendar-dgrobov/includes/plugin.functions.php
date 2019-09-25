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

define('PLUGIN_DIR', "plugins");
define('PLUGIN_MAIN_FILE', "main.php");
define('PLUGIN_PATH', MAIN_PATH . "/" . PLUGIN_DIR . "/");


$BW_install_actions = array();
$BW_uninstall_actions = array();
$BW_admin_pages = array();
$BW_actions = array();
$BW_pages = array();
$BW_custom_menu = array();





function get_plugins_list()
{

    $pluginList = array();

    foreach (scandir(PLUGIN_PATH) as $plugin) {

        $pluginMainFile = PLUGIN_PATH . $plugin . "/" . PLUGIN_MAIN_FILE;
        //print bw_dump($pluginMainFile);

        if (is_file($pluginMainFile)) {
            $plugin_data = bw_get_file_data($pluginMainFile);
            $plugin_data['name'] = $plugin;
            $pluginList[] = $plugin_data;
        }
    }
    //print bw_dump($pluginList);
    return $pluginList;
}

function load_plugins()
{
    $activePlugins = unserialize(getOption('active_plugins'));
    if (is_array($activePlugins)) {
        foreach ($activePlugins as $plugin) {
            $pluginMainFile = PLUGIN_PATH . $plugin . "/" . PLUGIN_MAIN_FILE;
            //print bw_dump($pluginMainFile);

            if (is_file($pluginMainFile)) {

                require_once $pluginMainFile;
            }
        }
    }
    bw_do_action("bw_init");
}

function bw_activate_plugin($plugin_name)
{
    global $BW_install_actions;

    require_once PLUGIN_PATH . $plugin_name . "/" . PLUGIN_MAIN_FILE;

    //bw_dump($BW_install_actions);

    foreach ($BW_install_actions as $item) {
        if ($item['plugin_name'] == $plugin_name && !empty($item['install_function'])) {

            if (register_plugin($plugin_name)) {
                call_user_func($item['install_function']);
                return TRUE;
            }
        }
    }
    return FALSE;
}
function  bw_deactivate_plugin($plugin_name)
{
    global $BW_uninstall_actions;

    require_once PLUGIN_PATH . $plugin_name . "/" . PLUGIN_MAIN_FILE;

    //bw_dump($BW_uninstall_actions);

    foreach ($BW_uninstall_actions as $item) {
        if ($item['plugin_name'] == $plugin_name && !empty($item['uninstall_function'])) {

            if (unregister_plugin($plugin_name)) {
                call_user_func($item['uninstall_function']);
                deleteOption("custom_menu");
                return TRUE;
            }
        }
    }
    return FALSE;
}

function add_install_action($path, $action_function)
{
    global $BW_install_actions;

    $pluginName = basename(dirname($path));
    $BW_install_actions[] = array("plugin_name" => $pluginName, "install_function" => $action_function);
}

function add_uninstall_action($path, $action_function)
{
    global $BW_uninstall_actions;

    $pluginName = basename(dirname($path));
    $BW_uninstall_actions[] = array("plugin_name" => $pluginName, "uninstall_function" => $action_function);
}

function is_active_plugin($plugin_name)
{
    $activePlugins = getOption("active_plugins");
    $activePlugins = unserialize($activePlugins);
    if (is_array($activePlugins)) {

        if (in_array($plugin_name, $activePlugins)) return TRUE;
    }
    return FALSE;
}

function register_plugin($plugin_name)
{

    $activePlugins = getOption("active_plugins");
    if (is_array(unserialize($activePlugins))) {
        $activePlugins = unserialize($activePlugins);
        if (in_array($plugin_name, $activePlugins)) return FALSE;
    } else {
        $activePlugins = array();
    }

    $activePlugins[] = $plugin_name;

    updateOption('active_plugins', $activePlugins);

    return TRUE;
}

function unregister_plugin($plugin_name)
{

    $activePlugins = getOption("active_plugins");
    if (is_array(unserialize($activePlugins))) {
        $activePlugins = unserialize($activePlugins);
        if (in_array($plugin_name, $activePlugins)) {
            $activePlugins = array_flip($activePlugins);
            unset($activePlugins[$plugin_name]);
            $activePlugins = array_flip($activePlugins);
            updateOption("active_plugins", $activePlugins);
            return true;
        } else { }
    } else {
        return true;
    }
}

function bw_get_file_data($file)
{
    // We don't need to write to the file, so just open for reading.
    $fp = fopen($file, 'r');

    // Pull only the first 8kiB of the file in.
    $file_data = fread($fp, 8192);

    // PHP will close file handle, but we are good citizens.
    fclose($fp);
    $all_headers = array(
        "plugin_name" => "Plugin Name",
        "plugin_description" => "Description"
    );


    foreach ($all_headers as $field => $regex) {
        preg_match('/^[ \t\/*#]*' . preg_quote($regex, '/') . ':(.*)$/mi', $file_data, ${$field});
        if (!empty(${$field}))
            ${$field} =  ${$field}[1];
        else
            ${$field} = '';
    }

    $file_data = compact(array_keys($all_headers));
    //bw_dump($file_data);
    return $file_data;
}

function bw_add_action($action, $action_function)
{

    return bw_add_filter($action, $action_function);
}

function bw_add_filter($filter, $action_function)
{
    global $BW_actions;
    //print $action_function;

    $BW_actions[$filter][] = array("install_function" => $action_function);
    return TRUE;
}

function bw_do_action($action, $args = '')
{
    global $BW_actions;

    $args = array();
    $args = func_get_args();
    if (isset($BW_actions[$action])) {

        foreach ($BW_actions[$action] as $do) {

            call_user_func_array($do['install_function'], array_slice($args, 1));
            //bw_dump($do);
        }
    }
    return TRUE;
}

function bw_apply_filter($action, $value)
{
    global $BW_actions;
    $args = array();



    $args = func_get_args();
    if (isset($BW_actions[$action])) {

        foreach ($BW_actions[$action] as $do) {
            $args[1] = $value;
            $value = call_user_func_array($do['install_function'], array_slice($args, 1));
            //bw_dump($do);
        }
        return $value;
    }
    return $value;
}

function add_admin_page($path, $menu_name, $action, $icon = "")
{
    global $BW_admin_pages, $_menuList, $BW_custom_menu;
    $plugin = basename(dirname($path));
    if (is_active_plugin($plugin)) {
        $BW_admin_pages[] = array(
            "path" => $path,
            "menu_name" => $menu_name
        );

        $menuItem = array(
            "menu_title" => $menu_name,
            "menu_link" => "getAdminPage.php?p=" . urlencode($action),
            "menu_action" => urlencode($action),
            "menu_icon" => $icon
        );
        $BW_custom_menu[$plugin] = $menuItem;
        //$menuList [$plugin] = $menuItem;
        //bw_add_action("get_menu","get_menu");
        //bw_dump($BW_admin_pages);
        bw_add_action("get_admin_page_$action", $action);
    }
    updateOption("custom_menu",  serialize($BW_custom_menu));
}

function add_page($path, $page_name, $action, $icon = "")
{
    global $BW_pages, $menuList;
    $plugin = basename(dirname($path));
    if (is_active_plugin($plugin)) {
        $BW_pages[] = array(
            "path" => $path,
            "menu_name" => $page_name
        );
        //bw_dump($BW_pages);
        bw_add_action("bw_get_page_$action", $action);
    }
}

function build_plugins_menu()
{
    global $BW_custom_menu;
    $menu = '';

    $menu .= count($BW_custom_menu) > 1 ? '<span>Plugins</span><ul>' : '';
    foreach ($BW_custom_menu as $menuItem) {
        $active = strpos($_SERVER['REQUEST_URI'], $menuItem['menu_link']) ? "active" : "";
        $menu .= "
					<li><a href=\"{$menuItem['menu_link']}\" class=\"{$active}\">{$menuItem['menu_title']}</a></li>
					";
    }
    $menu .= count($BW_custom_menu) > 1 ? '</ul>' : '';

    echo $menu;
}
