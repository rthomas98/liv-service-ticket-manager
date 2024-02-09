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

// Register Custom Post Type: Service Tickets
function create_service_ticket_post_type() {

    $labels = array(
        'name'                  => _x( 'Service Tickets', 'Post Type General Name', 'service-ticket-manager' ),
        'singular_name'         => _x( 'Service Ticket', 'Post Type Singular Name', 'service-ticket-manager' ),
        'menu_name'             => __( 'Service Tickets', 'service-ticket-manager' ),
        'name_admin_bar'        => __( 'Service Ticket', 'service-ticket-manager' ),
        'archives'              => __( 'Service Ticket Archives', 'service-ticket-manager' ),
        'attributes'            => __( 'Service Ticket Attributes', 'service-ticket-manager' ),
        'parent_item_colon'     => __( 'Parent Service Ticket:', 'service-ticket-manager' ),
        'all_items'             => __( 'All Service Tickets', 'service-ticket-manager' ),
        'add_new_item'          => __( 'Add New Service Ticket', 'service-ticket-manager' ),
        'add_new'               => __( 'Add New', 'service-ticket-manager' ),
        'new_item'              => __( 'New Service Ticket', 'service-ticket-manager' ),
        'edit_item'             => __( 'Edit Service Ticket', 'service-ticket-manager' ),
        'update_item'           => __( 'Update Service Ticket', 'service-ticket-manager' ),
        'view_item'             => __( 'View Service Ticket', 'service-ticket-manager' ),
        'view_items'            => __( 'View Service Tickets', 'service-ticket-manager' ),
        'search_items'          => __( 'Search Service Ticket', 'service-ticket-manager' ),
        'not_found'             => __( 'Not found', 'service-ticket-manager' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'service-ticket-manager' ),
    );
    $args = array(
        'label'                 => __( 'Service Ticket', 'service-ticket-manager' ),
        'description'           => __( 'Post type for managing service tickets', 'service-ticket-manager' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'custom-fields' ),
        'taxonomies'            => array( 'category', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-tickets-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    register_post_type( 'service_ticket', $args );

}
add_action( 'init', 'create_service_ticket_post_type' );

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

// Address Validation Script
function my_plugin_enqueue_scripts() {
    // Assuming your 'assets' folder is directly within your plugin folder
    wp_enqueue_script( 'my-address-validation-script', plugins_url( '/assets/js/my-address-validation.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );

    wp_localize_script( 'my-address-validation-script', 'myPluginData', array(
        'apiKey' => get_option( 'my_plugin_google_maps_api_key' )
    ) );
}
add_action( 'wp_enqueue_scripts', 'my_plugin_enqueue_scripts' );

// Function to set up/load the Google Maps Module Settings
function my_plugin_load_google_maps_settings() {
    // Check if we're in administration
    if ( is_admin() ) {
        include_once( dirname( __FILE__ ) . '/includes/plugin-settings.php' );
        my_google_maps_module_register_settings();
    }
}

// The Hooking Point – Ensure correct loading order
add_action( 'admin_init', 'my_plugin_load_google_maps_settings' );

// Add 'Google Maps API Settings' under the main 'Settings'
function my_plugin_add_settings_page() {
    add_submenu_page(
        'options-general.php', // Parent page slug (main 'Settings')
        'Google Maps API Settings', // Page Title
        'Google Maps Settings',     // Menu Title
        'manage_options',           // Required capability to access the page
        'my-google-maps-settings',  // Unique page slug
        'my_plugin_render_settings_page' // Callback for content
    );
}
add_action( 'admin_menu', 'my_plugin_add_settings_page' );

// Callback Function to Render Page Content (Simple for Now)
function my_plugin_render_settings_page() {
    ?>
    <h1>Google Maps API Settings</h1>
    <form method="post" action="options.php">
        <?php
        settings_fields( 'my_google_maps_module_settings_group' );
        do_settings_sections( 'my-google-maps-settings' );
        submit_button();
        ?>
    </form>
    <?php
}

// Now include other remaining files
include_once( plugin_dir_path( __FILE__ ) . '/includes/roles.php' );
include_once( plugin_dir_path( __FILE__ ) . '/includes/meta-boxes.php' );
include_once( plugin_dir_path( __FILE__ ) . '/includes/data-handling.php' );
