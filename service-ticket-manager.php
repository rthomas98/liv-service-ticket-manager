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

//  Function to add the meta box container
function service_ticket_metabox() {
    add_meta_box(
        'service_ticket_details',
        __( 'Service Ticket Details', 'service-ticket-manager' ),
        'display_service_ticket_form_fields',
        'service_ticket', // Post type
        'normal',      // Display position ('normal', 'side'..)
        'high'         // Priority ('high', 'low'...)
    );
}
add_action( 'add_meta_boxes', 'service_ticket_metabox' );

function display_service_ticket_form_fields( $post ) {
    // Add a nonce field for security validation
    wp_nonce_field( 'service_ticket_form_nonce', 'service_ticket_metabox_nonce' );

    // Example Field: Customer Name (add more as needed)
    $customer_name = get_post_meta( $post->ID, '_customer_name', true ); // Retrieve if data exists
    ?>
    <h3>Customer Information</h3>
    <p>
        <label for="customer_name"><?php _e( 'Customer Name', 'service-ticket-manager' ); ?></label><br>
        <input type="text" name="customer_name" id="customer_name" value="<?php echo esc_attr( $customer_name ); ?>" class="widefat" />
    </p>

    <h3>Customer Account Number</h3>

    <p>
        <label for="customer_account_number"><?php _e( 'Account Number', 'service-ticket-manager' ); ?></label><br>
        <input type="text" name="customer_account_number" id="customer_account_number" value="<?php echo esc_attr( get_post_meta( $post->ID, '_customer_account_number', true ) ); ?>" class="widefat" />
    </p>

    <p>
        <label for="customer_city"><?php _e( 'City', 'service-ticket-manager' ); ?></label><br>
        <input type="text" name="customer_city" id="customer_city" value="<?php echo esc_attr( get_post_meta( $post->ID, '_customer_city', true ) ); ?>" class="widefat" />
    </p>
    <p>
        <label for="customer_state"><?php _e( 'State', 'service-ticket-manager' ); ?></label><br>
        <input type="text" name="customer_state" id="customer_state" value="<?php echo esc_attr( get_post_meta( $post->ID, '_customer_state', true ) ); ?>" class="widefat" />
    </p>
    <p>
        <label for="customer_zip"><?php _e( 'Zip Code', 'service-ticket-manager' ); ?></label><br>
        <input type="text" name="customer_zip" id="customer_zip" value="<?php echo esc_attr( get_post_meta( $post->ID, '_customer_zip', true ) ); ?>" class="widefat" />
    </p>
    <h3>Contact Number:</h3>
    <p>
        <label for="customer_phone"><?php _e( 'Phone', 'service-ticket-manager' ); ?></label><br>
        <input type="text" name="customer_phone" id="customer_phone" value="<?php echo esc_attr( get_post_meta( $post->ID, '_customer_phone', true ) ); ?>" class="widefat" />
    </p>

    <h3>Email Address</h3>
    <p>
        <label for="customer_email"><?php _e( 'Email', 'service-ticket-manager' ); ?></label><br>
        <input type="email" name="customer_email" id="customer_email" value="<?php echo esc_attr( get_post_meta( $post->ID, '_customer_email', true ) ); ?>" class="widefat" />

    <h3>Service Details</h3>
    <p>
        <label for="date_of_services"><?php _e( 'Date of Services', 'service-ticket-manager' ); ?></label><br>
        <input type="date" name="date_of_services" id="date_of_services" value="<?php echo esc_attr( $date_of_services ); ?>" class="widefat" />
    </p>
    <h3>Description of Services</h3>
    <p>
        <label for="description_of_services"><?php _e( 'Description of Services', 'service-ticket-manager' ); ?></label><br>
        <textarea name="description_of_services" id="description_of_services" class="widefat"><?php echo esc_attr( get_post_meta( $post->ID, '_description_of_services', true ) ); ?></textarea>
    </p>

    <h3>Delivery Details</h3>
    <p>
        <label for="delivery_start"><?php _e( 'Delivery Start Location', 'service-ticket-manager' ); ?></label><br>
        <input type="text" name="delivery_start" id="delivery_start" value="<?php echo esc_attr( get_post_meta( $post->ID, '_delivery_start', true ) ); ?>" class="widefat" />
    </p>
    <p>
        <label for="delivery_destination"><?php _e( 'Delivery Destination', 'service-ticket-manager' ); ?></label><br>
        <input type="text" name="delivery_destination" id="delivery_destination" value="<?php echo esc_attr( get_post_meta( $post->ID, '_delivery_destination', true ) ); ?>" class="widefat" />
    </p>
    <p>
        <label for="unit_price"><?php _e( 'Unit Price', 'service-ticket-manager' ); ?></label><br>
        <input type="number" name="unit_price" id="unit_price" value="<?php echo esc_attr( get_post_meta( $post->ID, '_unit_price', true ) ); ?>" class="widefat" />
    </p>
    <p>
        <label for="extended_price"><?php _e( 'Extended Price', 'service-ticket-manager' ); ?></label><br>
        <input type="number" name="extended_price" id="extended_price" value="<?php echo esc_attr( get_post_meta( $post->ID, '_extended_price', true ) ); ?>" class="widefat" />
    </p>

    <h3>Subcontractor/Driver Details</h3>
    <p>
        <label for="driver_name"><?php _e( 'Name', 'service-ticket-manager' ); ?></label><br>
        <input type="text" name="driver_name" id="driver_name" value="<?php echo esc_attr( get_post_meta( $post->ID, '_driver_name', true ) ); ?>" class="widefat" />
    </p>
    <p>
        <label for="driver_address"><?php _e( 'Street Address', 'service-ticket-manager' ); ?></label><br>
        <input type="text" name="driver_address" id="driver_address" value="<?php echo esc_attr( get_post_meta( $post->ID, '_driver_address', true ) ); ?>" class="widefat" />
    </p>
    <p>
        <label for="driver_contact"><?php _e( 'Contact Number', 'service-ticket-manager' ); ?></label><br>
        <input type="tel" name="driver_contact" id="driver_contact" value="<?php echo esc_attr( get_post_meta( $post->ID, '_driver_contact', true ) ); ?>" class="widefat" />
    </p>
    <p>
        <label for="driver_email"><?php _e( 'Email Address (optional)', 'service-ticket-manager' ); ?></label><br>
        <input type="email" name="driver_email" id="driver_email" value="<?php echo esc_attr( get_post_meta( $post->ID, '_driver_email', true ) ); ?>" class="widefat" />
    </p>

    <h3>Manager/Dispatch</h3>
    <p>
        <label for="manager_name"><?php _e( 'Manager/Dispatcher Name', 'service-ticket-manager' ); ?></label><br>
        <input type="text" name="manager_name" id="manager_name" value="<?php echo esc_attr( get_post_meta( $post->ID, '_manager_name', true ) ); ?>" class="widefat" />
    </p>

    <h3>Status</h3>
    <p>
        <label for="ticket_status"><?php _e( 'Status', 'service-ticket-manager' ); ?></label><br>
        <select name="ticket_status" id="ticket_status">
            <option value="open" <?php selected( get_post_meta( $post->ID, '_ticket_status', true ), 'open' ); ?>><?php _e( 'Open', 'service-ticket-manager' ); ?></option>
            <option value="in_progress" <?php selected( get_post_meta( $post->ID, '_ticket_status', true ), 'in_progress' ); ?>><?php _e( 'In Progress', 'service-ticket-manager' ); ?></option>
            <option value="closed" <?php selected( get_post_meta( $post->ID, '_ticket_status', true ), 'closed' ); ?>><?php _e( 'Closed', 'service-ticket-manager' ); ?></option>
        </select>
    </p>

    <h3>Notes</h3>
    <p>
        <label for="ticket_notes"><?php _e( 'Notes', 'service-ticket-manager' ); ?></label><br>
        <textarea name="ticket_notes" id="ticket_notes" class="widefat"><?php echo esc_textarea( get_post_meta( $post->ID, '_ticket_notes', true ) ); ?></textarea>
    </p>
    <?php
}

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

    // Prices
    if ( isset( $_POST['unit_price'] ) ) {
        update_post_meta( $post_id, '_unit_price', floatval( $_POST['unit_price'] ) );
    }
    if ( isset( $_POST['extended_price'] ) ) {
        update_post_meta( $post_id, '_extended_price', floatval( $_POST['extended_price'] ) );
    }

    // Driver Details (Similar to customer)
    // ...

    // Manager/Dispatch, Status, Notes
    if ( isset( $_POST['manager_name'] ) ) {
        update_post_meta( $post_id, '_manager_name', sanitize_text_field( $_POST['manager_name'] ) );
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


