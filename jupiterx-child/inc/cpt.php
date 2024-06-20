<?php
/**
 * WP Default Custom Post Types Sample
 *
 * @package WP_Default
 */

function sota_reviews_create_post_type() {
	$labels = array(
		'name'					=> __( 'SOTA Reviews', 'scwd' ),
		'singular_name'			=> __( 'SOTA Review', 'scwd' ),
		'add_new'				=> __( 'Add New', 'scwd' ),
		'add_new_item'			=> __( 'Add New', 'scwd' ),
		'edit_item'				=> __( 'Edit SOTA Review', 'scwd' ),
		'new_item'				=> __( 'New SOTA Review', 'scwd' ),
		'view_item'				=> __( 'View SOTA Review', 'scwd' ),
		'search_items'			=> __( 'Search SOTA Reviews', 'scwd' ),
		'not_found'				=>  __( 'No SOTA Reviews Found', 'scwd' ),
		'not_found_in_trash'	=> __( 'No SOTA Reviews found in Trash', 'scwd' ),
	);
	$args = array(
		'labels'		=> $labels,
		'has_archive'	=> false,
		'public'		=> true,
		'hierarchical'	=> false,
		'rewrite'		=> array( 'slug' => 'sota-reviews' ),
		'supports'		=> array(
			'title',
			'editor',
			'thumbnail',
		)
	);
	register_post_type( 'sota-review', $args );
}
add_action( 'init', 'sota_reviews_create_post_type' );