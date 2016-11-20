<?php
/*
 * Plugin Name: MIF BP Mentorship
 * Plugin URI:  https://github.com/alexey-sergeev/mif-bp-mentorship
 * Author:      Alexey Sergeev
 * Author URI:  https://github.com/alexey-sergeev
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Description: Плагин BuddyPress для создания системы наставников в социальной образовательной сети.
 * Version:     0.0.1
 * Text Domain: mif-bp-mentorship
 * Domain Path: /lang/
 */

defined( 'ABSPATH' ) || exit;

//
// Подключаем свой файл CSS
//
//

add_action( 'wp_enqueue_scripts', 'mif_bp_mentorship_scripts_and_styles' );

function mif_bp_mentorship_scripts_and_styles() 
{
	wp_register_style( 'mentorship-styles', plugins_url( 'mentorship.css', __FILE__ ) );
	wp_enqueue_style( 'mentorship-styles' );

    wp_register_script( 'mentorship-scripts', plugins_url( 'js/mentorship.js', __FILE__ ) );  
    wp_enqueue_script( 'mentorship-scripts' );

}

//
// Подключаем файлы плагина
//
//

add_action( 'plugins_loaded', 'mif_bp_mentorship_init' );

function mif_bp_mentorship_init() 
{
	$plugin_path = plugin_dir_path( __FILE__ );
	require_once $plugin_path . 'inc/functions.php';
	require_once $plugin_path . 'inc/mentors-page.php';
	require_once $plugin_path . 'inc/ajax-helper.php';
}



?>
