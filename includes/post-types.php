<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$i18n = 'seclgroup-team-members';

if ( ! post_type_exists('team_members') ) :

register_post_type( 'team_members', array(
    'label'                 => __( 'Team Member', $i18n ),
    'description'           => __( 'Team Member Description', $i18n ),
    'labels'                => array(
        'name'                  => __( 'Team Members', $i18n ),
        'singular_name'         => __( 'Team Member', $i18n ),
        'menu_name'             => __( 'Team Members', $i18n ),
        'name_admin_bar'        => __( 'Team Member', $i18n ),
        'archives'              => __( 'Item Archives', $i18n ),
        'attributes'            => __( 'Item Attributes', $i18n ),
        'parent_item_colon'     => __( 'Parent Item:', $i18n ),
        'all_items'             => __( 'All Items', $i18n ),
        'add_new_item'          => __( 'Add New Item', $i18n ),
        'add_new'               => __( 'Add New', $i18n ),
        'new_item'              => __( 'New Item', $i18n ),
        'edit_item'             => __( 'Edit Item', $i18n ),
        'update_item'           => __( 'Update Item', $i18n ),
        'view_item'             => __( 'View Item', $i18n ),
        'view_items'            => __( 'View Items', $i18n ),
        'search_items'          => __( 'Search Item', $i18n ),
        'not_found'             => __( 'Not found', $i18n ),
        'not_found_in_trash'    => __( 'Not found in Trash', $i18n ),
        'featured_image'        => __( 'Featured Image', $i18n ),
        'set_featured_image'    => __( 'Set featured image', $i18n ),
        'remove_featured_image' => __( 'Remove featured image', $i18n ),
        'use_featured_image'    => __( 'Use as featured image', $i18n ),
        'insert_into_item'      => __( 'Insert into item', $i18n ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', $i18n ),
        'items_list'            => __( 'Items list', $i18n ),
        'items_list_navigation' => __( 'Items list navigation', $i18n ),
        'filter_items_list'     => __( 'Filter items list', $i18n ),
    ),
    'supports'              => array('title', 'editor'),
    'taxonomies'            => array(),
    'hierarchical'          => false,
    'public'                => true,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'show_in_rest'          => true,
    'menu_position'         => 70,
    'menu_icon'             => 'dashicons-id-alt',
    'show_in_admin_bar'     => false,
    'show_in_nav_menus'     => false,
    'can_export'            => true,
    'has_archive'           => true,
    'exclude_from_search'   => false,
    'publicly_queryable'    => true,
    'capability_type'       => 'page',
    'rewrite'               => array(
        'slug' => 'team-members',
        'with_front' => false
    ),
) );

endif;
