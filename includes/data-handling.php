<?php

function save_service_ticket_details($post_id) {
    // Check for nonce for security
    if (!isset($_POST['service_ticket_metabox_nonce']) ||
        !wp_verify_nonce($_POST['service_ticket_metabox_nonce'], 'service_ticket_form_nonce') ||
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
        !current_user_can('edit_post', $post_id)) {
        return;
    }

    // Define an array of field keys and their respective sanitization functions
    $fields = [
        'customer_name' => 'sanitize_text_field',
        'customer_account_number' => 'sanitize_text_field',
        'customer_city' => 'sanitize_text_field',
        'customer_state' => function($value) { return strtoupper(sanitize_text_field($value)); },
        'customer_zip' => 'sanitize_text_field',
        'customer_email' => 'sanitize_email',
        'date_of_services' => function($value) {
            $date = DateTime::createFromFormat('Y-m-d', $value);
            return ($date && $date->format('Y-m-d') === $value) ? $value : '';
        },
        'description_of_services' => function($value) { return wp_kses_post($value); },
        'delivery_start' => 'sanitize_text_field',
        'delivery_destination' => 'sanitize_text_field',
        'unit_price' => 'floatval',
        'extended_price' => 'floatval',
        'driver_name' => 'sanitize_text_field',
        'driver_email' => 'sanitize_email',
        'driver_contact' => 'sanitize_text_field',
        'manager_id' => 'intval',
        'ticket_status' => function($value) {
            $allowed = ['open', 'in_progress', 'closed'];
            return in_array($value, $allowed) ? $value : 'open';
        },
        'ticket_notes' => 'sanitize_textarea_field'
    ];

    foreach ($fields as $field => $sanitizer) {
        if (isset($_POST[$field])) {
            $value = is_callable($sanitizer) ? $sanitizer($_POST[$field]) : call_user_func($sanitizer, $_POST[$field]);
            if (!empty($value)) {
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }
}
add_action('save_post', 'save_service_ticket_details');
