<?php

/**
 * Template Helpers Functions
 *
 * @package     Fulcrum\Custom\Template
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+ and MIT Licence (MIT)
 */

if ( ! function_exists( 'fulcrum_load_template' ) ) {
	/**
	 * Load the specified template
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_file         Template filename with extension
	 * @param string $path                  Template path.  Defaults to plugin template folder
	 * @param array $local_variables        Variables to have in scope for the template file
	 */
	function fulcrum_load_template( $template_file, $path = '', $local_variables = array() ) {

		$path       = $path ?: FULCRUM_PLUGIN_DIR . 'lib/templates/';
		$template   = $path . $template_file;

		if ( $template && is_readable( $template ) ) {
			extract ( $local_variables );

			include( $template );
		}
	}
}

if ( ! function_exists( 'fulcrum_get_template_file' ) ) {
	/**
	 * Get the page's template file, if there is one
	 *
	 * @since 1.0.0
	 *
	 * @param int       $post_id        (optional) Post ID to fetch the template file for.
	 * @return string
	 */
	function fulcrum_get_template_file( $post_id = 0 ) {

		$post_id = fulcrum_get_post_id( $post_id );

		return $post_id > 0 ? get_post_meta( $post_id, '_wp_page_template', true ) : '';
	}
}