<?php

// Function to create custom roles and adjust capabilities
function create_service_ticket_roles_and_capabilities() {
    // Define Dispatcher capabilities
    $dispatcher_capabilities = array(
        'read' => true,
        'edit_posts' => true,  // Assuming 'posts' refers to Service Tickets
        'edit_others_posts' => false,  // Adjust as needed
        'publish_posts' => false,  // Adjust as needed
        'read_private_posts' => true,  // Allows reading of private tickets
        // Custom capability for managing and editing tickets
        'manage_tickets' => true,
        'edit_tickets' => true,
        'delete_tickets' => false,  // Dispatchers cannot delete tickets
        'update_ticket_notes' => true,  // Custom capability for updating ticket notes
        'assign_ticket_manager' => true,  // Dispatchers cannot assign managers
    );

    // Add or update the Dispatcher role with the defined capabilities
    add_role('dispatch', 'Service Ticket Dispatcher', $dispatcher_capabilities);

    // Manager capabilities - Inherits Dispatcher capabilities and adds more
    $manager_capabilities = array_merge($dispatcher_capabilities, array(
        'edit_others_posts' => true,  // Managers can edit all tickets
        'delete_posts' => true,  // Managers can delete tickets
        'publish_posts' => true,  // Managers can publish tickets
        'read_private_posts' => true,  // Managers can read private tickets
        // Custom capability for managing ticket reports
        'manage_ticket_reports' => true,

    ));

    // Add or update the Manager role with the defined capabilities
    add_role('manager', 'Service Ticket Manager', $manager_capabilities);
}

// Register the roles and capabilities upon plugin activation
function service_ticket_plugin_activation() {
    create_service_ticket_roles_and_capabilities();
    // Additional activation code can go here
}

register_activation_hook(__FILE__, 'service_ticket_plugin_activation');

// Optionally, you might want to clean up roles on plugin deactivation
function service_ticket_plugin_deactivation() {
    remove_role('dispatch');
    remove_role('manager');
    // Additional deactivation code can go here
}

register_deactivation_hook(__FILE__, 'service_ticket_plugin_deactivation');
