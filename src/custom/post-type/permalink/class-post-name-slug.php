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

class Post_Name_Slug {

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
	 * Initialization events.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init_events() {
		add_filter( 'wp_unique_post_slug', array(
			$this,
			'check_unique_post_slug_against_permalink_structure'
		), 10, 6 );
	}

	/**
	 * Checks the unique post slug against the custom post type permalink structure.  If it matches, then
	 * the original slug is returns; else $slug is returned.
	 *
	 * Registered to `wp_unique_post_slug`
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug
	 * @param int $post_id
	 * @param string $post_status
	 * @param string $post_type
	 * @param $post_parent
	 * @param string $original_slug
	 *
	 * @return string
	 */
	public function check_unique_post_slug_against_permalink_structure( $slug, $post_id, $post_status, $post_type, $post_parent, $original_slug ) {

		if ( $this->custom_post_type != $post_type ) {
			return $slug;
		}

		$term_id = $this->get_term_id( $post_id );
		if ( $term_id === false ) {
			return $slug;
		}

		if ( ! $this->does_post_name_exist_for_same_term_id( $original_slug, $post_type, $post_id, $term_id ) ) {
			return $original_slug;
		}

		return $slug;
	}

	/**
	 * Checks if the post name exists (is a duplicate) in the database for the
	 * same term.
	 *
	 * This handler knows there will be duplicate post_name values due to the custom permalink
	 * structure; however, we do not want duplicates for the same term.
	 *
	 * @since 1.0.4
	 *
	 * @param string $slug Article's post_name (slug).
	 * @param string $post_type Custom post type.
	 * @param int $post_id Post ID
	 * @param int $term_id Term ID for this post.
	 *
	 * @return null|string
	 */
	protected function does_post_name_exist_for_same_term_id( $slug, $post_type, $post_id, $term_id ) {
		global $wpdb;

		$check_sql = "
			SELECT p.post_name
			FROM $wpdb->posts AS p
			INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id)
			WHERE post_name = %s AND post_type = %s AND ID != %d AND tr.term_taxonomy_id IN (%d)
			LIMIT 1;
			";
		$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, $post_type, $post_id, $term_id ) );

		return $post_name_check;
	}

	/**
	 * Get the term ID for this taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID
	 *
	 * @return bool|string
	 */
	protected function get_term_id( $post_id ) {
		$terms = wp_get_post_terms( $post_id, $this->taxonomy, array(
			'fields' => 'ids',
		) );

		if ( is_array( $terms ) ) {
			return array_shift( $terms );
		}

		return false;
	}
}
