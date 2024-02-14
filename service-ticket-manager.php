<?php
/*
Plugin Name: LIV Transport Service Ticket Manager
Plugin URI:  https://www.empuls3.com/plugins/service-ticket-manager
Description: A custom plugin to manage service tickets and integrate with QuickBooks
Version:     1.0.0
Author:      Empuls3
Author URI:  https://www.empuls3.com
License:     GPLv2 or later
Text Domain: service-ticket-manager
*/

include_once plugin_dir_path( __FILE__ ) . '/includes/service-tickets/cpt-registration.php';
include_once plugin_dir_path( __FILE__ ) . '/includes/service-tickets/meta-boxes.php';
include_once plugin_dir_path( __FILE__ ) . '/includes/service-tickets/taxonomies.php';


// Enqueue Scripts for Ticket Form Validation
function enqueue_ticket_form_scripts() {
    wp_enqueue_script(
        'ticket-form-validation',   // Handle for your script
        plugins_url( 'assets/js/ticket-form-validation.js', __FILE__ ), // Path to the script
        array('jquery'),                                                // Dependency on jQuery
        '1.0.0',                                                        // Version number
        true                                                            // Load in footer for better performance
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_ticket_form_scripts' );

// Enqueue Styles for Ticket Manager
function enqueue_ticket_styles() {
    wp_enqueue_style(
        'ticket-styles',                        // Handle for your stylesheet
        plugins_url( 'assets/css/service-ticket-manager.css', __FILE__ ), // Path to the stylesheet
        array(),                                                              // Dependencies (none in this case)
        '1.0.0',                                                            // Version number
        'all'                                                               // Media type (for all)
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_ticket_styles' );  // Or another hook  if only on specific pages

// Now include other remaining files
include_once( plugin_dir_path( __FILE__ ) . '/includes/roles.php' );
include_once( plugin_dir_path( __FILE__ ) . '/includes/data-handling.php' );

include_once( plugin_dir_path( __FILE__ ) . '/includes/admin-functions.php');
include_once( plugin_dir_path( __FILE__ ) . '/includes/service-tickets/taxonomies.php' );
