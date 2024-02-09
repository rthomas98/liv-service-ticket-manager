<?php
// Module: Google Maps API Settings
function my_google_maps_module_register_settings() {
    // Register the Google Maps API settings field
    register_setting(
        'my_google_maps_module_settings_group', // Use a unique settings group
        'my_plugin_google_maps_api_key' // Option name in the database
    );

    // Add Settings Section
    add_settings_section(
        'my_google_maps_api_section',
        'Google Maps API Settings',
        'my_google_maps_api_section_callback',
        'my-google-maps-settings' // Changed here
    );

    add_settings_field(
        'my_plugin_google_maps_api_key_field',
        'API Key',
        'my_google_maps_api_key_field_callback',
        'my-google-maps-settings', // Changed here
        'my_google_maps_api_section'
    );
}

// The callbacks function
function my_google_maps_api_section_callback() {
    echo '<p>Enter your Google Maps Places API Key</p>';
}

function my_google_maps_api_key_field_callback() {
    $apiKey = get_option( 'my_plugin_google_maps_api_key' );
    ?>
    <input type="text" name="my_plugin_google_maps_api_key" value="<?php echo esc_attr( $apiKey ); ?>" class="regular-text">
    <?php
}

// Tigger on admin_init (conditionally if desired)
if ( is_admin() ) {
    my_google_maps_module_register_settings(); // Load it
}
