<?php

/**
 * Meta Helpers Functions
 *
 * @package     Fulcrum\Custom\Meta
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+ and MIT Licence (MIT)
 */

if ( ! function_exists( 'fulcrum_get_meta') ) {
	/**
	 * Get the meta
	 *
	 * @since 1.0.0
	 *
	 * @param string    $meta_key
	 * @param string    $meta_subkey
	 * @param int       $post_id
	 * @param bool      $single
	 * @return mixed
	 */
	function fulcrum_get_meta( $meta_key, $meta_subkey = '', $post_id = 0, $single = false ) {
		global $post;

		static $meta_cache = array();

		$post_id = $post_id > 0 ? $post_id : $post->ID;

		if ( isset( $meta_cache[ $post_id ] ) && array_key_exists( $meta_key, $meta_cache[ $post_id ] ) ) {

			$meta = $meta_cache[ $post_id ][ $meta_key ];

		} else {
			$meta = get_post_meta( $post_id, $meta_key, $single );
			$meta_cache[ $post_id ][ $meta_key ] = is_array( $meta ) && isset( $meta[0] ) && ! $single ? $meta[0] : $meta;
		}

		return $meta_subkey && is_array( $meta_cache[ $post_id ][ $meta_key ] ) && array_key_exists( $meta_subkey, $meta_cache[ $post_id ][ $meta_key ] )
			? $meta_cache[ $post_id ][ $meta_key ][ $meta_subkey ]
			: $meta;
	}
}
