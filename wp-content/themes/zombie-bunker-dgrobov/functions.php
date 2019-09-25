<?php
wp_register_script('bundle', get_template_directory_uri() . '/dist/bundle.js');
function themeslug_enqueue_script()
{
    wp_enqueue_script('bundle');
    wp_enqueue_script('map');
}
add_action('wp_enqueue_scripts', 'themeslug_enqueue_script');
add_filter('show_admin_bar', '__return_false');
function theme_prefix_register_elementor_locations($elementor_theme_manager)
{
    $elementor_theme_manager->register_all_core_location();
}
add_action('elementor/theme/register_locations', 'theme_prefix_register_elementor_locations');
