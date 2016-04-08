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

use Fulcrum\Config\Config_Contract;
use InvalidArgumentException;
use Fulcrum\Support\Exceptions\Configuration_Exception;
use Fulcrum\Config\Fulcrum;

class Post_Type implements Post_Type_Contract {

	/**
	 * Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Post type name (all lowercase & no spaces)
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Instance of the Post Type Supports Handler
	 *
	 * @var Post_Type_Supports
	 */
	protected $supports;

	/**
	 * Internal flag if the labels are configured
	 *
	 * @var bool
	 */
	private $_are_labels_configured = false;

	/**
	 * Internal flag if the columns_data is configured
	 *
	 * @var bool
	 */
	private $_is_columns_data_configured = false;

	/**
	 * Internal flag if the sortable_data is configured
	 *
	 * @var bool
	 */
	private $_is_sortable_columns_configured = false;

	/**
	 * Internal flag if the sort_columns_by is configured
	 *
	 * @var bool
	 */
	private $_is_sort_columns_by_configured = false;

	/****************************
	 * Instantiate & Initialize
	 ***************************/

	/**
	 * Instantiate the Custom Post Type
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Runtime configuration parameters.
	 * @param string $post_type_name Post type name (all lowercase & no spaces)
	 * @param Post_Type_Supports $supports Instance of the post type supports handler
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( Config_Contract $config, $post_type_name, Post_Type_Supports $supports ) {
		$this->config    = $config;
		$this->post_type = $post_type_name;
		$this->supports  = $supports;

		if ( $this->is_starting_state_valid() ) {
			$this->init_config();
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
		global $wp_post_types;

		if ( isset( $wp_post_types[ $this->post_type ] ) ) {
			unset( $wp_post_types[ $this->post_type ] );
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

		$this->add_columns_filter();

		$this->add_column_data();

		$this->init_sorting();

		add_filter( 'request', array( $this, 'add_or_remove_to_from_rss_feed' ) );
	}

	/*****************************************************
	 * Register Methods
	 ***************************************************/

	/**
	 * Register Custom Post Type
	 *
	 * @since 1.0.0
	 *
	 * @uses self::build_args() Builds up the needed args from defaults & configuration
	 *
	 * @return null
	 */
	public function register() {
		$args = $this->build_args( $this->config['args'] );

		register_post_type( $this->post_type, $args );
	}

	/**
	 * Get all of the supports
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_the_supports() {
		return $this->supports->get_supports();
	}

	/*****************************************************
	 * Helper Methods
	 ***************************************************/

	/**
	 * Build the args for the register_post_type
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Runtime configuration parameters.
	 *
	 * @return array
	 */
	protected function build_args( array $args ) {
		if ( ! $this->is_labels_configured( $args ) ) {
			$args['labels'] = $this->build_labels();
		}

		$args['supports'] = $this->supports->build_supports( $args );

		$this->convert_taxonomy_into_array( $args );

		return $args;
	}

	/*****************************************************
	 * Taxonomy Methods
	 ***************************************************/

	/**
	 * Checks and, if necessary, converts the taxomony(ies) into an array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	protected function convert_taxonomy_into_array( array &$args ) {
		if ( array_key_exists( 'taxonomies', $args ) && ! is_array( $args['taxonomies'] ) ) {
			$args['taxonomies'] = explode( ',', $args['taxonomies'] );
		}
	}

	/*****************************************************
	 * Configuration Handlers
	 ***************************************************/

	/**
	 * Checks if the starting state is valid
	 *
	 * @since 1.0.0
	 *
	 * @throws InvalidArgumentException
	 * @return bool
	 */
	protected function is_starting_state_valid() {
		if ( ! $this->post_type ) {
			throw new InvalidArgumentException( __( 'For Custom Post Type Configuration, the Post type cannot be empty', 'fulcrum' ) );
		}

		if ( ! $this->is_configuration_valid() ) {
			throw new InvalidArgumentException( sprintf( __( 'For Custom Post Type Configuration, the config for [%s] cannot be empty.', 'fulcrum' ), $this->post_type ) );
		}

		return true;
	}

	/**
	 * Checks if $config is valid
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_configuration_valid() {
		return ! empty( $this->config->all() );
	}

	/**
	 * Initialized Config
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_config() {
		if ( ! $this->config->has( 'add_feed' ) || ! isset( $this->config->add_feed ) || true !== $this->config->add_feed ) {
			$this->config->add_feed = false;
		}

		$this->_are_labels_configured = $this->config->is_array( 'args' ) && $this->config->is_array( 'args.labels' );

		$this->_is_columns_data_configured = $this->config->is_array( 'columns_data' );

		$this->_is_sortable_columns_configured = $this->config->is_array( 'sortable_columns' );

		$this->_is_sort_columns_by_configured = $this->config->is_array( 'sort_columns_by' );
	}

	/*****************************************************
	 * Feed Methods
	 ***************************************************/

