<?php

/**
 * Custom Taxonomy
 *
 * This class handles a custom taxonomy object.
 *
 * @package     Fulcrum\Custom\Taxonomy
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Taxonomy;

use Fulcrum\Config\Config_Contract;
use InvalidArgumentException;

class Taxonomy implements Taxonomy_Contract {

	/**
	 * Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Taxonomy name (all lowercase & no spaces)
	 *
	 * @var string
	 */
	protected $taxonomy_name;

	/**
	 * Name of the object type for the taxonomy object
	 *
	 * @var string|array
	 */
	protected $object_type;

	/**
	 * Internal flag if the labels are configured
	 *
	 * @var bool
	 */
	private $_are_labels_configured = false;

	/****************************
	 * Instantiate & Initialize
	 ***************************/

	/**
	 * Instantiate the Custom Post Type
	 *
	 * @since 1.0.0
	 *
	 * @param string $taxonomy_name Taxonomy name (all lowercase & no spaces)
	 * @param string|array $object_type Name of the object type for the taxonomy object
	 * @param Config_Contract $config Runtime configuration parameters
	 *
	 * @return self
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $taxonomy_name, $object_type, Config_Contract $config ) {
		$this->taxonomy_name = $taxonomy_name;
		$this->object_type   = $object_type;
		$this->config        = $config;

		if ( $this->is_starting_state_valid() ) {
			$this->init_events();
		}
	}

	/**
	 * Remove this CPT from the post types upon object destruct
	 *
	 * @since 1.0.0
	 *
	 * @uses global $wp_post_type
	 * @return null
	 */
	public function __destruct() {

		global $wp_taxonomies;

		if ( isset( $wp_taxonomies[ $this->taxonomy_name ] ) ) {
			unset( $wp_taxonomies[ $this->taxonomy_name ] );
		}
	}

	/**
	 * Setup Hooks & Filters
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_events() {
		add_action( 'init', array( $this, 'register' ) );
	}

	/*****************************************************
	 * Public Methods
	 ***************************************************/

	/**
	 * Time to register this taxonomy
	 *
	 * @since 1.0.1
	 *
	 * @uses self::build_args() to build up the args needed to register this taxonomy
	 * @return null
	 */
	public function register() {
		$args = $this->build_args( $this->config->args );

		register_taxonomy( $this->taxonomy_name, $this->object_type, $args );
	}

	/****************
	 * Helpers
	 ****************/

	/**
	 * Build the args for the register_taxonomy
	 *
	 * @since 1.0.0
	 *
	 * @uses filter: wpdc_register_taxonomy_default_args
	 *
	 * @uses self::build_labels() Builds up the labels from defaults and/or config
	 *
	 * @param array $args Runtime configuration parameters.
	 *
	 * @return array
	 */
	protected function build_args( array $args ) {

		if ( empty( $args['labels'] ) ) {
			$args['labels'] = $this->build_labels();
		}

		return $args;
	}

	/**
	 * Build the labels for the register_post_type() $args
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function build_labels() {
		$default_labels = array(
			'name'                       => _x( $this->config->plural_name, 'taxonomy general name', 'fulcrum' ),
			'singular_name'              => _x( $this->config->singular_name, 'taxonomy singular name', 'fulcrum' ),
			'menu_name'                  => _x( $this->config->plural_name, 'admin menu', 'fulcrum' ),
			'all_items'                  => sprintf( '%s %s', __( 'All', 'fulcrum' ), $this->config->plural_name ),
			'edit_item'                  => sprintf( '%s %s', __( 'Edit', 'fulcrum' ), $this->config->singular_name ),
			'view_item'                  => sprintf( '%s %s', __( 'View', 'fulcrum' ), $this->config->singular_name ),
			'update_item'                => sprintf( '%s %s', __( 'Update', 'fulcrum' ), $this->config->singular_name ),
			'add_new_item'               => sprintf( '%s %s', __( 'Add New', 'fulcrum' ), $this->config->singular_name ),
			'new_item_name'              => sprintf( '%s %s', __( 'New', 'fulcrum' ), $this->config->singular_name ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'search_items'               => sprintf( '%s %s', __( 'Search', 'fulcrum' ),  $this->config->plural_name ),
			'popular_items'              => sprintf( '%s %s', __( 'Popular', 'fulcrum' ),  $this->config->plural_name ),
			'separate_items_with_commas' => null,
			'add_or_remove_items'        => null,
			'choose_from_most_used'      => null,
			'not_found'                  => sprintf( __( 'No %s found', 'fulcrum' ), strtolower( $this->config->singular_name ) ),
		);

		return $this->_are_labels_configured ? wp_parse_args( $this->config->args['labels'], $default_labels ) : $default_labels;
	}

	/**
	 * Checks if $config is valid
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_configuration_valid() {
		$this->_are_labels_configured = $this->config->is_array( 'args' ) && $this->config->is_array( 'args.labels' );

		return true;
	}

	/**
	 * Checks if the starting state is valid
	 *
	 * @since 1.0.0
	 *
	 * @throws InvalidArgumentException
	 * @return bool
	 */
	protected function is_starting_state_valid() {

		if ( ! $this->taxonomy_name ) {
			throw new InvalidArgumentException( __( 'For Custom Taxonomy Configuration, the taxonomy name cannot be empty.', 'fulcrum' ) );
		}

		if ( ! $this->object_type || empty( $this->object_type ) ) {
			throw new InvalidArgumentException( __( 'For Custom Taxonomy Configuration, the object_type in config cannot be empty.', 'fulcrum' ) );
		}

		$this->is_configuration_valid();

		return true;
	}
}