<?php

/**
 * Post Type Class functions
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

use Fulcrum\Config\Config;
use Fulcrum\Config\Config_Contract;

class Post_Type_Supports {

	/**
	 * Configuration parameters
	 *
	 * @var Config
	 */
	protected $config;

	/**
	 * Default supports
	 *
	 * @var array
	 */
	protected $defaults = array();

	/**
	 * Array of supports
	 *
	 * @var array
	 */
	protected $supports = array();

	/****************************
	 * Instantiate & Initialize
	 ***************************/

	/**
	 * Post_Type_Supports constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Runtime configuration parameters.
	 */
	public function __construct( Config_Contract $config ) {
		$this->config    = $config;
	}

	/**
	 * Build the supports argument.  If it is not configured, then grab all of the
	 * supports from the built-in 'post' post type.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Runtime configuration parameters.
	 *
	 * @return array
	 */
	public function build_supports( array $args ) {

		if ( array_key_exists( 'supports', $args ) ) {
			$this->supports = $args['supports'];

			$this->add_page_attributes();

		} else {
			$this->build_supports_by_configuration();
		}

		return $this->supports;
	}

	/**
	 * Gets the array of supports for this post type.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_supports() {
		return $this->supports();
	}

	/*****************************************************
	 * Helpers
	 ***************************************************/

	/**
	 * Build the supports fromthe configuration.  The starting defaults are from the 'post' supports.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function build_supports_by_configuration() {
		$this->supports = get_all_post_type_supports( 'post' );

		if ( $this->are_additional_supports_enabled() ) {
			$this->supports = array_merge( $this->supports, $this->config->additional_supports );
		}

		$this->filter_out_excluded_supports();

		$this->supports = array_keys( $this->supports );

		$this->add_page_attributes();
	}

	/**
	 * Adds the 'page-attributes' support when required.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function add_page_attributes() {
		if ( $this->is_page_attributes_support_required() ) {
			$this->supports[] = 'page-attributes';
		}
	}

	/**
	 * Filters out the unwanted (excluded) supports.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function filter_out_excluded_supports() {
		$this->supports = array_filter( $this->supports, function ( $include_support ) {
			return $include_support;
		} );
	}

	/*****************************************************
	 * State Checkers
	 ***************************************************/

	/**
	 * Checks if the exclude_supports parameter is configured.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function are_additional_supports_enabled() {
		return $this->config->has( 'additional_supports' ) &&
		       $this->config->is_array( 'additional_supports' );
	}

	/**
	 * Checks if the page-attributes support is required.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_page_attributes_support_required() {
		if ( ! $this->is_hierachical() ) {
			return false;
		}

		return empty( $this->config->args['supports'] ) ||
		       ! in_array( 'page-attributes', $this->config->args['supports'] );
	}

	/**
	 * Checks if this post type is hierarchical.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_hierachical() {
		return isset( $this->config->args['hierarchical'] ) &&
		       $this->config->args['hierarchical'];
	}
}
