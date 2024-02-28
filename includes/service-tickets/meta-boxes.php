<?php

// Function to add the meta box container
function service_ticket_metabox() {
    add_meta_box(
        'service_ticket_details',
        __('Service Ticket Details', 'service-ticket-manager'),
        'display_service_ticket_form_fields',
        'service_ticket',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'service_ticket_metabox');

function display_service_ticket_form_fields($post) {
    // Security nonce field
    wp_nonce_field('service_ticket_form_nonce', 'service_ticket_metabox_nonce');

    $manager_id = get_post_meta($post->ID, '_manager_id', true);
    $manager_userdata = get_userdata($manager_id);
    $manager_name = $manager_userdata ? $manager_userdata->display_name : __('No Manager Assigned', 'service-ticket-manager');

    // Retrieve meta values
    $meta_values = [
        'customer_name' => get_post_meta($post->ID, '_customer_name', true),
        'customer_account_number' => get_post_meta($post->ID, '_customer_account_number', true),
        'customer_city' => get_post_meta($post->ID, '_customer_city', true),
        'customer_state' => get_post_meta($post->ID, '_customer_state', true),
        'customer_zip' => get_post_meta($post->ID, '_customer_zip', true),
        'customer_phone' => get_post_meta($post->ID, '_customer_phone', true),
        'customer_email' => get_post_meta($post->ID, '_customer_email', true),
        'date_of_services' => get_post_meta($post->ID, '_date_of_services', true),
        'description_of_services' => get_post_meta($post->ID, '_description_of_services', true),
        'delivery_start' => get_post_meta($post->ID, '_delivery_start', true),
        'delivery_destination' => get_post_meta($post->ID, '_delivery_destination', true),
        'unit_price' => get_post_meta($post->ID, '_unit_price', true),
        'extended_price' => get_post_meta($post->ID, '_extended_price', true),
        'driver_name' => get_post_meta($post->ID, '_driver_name', true),
        'driver_address' => get_post_meta($post->ID, '_driver_address', true),
        'driver_contact' => get_post_meta($post->ID, '_driver_contact', true),
        'driver_email' => get_post_meta($post->ID, '_driver_email', true),
        'manager_name' => $manager_name, // Use the retrieved manager's name
        'ticket_status' => get_post_meta($post->ID, '_ticket_status', true),
        'ticket_notes' => get_post_meta($post->ID, '_ticket_notes', true),
    ];



    // Display form fields
    foreach ($meta_values as $key => $value) {
        echo '<p>';
        echo '<label for="' . esc_attr($key) . '">' . esc_html__(ucwords(str_replace('_', ' ', $key)), 'service-ticket-manager') . '</label><br>';

        if ($key === 'manager_name') {
            echo '<input type="text" readonly="readonly" value="' . esc_attr($value) . '" class="widefat"/>'; // Display manager's name in a readonly input
        } elseif ($key === 'ticket_status') {
            echo '<select name="' . esc_attr($key) . '" id="' . esc_attr($key) . '">';
            foreach (['open', 'in_progress', 'closed'] as $status) {
                echo '<option value="' . esc_attr($status) . '"' . selected($value, $status, false) . '>' . esc_html(ucfirst($status)) . '</option>';
            }
            echo '</select>';
        } elseif ($key === 'description_of_services' || $key === 'ticket_notes') {
            echo '<textarea name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" class="widefat">' . esc_textarea($value) . '</textarea>';
        } else {
            echo '<input type="text" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="widefat"/>';
        }
        echo '</p>';
    }

    // Display Service Types dropdown
    $service_types = get_terms([
        'taxonomy' => 'service_type',
        'hide_empty' => false,
    ]);

    echo '<p><label for="service_type">' . __('Service Type:', 'service-ticket-manager') . '</label><br>';
    echo '<select name="service_type" id="service_type" class="widefat">';
// Adding a default placeholder option
    echo '<option value="">' . __('Select a Service Type', 'service-ticket-manager') . '</option>';

    foreach ($service_types as $type) {
        $selected = has_term($type->term_id, 'service_type', $post->ID) ? ' selected' : '';
        echo '<option value="' . esc_attr($type->term_id) . '"' . $selected . '>' . esc_html($type->name) . '</option>';
    }
    echo '</select></p>';


    // Display Priority Levels dropdown
    $priority_levels = get_terms([
        'taxonomy' => 'priority_level',
        'hide_empty' => false,
    ]);
    echo '<p><label for="priority_level">' . __('Priority Level:', 'service-ticket-manager') . '</label><br>';
    echo '<select name="priority_level" id="priority_level" class="widefat">';
// Placeholder option
    echo '<option value="">' . __('-- Select Priority Level --', 'service-ticket-manager') . '</option>';
    foreach ($priority_levels as $level) {
        $selected = has_term($level->term_id, 'priority_level', $post->ID) ? ' selected' : '';
        echo '<option value="' . esc_attr($level->term_id) . '"' . $selected . '>' . esc_html($level->name) . '</option>';
    }
    echo '</select></p>';

}

function save_service_ticket_meta_box_data($post_id) {
    // Check if our nonce is set and verify it.
    if (!isset($_POST['service_ticket_metabox_nonce']) || !wp_verify_nonce($_POST['service_ticket_metabox_nonce'], 'service_ticket_form_nonce')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, or the user doesn't have permission, return.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || !current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save or update metadata for input fields
    $fields_to_save = [
        'customer_name', 'customer_account_number', 'customer_city', 'customer_state',
        'customer_zip', 'customer_phone', 'customer_email', 'date_of_services',
        'description_of_services', 'delivery_start', 'delivery_destination',
        'unit_price', 'extended_price', 'driver_name', 'driver_address',
        'driver_contact', 'driver_email', 'manager_id', 'ticket_status', 'ticket_notes'
    ];

    foreach ($fields_to_save as $field) {
        if (array_key_exists($field, $_POST)) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Save Service Type taxonomy
    if (isset($_POST['service_type']) && !empty($_POST['service_type'])) {
        wp_set_object_terms($post_id, (int)$_POST['service_type'], 'service_type');
    }

    // Save Priority Level taxonomy
    if (isset($_POST['priority_level']) && !empty($_POST['priority_level'])) {
        wp_set_object_terms($post_id, (int)$_POST['priority_level'], 'priority_level');
    }
}
add_action('save_post', 'save_service_ticket_meta_box_data');

