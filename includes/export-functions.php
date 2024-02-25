<?php
// Add a new submenu for exporting service tickets
function service_ticket_manager_add_export_submenu() {
    add_submenu_page(
        'edit.php?post_type=service_ticket', // Ensure this points to a valid parent slug
        'Export Service Tickets',
        'Export Tickets',
        'manage_options',
        'export-service-tickets',
        'service_ticket_manager_export_page_callback'
    );
}
add_action('admin_menu', 'service_ticket_manager_add_export_submenu');

// Callback function for the export page
function service_ticket_manager_export_page_callback() {

    if (isset($_GET['error']) && $_GET['error'] === 'no-tickets-selected') {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Please select at least one ticket to export.', 'service-ticket-manager') . '</p></div>';
    }

    ?>
    <div class="wrap">
        <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
        <p><?php _e('Select service tickets to export to a CSV file.', 'service-ticket-manager'); ?></p>

        <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
            <input type="hidden" name="action" value="export_service_tickets">
            <?php wp_nonce_field('export_service_tickets_nonce', 'export_service_tickets_nonce_field'); ?>

            <table class="widefat fixed" cellspacing="0">
                <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column" scope="col">
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <th class="manage-column" scope="col"><?php _e('Ticket ID', 'service-ticket-manager'); ?></th>
                    <th class="manage-column" scope="col"><?php _e('Customer Name', 'service-ticket-manager'); ?></th>
                    <th class="manage-column" scope="col"><?php _e('Date of Services', 'service-ticket-manager'); ?></th>
                    <th class="manage-column" scope="col"><?php _e('Ticket Status', 'service-ticket-manager'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $args = array(
                    'post_type' => 'service_ticket',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                );
                $query = new WP_Query($args);
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        ?>
                        <tr>
                            <th class="check-column" scope="row">
                                <input type="checkbox" name="ticket_ids[]" value="<?php echo esc_attr(get_the_ID()); ?>">
                            </th>
                            <td><?php the_ID(); ?></td>
                            <td><?php echo esc_html(get_post_meta(get_the_ID(), '_customer_name', true)); ?></td>
                            <td><?php echo esc_html(get_post_meta(get_the_ID(), '_date_of_services', true)); ?></td>
                            <td><?php echo esc_html(get_post_meta(get_the_ID(), '_ticket_status', true)); ?></td>
                        </tr>
                        <?php
                    }
                    wp_reset_postdata();
                }
                ?>
                </tbody>
            </table>

            <input type="submit" class="button-primary" value="<?php _e('Export Selected Tickets', 'service-ticket-manager'); ?>">
        </form>
    </div>
    <?php
}


// Handle the export action
function service_ticket_manager_handle_export_action() {



    if (!current_user_can('manage_options') ||
        !isset($_POST['export_service_tickets_nonce_field']) ||
        !wp_verify_nonce($_POST['export_service_tickets_nonce_field'], 'export_service_tickets_nonce')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Check if any tickets were selected
    if (empty($_POST['ticket_ids']) || !is_array($_POST['ticket_ids'])) {
        // Redirect back to the export page with an error message
        $redirect_url = add_query_arg(array(
            'page' => 'export-service-tickets',
            'error' => 'no-tickets-selected',
        ), admin_url('admin.php'));
        wp_redirect($redirect_url);
        exit;
    }

    $filename = 'service-tickets-' . date('Ymd') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fputcsv($output, [
        'Ticket ID', 'Customer Name', 'Account Number', 'City', 'State', 'Zip Code', 'Email',
        'Date of Services', 'Description of Services', 'Delivery Start', 'Delivery Destination',
        'Unit Price', 'Extended Price', 'Driver Name', 'Driver Contact', 'Driver Email',
        'Manager/Dispatcher ID', 'Ticket Status', 'Ticket Notes'
    ]);

    if (!empty($_POST['ticket_ids']) && is_array($_POST['ticket_ids'])) {
        $ticket_ids = array_map('intval', $_POST['ticket_ids']); // Sanitize each ID
    } else {
        wp_die(__('No tickets selected for export.', 'service-ticket-manager'));
    }

    $args = array(
        'post_type' => 'service_ticket',
        'post__in' => $ticket_ids, // Only export selected tickets
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();

            // Fetch all fields using get_post_meta
            $customer_name = get_post_meta($post_id, '_customer_name', true);
            $customer_account_number = get_post_meta($post_id, '_customer_account_number', true);
            // Add other fields in similar fashion...
            $customer_city = get_post_meta($post_id, '_customer_city', true);
            $customer_state = get_post_meta($post_id, '_customer_state', true);
            $customer_zip = get_post_meta($post_id, '_customer_zip', true);
            $customer_email = get_post_meta($post_id, '_customer_email', true);
            $date_of_services = get_post_meta($post_id, '_date_of_services', true);
            $description_of_services = get_post_meta($post_id, '_description_of_services', true);
            $delivery_start = get_post_meta($post_id, '_delivery_start', true);
            $delivery_destination = get_post_meta($post_id, '_delivery_destination', true);
            $unit_price = get_post_meta($post_id, '_unit_price', true);
            $extended_price = get_post_meta($post_id, '_extended_price', true);
            $driver_name = get_post_meta($post_id, '_driver_name', true);
            $driver_contact = get_post_meta($post_id, '_driver_contact', true);
            $driver_email = get_post_meta($post_id, '_driver_email', true);
            $manager_id = get_post_meta($post_id, '_manager_id', true);
            $ticket_status = get_post_meta($post_id, '_ticket_status', true);
            $ticket_notes = get_post_meta($post_id, '_ticket_notes', true);

            // Write the fetched data to CSV
            fputcsv($output, [
                $post_id, $customer_name, $customer_account_number, $customer_city, $customer_state,
                $customer_zip, $customer_email, $date_of_services, $description_of_services,
                $delivery_start, $delivery_destination, $unit_price, $extended_price, $driver_name,
                $driver_contact, $driver_email, $manager_id, $ticket_status, $ticket_notes
            ]);
        }
        wp_reset_postdata();
    }

    fclose($output);
    exit;
}
add_action('admin_post_export_service_tickets', 'service_ticket_manager_handle_export_action');
