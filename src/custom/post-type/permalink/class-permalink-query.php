<?php
/**
 * Handler for rewrites and WP_Query for custom post type when you want to put
 * the term into the URL, e.g. domain.com/custom_post_type/term/postname.
 *
 * This handler does the following:
 *
 * 1. Allows duplicate postname slugs when in different terms but same custom_post_type.
 * 2.
 *
 * Note: The extra steps for manipulating the $wp_query is because of the duplicate post name slugs.  Without
 * these, when you go to domain.com/custom_post_type/term/postname, you may get more than one post (when you
 * only want the one article).
 *
 * @package     Fulcrum\Custom\Post_Type\Permalink
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knownthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Post_Type\Permalink;

use Fulcrum\Config\Config_Contract;
use WP_Query;

class Permalink_Query {

	/**
	 * Runtime configuration parameters.
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Custom post type
	 *
	 * @var string
	 */
	protected $custom_post_type;

	/**
	 * Taxonomy
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Taxonomy Query SQL (comes from WP_Tax_Query)
	 *
	 * @var array
	 */
	protected $tax_query_sql = array();

	/**
	 * Post_Name_Slug_Handler constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config
	 */
	public function __construct( Config_Contract $config ) {
		$this->config           = $config;
		$this->custom_post_type = $config->custom_post_type;
		$this->taxonomy         = $config->taxonomy;
	}

	/**
	 * Initialize the events.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init_events() {
		add_action( 'pre_get_posts', array( $this, 'add_tax_terms_to_query_handler' ) );
	}


	/**
	 * Handle adding in the proper query_vars elements.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query
	 *
	 * @return void
	 */
	public function add_tax_terms_to_query_handler( WP_Query $query ) {
		if ( ! $this->ok_to_add_tax_terms_query_vars( $query ) ) {
			$this->add_sort_order( $query );

			return;
		}

		$this->add_tax_terms_query_vars( $query );

		$this->register_callbacks_for_tax_query_sql_events();
	}

	/**
	 * Handle adding in the proper query_vars elements.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query
	 *
	 * @return void
	 */
	public function add_tax_terms_query_vars( WP_Query $query ) {
		$query->query_vars['taxonomy']  = $this->taxonomy;
		$query->query_vars['terms']     = $query->query[ $this->taxonomy ];
		$query->query_vars['tax_query'] = array(
			array(
				'taxonomy' => $this->taxonomy,
				'field'    => 'slug',
				'terms'    => $query->query[ $this->taxonomy ],
			),
		);
		$query->query_vars['orderby']   = 'title';
	}

	/**
	 * Register the callbacks for the tax query SQL events.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function register_callbacks_for_tax_query_sql_events() {
		add_filter( 'posts_where', array( $this, 'add_tax_query_sql' ), 10, 2 );

		add_filter( 'posts_join', array( $this, 'add_tax_query_sql' ), 10, 2 );
	}

	/**
	 * With a single article, no tax_query is called organically out of `WP_Query`.  It skips over
	 * the tax sections because it's a single, i.e. `is_single()`.  Therefore, we need to add in
	 * the where and join SQL statements to the `$query->request`.
	 *
	 * This callback hooks into both `posts_where` and `posts_join` filters.
	 *
	 * @since 1.0.0
	 *
	 * @param string $clause
	 * @param WP_Query $query Instance of WP_Query
	 *
	 * @return string
	 */
	public function add_tax_query_sql( $clause, WP_Query $query ) {

		if ( ! $this->is_single_query( $query ) ) {
			return $clause;
		}

		$which_clause = current_filter() == 'posts_where' ? 'where' : 'join';

		$this->init_tax_query_sql( $query );

		if ( is_array( $this->tax_query_sql ) && array_key_exists( $which_clause, $this->tax_query_sql ) ) {
			$clause .= $this->tax_query_sql[ $which_clause ];
		}

		return $clause;
	}

	/**
	 * Sort the query by title and ascending.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query
	 *
	 * @return void
	 */
	public function add_sort_order( WP_Query $query ) {
		if ( ! $this->is_main_front_end_query( $query ) ) {
			return;
		}

		if ( ! $query->is_archive() || ! $this->is_custom_single( $query ) ) {
			return;
		}

		$query->query_vars['orderby']                = 'title';
		$query->query_vars['order']                  = 'ASC';
		$query->query_vars['posts_per_archive_page'] = - 1;
		$query->query_vars['posts_per_page']         = - 1;
	}

	/**
	 * Checks if this query is the main front-end one.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query
	 *
	 * @return bool
	 */
	public function is_main_front_end_query( WP_Query $query ) {
		return ! is_admin() && $query->is_main_query();
	}


	/**
	 * Checks if this query is the one we want to add in the tax and terms query_vars.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query
	 *
	 * @return bool
	 */
	public function ok_to_add_tax_terms_query_vars( WP_Query $query ) {
		if ( ! $this->is_main_front_end_query( $query ) || $query->is_archive ) {
			return false;
		}

		return $this->is_custom_single( $query ) &&
		       ! array_key_exists( 'tax_query', $query->query_vars ) &&
		       empty( $query->tax_query );
	}

	/**
	 * Checks if this query is a single query..
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query
	 *
	 * @return bool
	 */
	protected function is_single_query( WP_Query $query ) {
		if ( is_admin() || ! is_single() ) {
			return false;
		}

		return $this->is_custom_single( $query ) &&
		       $this->taxonomy == $query->query_vars['taxonomy'] &&
		       array( 'terms', $query->query_vars );
	}

	/**
	 * Checks if this query is our custom single.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query
	 *
	 * @return bool
	 */
	protected function is_custom_single( WP_Query $query ) {
		return array_key_exists( 'post_type', $query->query ) &&
		       $this->custom_post_type == $query->query['post_type'] &&
		       array_key_exists( $this->taxonomy, $query->query );
	}

	/**
	 * Initialize the tax_query parameters within the $query.  We need to do this in order
	 * to populate the query and grab the SQL array (which is used in the filters above).
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query
	 *
	 * @return void
	 */
	protected function init_tax_query_sql( WP_Query $query ) {
		if ( ! empty( $this->tax_query_sql ) ) {
			return;
		}
		global $wpdb;

		$query->parse_tax_query( $query->query_vars );

		$this->tax_query_sql = $query->tax_query->get_sql( $wpdb->posts, 'ID' );
	}
}