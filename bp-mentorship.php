<?php
/*
 * Plugin Name: MIF BP Mentorship
 * Plugin URI:  https://github.com/alexey-sergeev/mif-bp-mentorship
 * Author:      Alexey Sergeev
 * Author URI:  https://github.com/alexey-sergeev
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
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

add_action( 'wp_enqueue_scripts', 'mif_bp_mentorship_styles' );

function mif_bp_mentorship_styles() 
{
	wp_register_style( 'mif-bp-mentorship-styles', plugins_url( 'mif-bp-mentorship-styles.css', __FILE__ ) );
	wp_enqueue_style( 'mif-bp-mentorship-styles' );
}

?>
