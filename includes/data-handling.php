<?php

// Function to save ticket details
function save_service_ticket_details( $post_id ) {

    $errors = get_transient('service_ticket_manager_errors') ?: [];


    // *** Security Checks ****
    // 1. Nonce Verification
    if ( !isset( $_POST['service_ticket_metabox_nonce'] ) ||
        !wp_verify_nonce( $_POST['service_ticket_metabox_nonce'], 'service_ticket_form_nonce' ) ) {
        return; // Exit the function if the nonce isn't set or fails verification
    }

    // 2. Autosave Check
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return; // Exit if WordPress is doing an autosave
    }

    // 3. Capability Check
    if ( !current_user_can( 'edit_post', $post_id ) ) {
        return; // Ensure the current user has permission to edit the ticket
    }


    // Customer Fields

    // Customer Name Validation and Sanitization
    if (isset($_POST['customer_name'])) {
        $customer_name = sanitize_text_field($_POST['customer_name']);
        if (empty($customer_name)) {
            $errors['customer_name'] = 'Customer Name is required.';
        } elseif (!is_valid_name($customer_name)) { // Assuming is_valid_name is a custom validation function you've defined
            $errors['customer_name'] = 'Invalid Customer Name format.';
        } else {
            update_post_meta($post_id, '_customer_name', $customer_name);
        }
    }


    if (isset($_POST['customer_account_number'])) {
        $customer_account_number = sanitize_text_field($_POST['customer_account_number']);
        update_post_meta($post_id, '_customer_account_number', $customer_account_number);
    }


    if (isset($_POST['customer_city'])) {
        $customer_city = sanitize_text_field($_POST['customer_city']);
        if (empty($customer_city)) {
            $errors['customer_city'] = 'City is required.';
        } elseif (!preg_match('/^[a-zA-Z\s\-.,]+$/', $customer_city)) {
            $errors['customer_city'] = 'Invalid city name format.';
        } else {
            update_post_meta($post_id, '_customer_city', $customer_city);
        }
    }


    if (isset($_POST['customer_state'])) {
        $customer_state = strtoupper(sanitize_text_field($_POST['customer_state'])); // Force uppercase and sanitize
        if (empty($customer_state)) {
            $errors['customer_state'] = 'State is required.';
        } elseif (!preg_match('/^[A-Z]{2}$/', $customer_state)) {
            $errors['customer_state'] = 'Invalid state format. Please use the two-letter state code.';
        } else {
            update_post_meta($post_id, '_customer_state', $customer_state);
        }
    }


    if (isset($_POST['customer_zip'])) {
        $customer_zip = sanitize_text_field($_POST['customer_zip']);
        if (empty($customer_zip)) {
            $errors['customer_zip'] = 'Zip Code is required.';
        } elseif (!preg_match('/^\d{5}(-\d{4})?$/', $customer_zip)) { // Updated regex to allow for ZIP+4 format
            $errors['customer_zip'] = 'Invalid Zip Code format.';
        } else {
            update_post_meta($post_id, '_customer_zip', $customer_zip);
        }
    }


    if (isset($_POST['customer_email'])) {
        $customer_email = sanitize_email($_POST['customer_email']);
        if (!is_email($customer_email)) {
            $errors['customer_email'] = 'Invalid email address.';
        } else {
            update_post_meta($post_id, '_customer_email', $customer_email);
        }
    }

    // Service Details
    if (isset($_POST['date_of_services'])) {
        $date_of_services = sanitize_text_field($_POST['date_of_services']);
        $date = DateTime::createFromFormat('Y-m-d', $date_of_services);
        if ($date && $date->format('Y-m-d') === $date_of_services) {
            update_post_meta($post_id, '_date_of_services', $date_of_services);
        } else {
            add_service_ticket_error('Invalid Date of Services format. Expected YYYY-MM-DD.');
        }
    }


    if (isset($_POST['description_of_services'])) {
        update_post_meta($post_id, '_description_of_services', wp_kses_post($_POST['description_of_services']));
    }


    // Delivery Details (same pattern as customer fields)
    // ...

    if (isset($_POST['delivery_start'])) {
        update_post_meta($post_id, '_delivery_start', sanitize_text_field($_POST['delivery_start']));
    }
    if (isset($_POST['delivery_destination'])) {
        update_post_meta($post_id, '_delivery_destination', sanitize_text_field($_POST['delivery_destination']));
    }

    // Prices
    if (isset($_POST['unit_price'])) {
        update_post_meta($post_id, '_unit_price', floatval($_POST['unit_price']));
    }
    if (isset($_POST['extended_price'])) {
        update_post_meta($post_id, '_extended_price', floatval($_POST['extended_price']));
    }


    // Subcontractor/Driver Details (Similar to customer)
    // ...

    if (isset($_POST['driver_name'])) {
        update_post_meta($post_id, '_driver_name', sanitize_text_field($_POST['driver_name']));
    }
    if (isset($_POST['driver_email'])) {
        $driver_email = sanitize_email($_POST['driver_email']);
        if (!is_email($driver_email)) {
            add_service_ticket_error('Invalid Driver Email Address provided.');
        } else {
            update_post_meta($post_id, '_driver_email', $driver_email);
        }
    }

    if (isset($_POST['driver_contact'])) {
        update_post_meta($post_id, '_driver_contact', sanitize_text_field($_POST['driver_contact'])); // Consider regex validation for format
    }
    if (isset($_POST['driver_email'])) {
        $driver_email = sanitize_email($_POST['driver_email']);
        if (!is_email($driver_email)) {
            // Fetch existing errors
            $errors = get_transient('service_ticket_manager_errors') ?: [];
            $errors[] = 'Invalid Driver Email Address provided.';
            // Save the updated errors array back to the transient
            set_transient('service_ticket_manager_errors', $errors, HOUR_IN_SECONDS);
        } else {
            update_post_meta($post_id, '_driver_email', $driver_email);
        }
    }



    // Driver Details (Similar to customer)

    // Manager/Dispatch, Status, Notes
    if (isset($_POST['manager_id'])) {
        update_post_meta($post_id, '_manager_id', absint($_POST['manager_id']));
    }

    if (isset($_POST['ticket_status'])) {
        $allowed_statuses = ['open', 'in_progress', 'closed'];
        $submitted_status = sanitize_text_field($_POST['ticket_status']);
        if (!in_array($submitted_status, $allowed_statuses)) {
            $errors = get_transient('service_ticket_manager_errors') ?: [];
            $errors[] = 'Invalid Ticket Status submitted.';
            set_transient('service_ticket_manager_errors', $errors, HOUR_IN_SECONDS);
            $submitted_status = 'open'; // Default to 'open' or handle as needed
        }
        update_post_meta($post_id, '_ticket_status', $submitted_status);
    }


    if (isset($_POST['ticket_notes'])) {
        update_post_meta($post_id, '_ticket_notes', sanitize_textarea_field($_POST['ticket_notes']));
    }

    if (!empty($errors)) {
        set_transient('service_ticket_manager_errors', $errors, HOUR_IN_SECONDS);
    }

}
add_action( 'save_post', 'save_service_ticket_details' );
