<?php
/**
Plugin Name: ResOnline Booking Gadget
Plugin URI: https://wordpress.org/plugins/resonline-booking-gadget
Description: Displays a <a href="https://www.resonline.com/features/online-booking-integration" target="_blank" title="ResOnline Booking Gadget">ResOnline Booking Gadget</a> for any ResOnline property, using a simple short code.
Author: Phoenix Online
Version: 1.0
Author URI: https://profiles.wordpress.org/psdtofinal
License: GPLv2 or later
Text Domain: phx
*/

/**
 * @package Phx_Res_Online_Plugin
 * @version 1.0
 */

/** Define the version */
define("PHX_RES_ONLINE_VERSION", "1.0.3");

/** Define the text domain */
if (!defined("PHX_DOMAIN")) {
    define("PHX_DOMAIN", "phx");
}

/** Next, check where this is being called from... */
if (defined( 'DOING_AJAX' ) && DOING_AJAX) {

    /**
     * Looks like processing something AJAX'y. Load the
     * class handler...
     */
    require_once plugin_dir_path( __FILE__)."lib/PhxResOnlineAjax.php";
    $PhxResOnlineAjax = new PhxResOnlineAjax(__FILE__, PHX_RES_ONLINE_VERSION);

} else if (is_admin()) {

    /**
     * Looks like we're in the admin section, enqueue the admin
     * scripts / CSS and call the admin class
     */
    require_once plugin_dir_path( __FILE__)."lib/PhxResOnlineAdmin.php";
    $PhxResOnlineAdmin = new PhxResOnlineAdmin(__FILE__, PHX_RES_ONLINE_VERSION);

} else {

    /**
     * We have to assume we're front of house. Enqueue the FOH
     * scripts, CSS and helper classes
     */
    require_once plugin_dir_path( __FILE__)."lib/PhxResOnlinePublic.php";
    $PhxResOnlinePublic = new PhxResOnlinePublic(__FILE__, PHX_RES_ONLINE_VERSION);
}

/**
 * Creates the base / initial / default settings for the
 * Booking Gadget
 */
function phx_resonline_booking_gadget_install() {
    require_once plugin_dir_path( __FILE__)."lib/PhxResOnlineAdmin.php";
    $PhxResOnlineAdmin = new PhxResOnlineAdmin(__FILE__, PHX_RES_ONLINE_VERSION);
    $PhxResOnlineAdmin->install();
}
register_activation_hook( __FILE__, 'phx_resonline_booking_gadget_install' );

/**
 * Developers' note:
 *
 * "Full" (and I use the term loosely) documentation for the ResOnline Booking
 * Gadget can be found here:
 * https://gadgets.securetravelpayments.com/_doc/
 *
 * Still on the TODO List:
 *
 * - Adding more options (we'll eventually get through all of them)
 * - Adding styling options / custom CSS
 * - Splitting documentation into it's own section within the plugin
 *
 * If you'd like to make a feature request, please hit us up!
 */


