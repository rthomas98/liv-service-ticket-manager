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