	/**
	 * Handles adding (or removing) this CPT to/from the RSS Feed
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_vars Query variables from parse_request
	 *
	 * @return array $query_vars
	 */
	public function add_or_remove_to_from_rss_feed( $query_vars ) {
		if ( ! isset( $query_vars['feed'] ) ) {
			return $query_vars;
		}

		if ( $query_vars['post_type'] ) {
			$this->add_or_remove_post_type_tofrom_feed_handler( $query_vars );

			return $query_vars;

		}

		if ( $this->config->add_feed ) {
			$query_vars['post_type'] = get_post_types();
		}

		return $query_vars;
	}

	/**
	 * Checks whether to add or remove the post type from feed. If yes,
	 * then it either adds or removes it.
	 *
	 * @since 1.0.0
	 *
	 * @param $query_vars
	 */
	protected function add_or_remove_post_type_tofrom_feed_handler( &$query_vars ) {
		$index = array_search( $this->post_type, (array) $query_vars['post_type'] );

		if ( $this->is_set_to_add_to_feed( $index ) ) {
			$query_vars['post_type'][] = $this->post_type;
		} elseif ( $this->is_set_to_remove_from_feed( $index ) ) {
			unset( $query_vars['post_type'][ $index ] );
			$query_vars['post_type'] = array_values( $query_vars['post_type'] );
		}
	}

	protected function is_set_to_add_to_feed( $index ) {
		return false === $index && $this->config->add_feed;
	}

	protected function is_set_to_remove_from_feed( $index ) {
		return false !== $index && ! $this->config->add_feed;
	}

	/*****************************************************
	 * Column Data Handlers
	 ***************************************************/

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
	public function columns_data( $column_name, $post_id ) {
		$column_config = $this->get_column_config( $column_name, $post_id );
		if ( false === $column_config ) {
			return;
		}

		if ( $this->is_callback_callable( $column_config['callback'] ) ) {
			$response = call_user_func_array( $column_config['callback'], $column_config['args'] );
			if ( $column_config['echo'] ) {
				echo $response;
			}
		}
	}

	/**
	 * Modify the columns for this custom post type
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Array of Columns
	 *
	 * @return array Amended Array
	 */
	public function columns_filter( $columns ) {
		foreach ( $this->config->columns_filter as $column => $value ) {
			if ( 'cb' == $column && true == $value ) {
				$columns['cb'] = '<input type="checkbox" />';
			} else {
				$columns[ $column ] = $value;
			}
		}

		return $columns;
	}

	/**
	 * Check if the column name is valid for our configuration
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 *
	 * @return bool
	 */
	protected function is_column_name_valid( $column_name ) {
		/** @noinspection PhpIllegalArrayKeyTypeInspection */
		return $this->_is_columns_data_configured &&
		       array_key_exists( $column_name, $this->config->columns_data ) &&
		       is_array( $this->config->columns_data[ $column_name ] ) && ! empty( $this->config->columns_data[ $column_name ] ) &&
		       isset( $this->config->columns_data[ $column_name ]['callback'] ) && ! empty( $this->config->columns_data[ $column_name ]['callback'] );
	}

	/**
	 * Get the config for the injected column name
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 *
	 * @return array|bool
	 */
	protected function get_column_config( $column_name, $post_id ) {
		if ( ! $this->is_column_name_valid( $column_name ) ) {
			return false;
		}

		$column_config = wp_parse_args(
			$this->config->columns_data[ $column_name ],
			array(
				'callback' => '',
				'echo'     => true,
				'args'     => array(),
			)
		);

		$column_config['args'][] = $post_id;

		return $column_config;
	}

	/**
	 * Add columns filter when it is configured for use.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function add_columns_filter() {
		if ( $this->config->has( 'columns_filter' ) && $this->config->is_array( 'columns_filter' ) ) {
			add_filter( "manage_{$this->post_type}_posts_columns", array( $this, 'columns_filter' ) );
		}
	}

	/**
	 * Add columns data when it is configured for use.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function add_column_data() {
		if ( $this->config->has( 'columns_data' ) && $this->config->is_array( 'columns_data' ) ) {
			add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'columns_data' ), 10, 2 );
		}
	}

	/*****************************************************
	 * Column Sorting Handlers
	 ***************************************************/

