<?php
function service_ticket_manager_admin_notices() {
    $errors = get_transient('service_ticket_manager_errors');
    if ($errors) {
        foreach ($errors as $error) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($error) . '</p></div>';
        }
        // Clear the transient to avoid displaying the same messages again
        delete_transient('service_ticket_manager_errors');
    }
}
add_action('admin_notices', 'service_ticket_manager_admin_notices');

// Add a new error to the transient
function add_service_ticket_error($error) {
    $errors = get_transient('service_ticket_manager_errors') ?: [];
    $errors[] = $error;
    set_transient('service_ticket_manager_errors', $errors, HOUR_IN_SECONDS);
}

function clear_service_ticket_errors() {
    delete_transient('service_ticket_manager_errors');
}
add_action('admin_init', 'clear_service_ticket_errors');


// Add a new menu item for QuickBooks Connect
function my_plugin_add_quickbooks_connect_page() {
    add_menu_page(
        'QuickBooks Integration', // Page Title
        'QuickBooks Connect', // Menu Title
        'manage_options', // Capability
        'my-plugin-quickbooks-connect', // Menu Slug
        'my_plugin_quickbooks_connect_page_callback', // Function
        'dashicons-chart-line', // Icon URL
        6 // Position
    );
}
add_action('admin_menu', 'my_plugin_add_quickbooks_connect_page');

// Register settings for the QuickBooks Connect page

function my_plugin_register_settings() {
    // Register a new setting for "QuickBooks integration" page
    register_setting('my_plugin_settings', 'my_plugin_quickbooks_client_id');

    // Add a new section to "QuickBooks integration" page
    add_settings_section(
        'my_plugin_quickbooks_settings_section',
        'QuickBooks Integration Settings',
        'my_plugin_quickbooks_settings_section_callback',
        'my_plugin_settings'
    );

    // Add a new field for the QuickBooks Client ID
    add_settings_field(
        'my_plugin_quickbooks_client_id_field',
        'QuickBooks Client ID',
        'my_plugin_quickbooks_client_id_field_callback',
        'my_plugin_settings',
        'my_plugin_quickbooks_settings_section'
    );
}
add_action('admin_init', 'my_plugin_register_settings');


function my_plugin_quickbooks_settings_section_callback() {
    echo '<p>Enter your QuickBooks integration details below.</p>';
}

function my_plugin_quickbooks_client_id_field_callback() {
    $client_id = get_option('my_plugin_quickbooks_client_id');
    echo '<input type="text" id="my_plugin_quickbooks_client_id" name="my_plugin_quickbooks_client_id" value="' . esc_attr($client_id) . '"/>';
}


function my_plugin_exchange_quickbooks_token($authorization_code) {
    $client_id = get_option('my_plugin_quickbooks_client_id');
    $client_secret = get_option('my_plugin_quickbooks_client_secret'); // Ensure you have a method to securely store and retrieve this
    $redirect_uri = urlencode(admin_url('admin.php?page=my-plugin-quickbooks-connect'));
    $token_url = 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer';

    $response = wp_remote_post($token_url, [
        'body' => [
            'grant_type' => 'authorization_code',
            'code' => $authorization_code,
            'redirect_uri' => $redirect_uri,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
        ],
        'headers' => [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ],
    ]);

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
        add_service_ticket_error('Failed to exchange authorization code for tokens. Please try again.');
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (!empty($body['access_token']) && !empty($body['refresh_token'])) {
        update_option('my_plugin_quickbooks_access_token', $body['access_token']);
        update_option('my_plugin_quickbooks_refresh_token', $body['refresh_token']);
        return true;
    } else {
        add_service_ticket_error('Failed to retrieve tokens from QuickBooks.');
        return false;
    }
}


// Callback function for the QuickBooks Connect page
function my_plugin_quickbooks_connect_page_callback() {
    settings_errors();

    if (isset($_GET['code'])) {
        $authorization_code = sanitize_text_field($_GET['code']);
        // Attempt to exchange the authorization code for tokens
        if (my_plugin_exchange_quickbooks_token($authorization_code)) {
            echo '<div class="notice notice-success">Authorization successful. Tokens have been stored.</div>';
        } else {
            // Here we add the error using the add_service_ticket_error function
            add_service_ticket_error('Failed to exchange authorization code for tokens. Please try again.');
            // Optionally, you can call service_ticket_manager_admin_notices() to display errors immediately,
            // but it's already being hooked to 'admin_notices', so it will display on the next page load.
            echo '<div class="notice notice-error">Authorization failed. Please try connecting again.</div>';
        }
    } else {
        $state = wp_generate_password(12, false);
        update_option('my_plugin_quickbooks_oauth_state', $state);

        $auth_url = my_plugin_generate_quickbooks_authorization_url($state);
        echo '<p>Connect to QuickBooks by authorizing this application.</p>';
        echo "<a href='{$auth_url}' class='button button-primary'>Connect to QuickBooks</a>";
    }

    // Render the settings form for QuickBooks Client ID
    echo '<form action="options.php" method="post">';
    do_settings_sections('my_plugin_settings'); // This outputs the settings sections for 'my_plugin_settings'
    settings_fields('my_plugin_settings'); // This outputs the nonce, action, and option_page fields for the 'my_plugin_settings' section
    submit_button('Save Settings');
    echo '</form>';
}

// Function to generate the QuickBooks authorization URL

function my_plugin_generate_quickbooks_authorization_url($state) {
    $client_id = get_option('my_plugin_quickbooks_client_id'); // Get the client ID from WordPress options
    $redirect_uri = urlencode(admin_url('admin.php?page=my-plugin-quickbooks-connect'));
    $scope = urlencode('com.intuit.quickbooks.accounting');
    $authorization_url = "https://appcenter.intuit.com/connect/oauth2?client_id={$client_id}&redirect_uri={$redirect_uri}&scope={$scope}&response_type=code&state={$state}";

    return $authorization_url;
}

// Validate the QuickBooks Client ID
function my_plugin_validate_client_id($input) {
    // Basic validation for alphanumeric characters
    if (!preg_match('/^[a-zA-Z0-9]+$/', $input)) {
        add_settings_error(
            'my_plugin_quickbooks_client_id',
            'my_plugin_quickbooks_client_id_error',
            'The Client ID should only contain letters and numbers.',
            'error'
        );
        // Retrieve the current value in case of error
        return get_option('my_plugin_quickbooks_client_id');
    }
    return $input; // Return sanitized input if validation passes
}
add_filter('pre_update_option_my_plugin_quickbooks_client_id', 'my_plugin_validate_client_id', 10, 2);


