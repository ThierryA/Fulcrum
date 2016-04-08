<?php

/**
 * Taxonomy Helpers Functions
 *
 * @package     Fulcrum\Custom\Taxonomy
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+ and MIT Licence (MIT)
 */

if ( ! function_exists( 'fulcrum_get_joined_list_of_terms' ) ) {
	/**
	 * Get a joined list of all the terms for the requested
	 * post ID and term name
	 *
	 * @since 1.0.0
	 *
	 * @param string $taxonomy
	 * @param integer $post_id
	 *
	 * @return string       Returns the list of terms or ''
	 */
	function fulcrum_get_joined_list_of_terms( $taxonomy, $post_id ) {

		if ( ! $taxonomy || $post_id < 1 ) {
			return '';
		}

		$terms = get_the_terms( (int) $post_id, $taxonomy );
		if ( fulcrum_are_terms_present( $terms ) ) {
			$terms_arr = array();
			foreach ( $terms as $term ) {
				$terms_arr[] = $term->name;
			}

			return join( ', ', $terms_arr );
		}

		return '';
	}
}

if ( ! function_exists( 'fulcrum_are_terms_present' ) ) {
	/**
	 * Checks if the terms are present.
	 *
	 * @since 1.0.0
	 *
	 * @param array|false|WP_Error $terms Array of term objects on success, false if there are no terms, or the post does not exist, WP_Error on failure.
	 *
	 * @return string
	 */
	function fulcrum_are_terms_present( $terms ) {
		return $terms && ! is_wp_error( $terms );
	}
}

add_filter( 'terms_clauses', 'fulcrum_add_post_type_to_terms_sql', 99999, 3 );
/**
 * This callback adds the post type SQL queries to the terms clauses, as we want to only
 * grab the terms for this specific post type.
 *
 * @since 1.0.0
 *
 * @param array $clauses Terms query SQL clauses.
 * @param array $taxonomies An array of taxonomies.
 * @param array $args An array of terms query arguments.
 *
 * @return mixed
 */
function fulcrum_add_post_type_to_terms_sql( $clauses, $taxonomies, $args ) {
	if ( ! array_key_exists( 'post_type', $args ) || ! $args['post_type'] ) {
		return $clauses;
	}

	$post_types = fulcrum_flatten_array_into_delimited_list( $args['post_type'] );

	global $wpdb;

	$clauses['join'] .= " INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN $wpdb->posts AS p ON p.ID = tr.object_id";
	$clauses['where'] .= $wpdb->prepare( " AND p.post_type IN ( %s ) GROUP BY t.term_id", $post_types );

	return $clauses;
}