	/**
	 * Initialize the sorting features (i.e. to customize it).
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_sorting() {
		if ( ! $this->_is_sortable_columns_configured ) {
			return;
		}

		add_filter( "manage_edit-{$this->post_type}_sortable_columns", array( $this, 'make_columns_sortable' ) );

//		add_filter( 'request', array( $this, 'sort_columns_by' ), 50 );
	}

	/**
	 * Filter for making the columns sortable
	 *
	 * @since  1.0.0
	 *
	 * @param  array $sortable_columns Sortable columns
	 *
	 * @return array Amended $sortable_columns
	 */
	public function make_columns_sortable( $sortable_columns ) {
		foreach ( (array) $this->config['sortable_columns'] as $key => $col ) {
			$sortable_columns[ $key ] = $key;
		}

		return $sortable_columns;
	}

	/**
	 * Sort columns by the configuration
	 *
	 * @since 1.0.0
	 *
	 * @param $vars
	 *
	 * @return mixed
	 */
	public function sort_columns_by( $vars ) {
		if ( ! isset( $vars['post_type'] ) || $this->post_type != $vars['post_type'] ) {
			return $vars;
		}

		//* TODO-Tonya Add code for sorting columns by
//        foreach( (array) $this->config['sort_columns_by'] as $key => $sc_vars) {
//            if ( isset( $vars['orderby'] ) && $sc_vars['meta_key'] == $vars['orderby'] ) {
//                // $vars = array_merge($vars, array(
//                //     'meta_key'  => $sc_vars['meta_key'],
//                //     'orderby'   => $sc_vars['orderby']
//                // ));
//            }
//        }

		return $vars;
	}

	/*****************************************************
	 * Label Methods
	 ***************************************************/

	/**
	 * Build the labels for the register_post_type() $args
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function build_labels() {
		$default_labels = array(
			'name'               => _x( $this->config->plural_name, 'post type general name', 'fulcrum' ),
			'singular_name'      => _x( $this->config->singular_name, 'post type singular name', 'fulcrum' ),
			'add_new'            => _x( 'Add New', $this->post_type, 'fulcrum' ),
			'add_new_item'       => sprintf( '%s %s', __( 'Add New', 'fulcrum' ), $this->config->singular_name ),
			'edit_item'          => sprintf( '%s %s', __( 'Edit', 'fulcrum' ), $this->config->singular_name ),
			'new_item'           => sprintf( '%s %s', __( 'New', 'fulcrum' ), $this->config->singular_name ),
			'view_item'          => sprintf( '%s %s', __( 'View', 'fulcrum' ), $this->config->singular_name ),
			'search_items'       => sprintf( '%s %s', __( 'Search', 'fulcrum' ), $this->config->plural_name ),
			'not_found'          => sprintf( __( 'No %s found', 'fulcrum' ), strtolower( $this->config->singular_name ) ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'fulcrum' ), strtolower( $this->config->plural_name ) ),
			'parent_item_colon'  => '',
			'all_items'          => sprintf( '%s %s', __( 'All', 'fulcrum' ), $this->config->plural_name ),
			'menu_name'          => _x( $this->config->plural_name, 'admin menu', 'fulcrum' ),
		);

		return $this->_are_labels_configured ? wp_parse_args( $this->config['args']['labels'], $default_labels ) : $default_labels;
	}

	/**
	 * Convert the post type from a slug to a name
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function convert_post_type_to_name() {
		if ( $this->is_labels_key_configured( 'name' ) ) {
			return $this->config['args']['labels']['name'];
		}

		$name = str_replace( '-', ' ', $this->post_type );

		return ucwords( $name );
	}

	/**
	 * Get the label name
	 *
	 * @since 1.1.1
	 *
	 * @return string
	 */
	protected function get_label_name() {
		return $this->config->has( 'singular_name' )
			? $this->config->singular_name
			: $this->convert_post_type_to_name();
	}


	/**
	 * Check if the given $key is in the labels configuration array and has a value
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key in the labels configuration array to check
	 *
	 * @return bool
	 */
	protected function is_labels_key_configured( $key ) {
		return $this->_are_labels_configured && isset( $this->config['args']['labels'][ $key ] ) &&
		       ! empty( $this->config['args']['labels'][ $key ] );
	}

	/*****************************************************
	 * State Checkers
	 ***************************************************/

	/**
	 * Checks if the labels are configured.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Array of arguments.
	 *
	 * @return bool
	 */
	protected function is_labels_configured( array $args ) {
		return array_key_exists( 'labels', $args ) && ! empty( $args['labels'] );
	}

	/**
	 * Checks if the callback is callback
	 *
	 * @since 1.0.0
	 *
	 * @param string $callback
	 *
	 * @return bool
	 * @throws Configuration_Exception
	 */
	protected function is_callback_callable( $callback ) {
		if ( ! is_callable( $callback ) ) {
			throw new Configuration_Exception(
				sprintf( __( 'The callback [%s], for the custom post type [%s], was not found, as call_user_func_array() expects a valid callback function/method.', 'fulcrum' ),
					$callback, $this->post_type
				)
			);

			return false;
		}

		return true;
	}
}
