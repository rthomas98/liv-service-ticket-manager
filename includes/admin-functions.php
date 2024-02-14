<?php
function service_ticket_manager_admin_notices() {
    $errors = get_transient('service_ticket_manager_errors');
    if ($errors) {
        foreach ($errors as $error) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($error) . '</p></div>';
        }
        delete_transient('service_ticket_manager_errors');
    }
}
add_action('admin_notices', 'service_ticket_manager_admin_notices');

function add_service_ticket_error($error) {
    $errors = get_transient('service_ticket_manager_errors') ?: [];
    $errors[] = $error;
    set_transient('service_ticket_manager_errors', $errors, HOUR_IN_SECONDS);
}

function clear_service_ticket_errors() {
    delete_transient('service_ticket_manager_errors');
}
add_action('admin_init', 'clear_service_ticket_errors');

function my_plugin_add_quickbooks_connect_page() {
    add_menu_page(
        'QuickBooks Integration',
        'QuickBooks Connect',
        'manage_options',
        'my-plugin-quickbooks-connect',
        'my_plugin_quickbooks_connect_page_callback',
        'dashicons-chart-line',
        6
    );
}
add_action('admin_menu', 'my_plugin_add_quickbooks_connect_page');

function my_plugin_register_settings() {
    register_setting('my_plugin_settings', 'my_plugin_quickbooks_client_id');
    add_settings_section('my_plugin_quickbooks_settings_section', 'QuickBooks Integration Settings', 'my_plugin_quickbooks_settings_section_callback', 'my_plugin_settings');
    add_settings_field('my_plugin_quickbooks_client_id_field', 'QuickBooks Client ID', 'my_plugin_quickbooks_client_id_field_callback', 'my_plugin_settings', 'my_plugin_quickbooks_settings_section');
}
add_action('admin_init', 'my_plugin_register_settings');

function my_plugin_quickbooks_settings_section_callback() {
    echo '<p>Enter your QuickBooks integration details below.</p>';
}

function my_plugin_quickbooks_client_id_field_callback() {
    $client_id = get_option('my_plugin_quickbooks_client_id');
    echo '<input type="text" id="my_plugin_quickbooks_client_id" name="my_plugin_quickbooks_client_id" value="' . esc_attr($client_id) . '"/>';
}

function my_plugin_quickbooks_connect_page_callback() {
    settings_errors();
    if (isset($_GET['code']) && isset($_GET['state'])) {
        $authorization_code = sanitize_text_field($_GET['code']);
        $state = sanitize_text_field($_GET['state']);
        $savedState = get_option('my_plugin_quickbooks_oauth_state');
        if ($state === $savedState) {
            if (my_plugin_exchange_quickbooks_token($authorization_code)) {
                echo '<div class="notice notice-success is-dismissible"><p>Authorization successful. Tokens have been stored.</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>Authorization failed. Please try connecting again.</p></div>';
            }
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>Invalid state. Possible CSRF detected.</p></div>';
        }
    }
    $newState = wp_generate_password(12, false);
    update_option('my_plugin_quickbooks_oauth_state', $newState);
    $authUrl = my_plugin_generate_quickbooks_authorization_url($newState);
    echo '<p>Connect to QuickBooks by authorizing this application:</p>';
    echo "<a href='" . esc_url($authUrl) . "' class='button button-primary'>Connect to QuickBooks</a>";
    echo '<hr>';
    echo '<h1>QuickBooks Settings</h1>';
    echo '<form method="post" action="options.php">';
    settings_fields('my_plugin_settings');
    do_settings_sections('my_plugin_settings');
    submit_button();
    echo '</form>';
}

function my_plugin_generate_quickbooks_authorization_url($state) {
    $client_id = get_option('my_plugin_quickbooks_client_id');
    $redirect_uri = urlencode(admin_url('admin.php?page=my-plugin-quickbooks-connect'));
    $scope = urlencode('com.intuit.quickbooks.accounting');
    $response_type = 'code';
    $authorization_url = "https://appcenter.intuit.com/connect/oauth2"
        . "?client_id={$client_id}"
        . "&redirect_uri={$redirect_uri}"
        . "&scope={$scope}"
        . "&response_type={$response_type}"
        . "&state={$state}";
    return $authorization_url;
}

function my_plugin_validate_client_id($input) {
    if (preg_match('/^[a-zA-Z0-9]+$/', $input)) {
        return $input;
    } else {
        add_settings_error(
            'my_plugin_quickbooks_client_id',
            'invalid-quickbooks-client-id',
            'The QuickBooks Client ID provided is invalid. Please ensure it contains only letters and numbers.',
            'error'
        );
        return get_option('my_plugin_quickbooks_client_id');
    }
}
add_filter('pre_update_option_my_plugin_quickbooks_client_id', 'my_plugin_validate_client_id', 10, 2);

function fetch_quickbooks_customers() {
    $access_token = get_option('my_plugin_quickbooks_access_token');
    $realmId = get_option('my_plugin_quickbooks_realm_id');
    if (empty($access_token) || empty($realmId)) {
        error_log('QuickBooks access token or realm ID is not set.');
        return false;
    }
    $url = "https://quickbooks.api.intuit.com/v3/company/$realmId/query";
    $query = urlencode('SELECT * FROM Customer MAXRESULTS 1000');
    $full_url = $url . "?query=" . $query;
    $args = [
        'headers' => [
            'Authorization' => "Bearer $access_token",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ],
        'method' => 'GET',
        'timeout' => 45,
    ];
    $response = wp_remote_get($full_url, $args);
    if (is_wp_error($response)) {
        error_log('Error fetching customers from QuickBooks: ' . $response->get_error_message());
        return false;
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    if (isset($data['QueryResponse']['Customer']) && is_array($data['QueryResponse']['Customer'])) {
        return $data['QueryResponse']['Customer'];
    } else {
        error_log('No customers found or there was an issue with the QuickBooks API response.');
        return false;
    }
}
