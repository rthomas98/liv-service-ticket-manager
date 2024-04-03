<?php

// Function to create custom roles
function create_service_ticket_roles() {
add_role( 'manager', 'Service Ticket Manager', array(
'read'                  => true,
'edit_posts'            => true,   // Assumes your tickets rely on 'posts'
'edit_others_posts'     => true,    // Can Edit ALL Tickets
'publish_posts'         => true,     // Maybe if they can 'finalize' a ticket
'manage_ticket_reports' => true   // Example CUSTOM capability we'll talk about soon
) );

add_role( 'dispatch', 'Service Ticket Dispatcher', array(
'read'              => true,
'edit_posts'        => true,  // Able to modify ticket data
'update_ticket_notes' => true   // Assume this might be a custom cap as well
) );
}

register_activation_hook(__FILE__, 'service_ticket_plugin_activation');

// Optionally, you might want to clean up roles on plugin deactivation
function service_ticket_plugin_deactivation() {
    remove_role('dispatch');
    remove_role('manager');
    // Additional deactivation code can go here
}

register_deactivation_hook(__FILE__, 'service_ticket_plugin_deactivation');
