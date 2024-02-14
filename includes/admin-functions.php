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


function add_service_ticket_error($error) {
    $errors = get_transient('service_ticket_manager_errors') ?: [];
    $errors[] = $error;
    set_transient('service_ticket_manager_errors', $errors, HOUR_IN_SECONDS);
}

function clear_service_ticket_errors() {
    delete_transient('service_ticket_manager_errors');
}
add_action('admin_init', 'clear_service_ticket_errors');

