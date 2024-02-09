<?php

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
        <?php
        // Arguments for our user dropdown
        $dropdown_args = array(
            'name'             => 'manager_id',
            'id'               => 'manager_id',
            'show_option_none' => __( 'Select a Manager/Dispatcher', 'service-ticket-manager' ),
            'selected'         => get_post_meta( $post->ID, '_manager_id', true ),
            'role__in'         => array( 'manager', 'dispatch' ) // Key Change here!
        );

        // Function to generate the dropdown
        wp_dropdown_users( $dropdown_args );
        ?>
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


