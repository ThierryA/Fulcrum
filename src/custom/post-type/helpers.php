<?php

/**
 * Post Type Helpers Functions
 *
 * @package     Fulcrum\Custom\Post_Type
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+ and MIT Licence (MIT)
 */

if ( ! function_exists( 'fulcrum_get_all_supports_for_post_type' ) ) {
	/**
	 * Get all of the supports for the given post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Post type to fetch the supports for.
	 * @param bool $keys_only Flag to indicate whether to return only the supports.
	 *
	 * @return array
	 */
	function fulcrum_get_all_supports_for_post_type( $type, $keys_only = true ) {
		$enabled_post_types = get_all_post_type_supports( $type );

		if ( $keys_only ) {
			return array_keys( $enabled_post_types );
		}

		return $enabled_post_types;
	}
}

if ( ! function_exists( 'fulcrum_get_all_post_types' ) ) {
	/**
	 * Gets all the of the "post" types, which includes Custom Post
	 * Types and the builtin 'post'.
	 *
	 * @since 1.1.0
	 *
	 * @param bool $include_builtin_post True - includes the builtin 'post';
	 *                                      False - only custom post types.
	 *
	 * @return array
	 */
	function fulcrum_get_all_post_types( $include_builtin_post = true ) {
		$custom_post_types = get_post_types(
			array(
				'_builtin' => false,
			)
		);

		if ( $include_builtin_post ) {
			$custom_post_types['post'] = 'post';
		}

		return $custom_post_types;
	}
}
