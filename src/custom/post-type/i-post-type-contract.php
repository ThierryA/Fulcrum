<?php

/**
 * Post Type Contract
 *
 * This class handles a custom post type object.
 *
 * @package     Fulcrum\Custom\Post_Type
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Post_Type;

interface Post_Type_Contract {

	/**
	 * Register Custom Post Type
	 *
	 * @since 1.0.0
	 *
	 * @uses self::build_args() Builds up the needed args from defaults & configuration
	 *
	 * @return void
	 */
	public function register();

	/**
	 * Modify the columns for this custom post type
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Array of Columns
	 *
	 * @return array                Amended Array
	 */
	public function columns_filter( $columns );

	/**
	 * Filter the data that shows up in the columns
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name The name of the column to display.
	 * @param int $post_id The current post ID.
	 *
	 * @return null
	 *
	 * @throws Configuration_Exception
	 */
	public function columns_data( $column_name, $post_id );

	/**
	 * Filter for making the columns sortable
	 *
	 * @since  1.0.0
	 *
	 * @param  array $sortable_columns Sortable columns
	 *
	 * @return array                    Amended $sortable_columns
	 */
	public function make_columns_sortable( $sortable_columns );

	/**
	 * Sort columns by the configuration
	 *
	 * @since 1.0.0
	 *
	 * @param $vars
	 *
	 * @return mixed
	 */
	public function sort_columns_by( $vars );

	/**
	 * Handles adding (or removing) this CPT to/from the RSS Feed
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_vars Query variables from parse_request
	 *
	 * @return array    $query_vars
	 */
	public function add_or_remove_to_from_rss_feed( $query_vars );

	/**
	 * Get all of the supports
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_the_supports();
}
