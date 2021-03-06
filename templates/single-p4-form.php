<?php
/**
 * Part of the Planet4 Form Builder.
 */

namespace P4FB\Form_Builder\Templates;

use Timber\Post;
use Timber\Timber;

$context     = Timber::get_context();
$form_id     = get_query_var( 'form_id' );
$form_errors = get_option( 'p4fb_submission_errors_' . $form_id );
if ( ! empty( $form_errors ) ) {
	$context['form_errors'] = $form_errors;
}
update_option('p4fb_submission_errors_' . $form_id, [] );

// If called from the shortcode, the post is already set.
if ( empty( $context['post'] ) ) {
	$context['post'] = new Post( $form_id );
}
$context['form_submit_url']  = admin_url( 'admin-post.php' );
$context['action']           = P4FB_FORM_ACTION;
$context['nonce_action']     = P4FB_FORM_ACTION . '-' . $context['post']->ID;
$context['nonce_name']       = P4FB_FORM_NONCE;
$context['required_message'] = __( '(* required)', 'planet4-form-builder' );

// process the field options here for easier rendering
foreach ( $context['post']->p4_form_fields as $index => $field ) {
	if ( ( 'select' === $field['type'] ) || ( 'checkbox-group' === $field['type'] ) || ( 'radio-group' === $field['type'] ) ) {
		$options     = $field['options'];
		$options     = explode( "\n", $options );
		$new_options = [];
		foreach ( $options as $option ) {
			if ( empty( trim( $option ) ) ) {
				continue;
			}
			$parts = explode( '|', $option, 2 );
			if ( count( $parts ) > 1 ) {
				$new_options[ trim( $parts[0] ) ] = trim( $parts[1] );
			} else {
				$new_options[ trim( $option ) ] = trim( $option );
			}
		}
		$context['post']->p4_form_fields[ $index ]['options'] = $new_options;
	}
}

// @Todo: Derive/set $context['current_value']?
// have multi checkbox as array of selected values ?(even if only one)

Timber::render(
	[
		'single-' . $context['post']->post_type . '.twig',
		//'single.twig',
	],
	$context
);
