<?php

// Function to save ticket details
function save_service_ticket_details( $post_id ) {

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
    if ( isset( $_POST['customer_name'] ) ) {
        update_post_meta( $post_id, '_customer_name', sanitize_text_field( $_POST['customer_name'] ) );
    }
    if ( isset( $_POST['customer_account_number'] ) ) {
        update_post_meta( $post_id, '_customer_account_number', sanitize_text_field( $_POST['customer_account_number'] ) ); // Text to prevent accidental modification if truly meant to be text
    }
    if ( isset( $_POST['customer_city'] ) ) {
        update_post_meta( $post_id, '_customer_city', sanitize_text_field( $_POST['customer_city'] ) );
    }
    if ( isset( $_POST['customer_state'] ) ) {
        update_post_meta( $post_id, '_customer_state', sanitize_text_field( $_POST['customer_state'] ) );
    }
    if ( isset( $_POST['customer_zip'] ) ) {
        update_post_meta( $post_id, '_customer_zip', sanitize_text_field( $_POST['customer_zip'] ) );
    }
    if ( isset( $_POST['customer_phone'] ) ) {
        update_post_meta( $post_id, '_customer_phone', sanitize_text_field( $_POST['customer_phone'] ) ); // Consider a phone-specific check if there's a strict format
    }
    if ( isset( $_POST['customer_email'] ) ) {
        update_post_meta( $post_id, '_customer_email', sanitize_email( $_POST['customer_email'] ) );
    }

    // Service Details
    if ( isset( $_POST['date_of_services'] ) ) {
        update_post_meta( $post_id, '_date_of_services', sanitize_text_field( $_POST['date_of_services'] ) );
    }
    if ( isset( $_POST['description_of_services'] ) ) {
        update_post_meta( $post_id, '_description_of_services', wp_kses_post( $_POST['description_of_services'] ) );
    }

    // Delivery Details (same pattern as customer fields)
    // ...

    if ( isset( $_POST['delivery_start'] ) ) {
        update_post_meta( $post_id, '_delivery_start', sanitize_text_field( $_POST['delivery_start'] ) );
    }
    if ( isset( $_POST['delivery_destination'] ) ) {
        update_post_meta( $post_id, '_delivery_destination', sanitize_text_field( $_POST['delivery_destination'] ) );
    }

    // Prices
    if ( isset( $_POST['unit_price'] ) ) {
        update_post_meta( $post_id, '_unit_price', floatval( $_POST['unit_price'] ) );
    }
    if ( isset( $_POST['extended_price'] ) ) {
        update_post_meta( $post_id, '_extended_price', floatval( $_POST['extended_price'] ) );
    }

    // Subcontractor/Driver Details (Similar to customer)
    // ...

    if ( isset( $_POST['driver_name'] ) ) {
        update_post_meta( $post_id, '_driver_name', sanitize_text_field( $_POST['driver_name'] ) );
    }
    if ( isset( $_POST['driver_address'] ) ) {
        update_post_meta( $post_id, '_driver_address', sanitize_text_field( $_POST['driver_address'] ) );
    }
    if ( isset( $_POST['driver_contact'] ) ) {
        update_post_meta( $post_id, '_driver_contact', sanitize_text_field( $_POST['driver_contact'] ) );
    }
    if ( isset( $_POST['driver_email'] ) ) {
        update_post_meta( $post_id, '_driver_email', sanitize_email( $_POST['driver_email'] ) );
    }


    // Driver Details (Similar to customer)
    // ...

    // Manager/Dispatch, Status, Notes
    if ( isset( $_POST['manager_id'] ) ) {
        update_post_meta( $post_id, '_manager_id', absint( $_POST['manager_id'] ) ); // Use absint for integer sanitation
    }
    if ( isset( $_POST['ticket_status'] ) ) {
        $allowed_statuses = array( 'open', 'in_progress', 'closed' );
        $submitted_status = sanitize_text_field( $_POST['ticket_status'] );
        if ( in_array( $submitted_status, $allowed_statuses ) ) {
            update_post_meta( $post_id, '_ticket_status', $submitted_status );
        } else {
            //  Default to a safe status if someone messes with submitted data?
            update_post_meta( $post_id, '_ticket_status', 'open' );
        }
    }
    if ( isset( $_POST['ticket_notes'] ) ) {
        update_post_meta( $post_id, '_ticket_notes', sanitize_textarea_field( $_POST['ticket_notes'] ) );
    }
}
add_action( 'save_post', 'save_service_ticket_details' );

include_once( plugin_dir_path( __FILE__ ) . 'roles.php' );
include_once( plugin_dir_path( __FILE__ ) . 'meta-boxes.php' );
include_once( plugin_dir_path( __FILE__ ) . 'data-handling.php' );
