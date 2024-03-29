<?php

function register_service_ticket_taxonomies() {
    // Define the text domain for translations
    $textdomain = 'service-ticket-manager';

    // Service Types taxonomy labels and args
    $service_type_labels = array(
        'name'                       => _x('Service Types', 'taxonomy general name', $textdomain),
        'singular_name'              => _x('Service Type', 'taxonomy singular name', $textdomain),
        'search_items'               => __('Search Service Types', $textdomain),
        'all_items'                  => __('All Service Types', $textdomain),
        'parent_item'                => __('Parent Service Type', $textdomain),
        'parent_item_colon'          => __('Parent Service Type:', $textdomain),
        'edit_item'                  => __('Edit Service Type', $textdomain),
        'update_item'                => __('Update Service Type', $textdomain),
        'add_new_item'               => __('Add New Service Type', $textdomain),
        'new_item_name'              => __('New Service Type Name', $textdomain),
        'menu_name'                  => __('Service Types', $textdomain),
    );
    $service_type_args = array(
        'hierarchical'      => true,
        'labels'            => $service_type_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'service-type'),
    );
    register_taxonomy('service_type', array('service_ticket'), $service_type_args);

    // Priority Levels taxonomy labels and args
    $priority_level_labels = array(
        'name'                       => _x('Priority Levels', 'taxonomy general name', $textdomain),
        'singular_name'              => _x('Priority Level', 'taxonomy singular name', $textdomain),
        'search_items'               => __('Search Priority Levels', $textdomain),
        'popular_items'              => __('Popular Priority Levels', $textdomain),
        'all_items'                  => __('All Priority Levels', $textdomain),
        'edit_item'                  => __('Edit Priority Level', $textdomain),
        'update_item'                => __('Update Priority Level', $textdomain),
        'add_new_item'               => __('Add New Priority Level', $textdomain),
        'new_item_name'              => __('New Priority Level Name', $textdomain),
        'separate_items_with_commas' => __('Separate priority levels with commas', $textdomain),
        'add_or_remove_items'        => __('Add or remove priority levels', $textdomain),
        'choose_from_most_used'      => __('Choose from the most used priority levels', $textdomain),
        'not_found'                  => __('No priority levels found.', $textdomain),
        'menu_name'                  => __('Priority Levels', $textdomain),
    );
    $priority_level_args = array(
        'hierarchical'          => false,
        'labels'                => $priority_level_labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array('slug' => 'priority-level'),
    );
    register_taxonomy('priority_level', 'service_ticket', $priority_level_args);
}

add_action('init', 'register_service_ticket_taxonomies');
