<?php
/**
 * Description
 *
 * @package     Fulcrum\Custom\Post_Type\Permalink
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knownthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Post_Type\Permalink;


use Fulcrum\Config\Config_Contract;

class Custom_Permalink {

	/**
	 * Runtime configuration parameters.
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Instance of the Post Name Slug
	 *
	 * @var Post_Name_Slug
	 */
	protected $post_name_slug;

	/**
	 * Instance of the Query Handler
	 *
	 * @var Permalink_Query
	 */
	protected $permalink_query;

	/**
	 * Custom_Permalink constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config
	 * @param Post_Name_Slug $post_name_slug
	 * @param Permalink_Query $permalink_query
	 */
	public function __construct( Config_Contract $config, Post_Name_Slug $post_name_slug, Permalink_Query $permalink_query ) {
		$this->config          = $config;
		$this->post_name_slug  = $post_name_slug;
		$this->permalink_query = $permalink_query;

		$this->init_events();
	}

	/**
	 * Add taxonomy to the post link (rewrite), when configured.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_events() {
		$this->post_name_slug->init_events();
		$this->permalink_query->init_events();

		if ( $this->is_rewrite_with_taxonomy() ) {
			add_filter( 'post_type_link', array( $this, 'add_taxonomy_to_post_type_link' ), 10, 2 );
		}

		if ( $this->config->debugger ) {
			$this->debugger();
		}
	}

	/**
	 * Filter the permalink for a post with a custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_link The post's permalink.
	 * @param WP_Post $post The post in question.
	 *
	 * @return string
	 */
	public function add_taxonomy_to_post_type_link( $post_link, $post ) {

		if ( $post->post_type != $this->config->custom_post_type ) {
			return $post_link;
		}

		$taxonomy = $this->config->rewrite_with_taxonomy['taxonomy_name'];

		$terms = get_the_terms( $post, $taxonomy );

		if ( ! fulcrum_are_terms_present( $terms ) ) {
			return $post_link;
		}
		$term      = current( $terms );

		$link = sprintf( '%s/%s/%s', $this->get_post_type_rewrite(), $term->slug, $post->post_name );

		return home_url( user_trailingslashit( $link ) );

		return $post_link;
	}

	/**
	 * Fetches the post type rewrite.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_post_type_rewrite() {
		if ( $this->config->has( 'post_type_rewrite' ) ) {
			return $this->config->post_type_rewrite;
		}

		return $this->config->custom_post_type;
	}

	/**
	 * Checks if the "rewrite_with_taxonomy" is required.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_rewrite_with_taxonomy() {
		return $this->config->has( 'rewrite_with_taxonomy' ) &&
		       $this->config->is_array( 'rewrite_with_taxonomy' ) &&
		       $this->config->rewrite_with_taxonomy['enable'];
	}

	/**
	 * The debugger hooks in some data point views for display using Kint.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function debugger() {

		add_action( 'parse_request', function( $wp ) {
			if ( is_admin() ) {
				return;
			}

			d( $wp->matched_rule );
			d( $wp->matched_query );
		}, 9999);


		add_action( 'pre_get_posts', function( $query ){
			if ( is_admin() ) {
				return;
			}

			d( $query );
			if ( $this->permalink_query->ok_to_add_tax_terms_query_vars( $query ) ) {
				d( $query );
			}
		}, 9);
	}
}