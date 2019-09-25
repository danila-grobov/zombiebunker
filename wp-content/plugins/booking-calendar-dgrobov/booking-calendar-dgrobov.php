<?php

/**
 * Plugin Name: Booking Calendar
 * Author:  Danila Grobov
 * Description: Booking calendar plugin created for zombiebunker.lt
 */

function zbbc_register_menu()
{
	$page = add_menu_page(
		'ZB Booking Manager',
		'Booking',
		'manage_options',
		plugin_dir_path(__FILE__) . '/page.php',
		null,
		'data:image/svg+xml;base64,' . base64_encode('<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.6 20"><title>Layer 1</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path fill="white" d="M10.6,20,7.85,9.58a5.16,5.16,0,1,0-5,.2L.11,20Z"/></g></g></svg>'),
		45
	);
}

add_action('admin_menu', 'zbbc_register_menu');
