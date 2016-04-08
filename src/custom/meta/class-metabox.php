<?php

/**
 * Custom Field Contract
 *
 * @package     Fulcrum\Custom\Meta
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Meta;

use Fulcrum\Config\Config_Contract;

class Metabox implements Metabox_Contract {

	/**
	 * Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Flag for if Meta Array is configured
	 *
	 * @var bool
	 */
	protected $has_meta_array = false;

	/**
	 * Meta array configuration
	 *
	 * @var
	 */
	protected $meta_array = array();

	/**
	 * Flag for if Meta Single is configured
	 *
	 * @var bool
	 */
	protected $has_meta_single = false;

	/**
	 * Meta single configuration
	 *
	 * @var
	 */
	protected $meta_single = array();

	/**
	 * Post ID
	 *
	 * @var int
	 */
	protected $post_id = 0;

	/*****************************
	 * Instantiate & Initialize
	 ****************************/

	/**
	 * Handles the methods upon instantiation
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Runtime configuration parameters.
	 *
	 * @return self
	 */
	public function __construct( Config_Contract $config ) {
		if ( ! is_admin() ) {
			return;
		}

		$this->post_id = fulcrum_get_post_id();

		if ( $this->post_id > 1 ) {
			$this->init_properties( $config );
			$this->init_events();
		}
	}

	/**
	 * Initialize properties
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Runtime configuration parameters.
	 *
	 * @return null
	 */
	protected function init_properties( Config_Contract $config ) {
		$this->config = $config;

		$this->init_single_array();

		$this->init_meta_array();
	}

	/**
	 * Initialize the Single Array Meta, if configured
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_single_array() {
		$this->has_meta_single = $this->config->is_array( 'meta_single' );
		if ( $this->has_meta_single ) {
			$this->meta_single = $this->config->meta_single;
		}
	}

	/**
	 * Initialize the Array Array Meta, if configured
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_meta_array() {
		$this->has_meta_array = $this->config->is_array( 'meta_array' ) &&
		                        $this->config->meta_array['meta_key'] &&
		                        ! empty( $this->config->meta_array['meta_defaults'] );

		if ( $this->has_meta_array ) {
			$this->meta_array = $this->config->meta_array;
		}
	}

	/**
	 * Initialize the object by hooking into the needed actions and/or filters
	 *
	 * @since  1.0.0
	 *
	 * @return null
	 */
	protected function init_events() {
		if ( ! $this->is_add_limiter_valid() ) {
			return;
		}

		if ( ! $this->is_post_type_correct() ) {
			return;
		}

		add_action( 'add_meta_boxes', array( $this, 'add_inpost_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_meta' ), $this->config->save_post_priority, 2 );
	}

	/***********************
	 * Public Methods
	 **********************/

	/**
	 * Register the metabox for each screen.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function add_inpost_metaboxes() {
		array_walk( $this->config->screen, array( $this, 'add_meta_box' ) );
	}

	/**
	 * Renders the metabox HTML
	 *
	 * @since 1.0.0
	 *
	 * @param $post
	 * @param array $args
	 *
	 * @return null
	 */
	public function render_metabox( $post, $args ) {
		list( $meta, $meta_single ) = $this->fetch_meta( $post, $args );

		$this->do_pre_render( $post, $args );

		if ( is_readable( $this->config->view ) ) {
			wp_nonce_field( $this->config->nonce_action, $this->config->nonce_name );
			include( $this->config->view );

			do_action( 'fulcrum_metabox_render_view', $this->config->meta_name, $meta, $post, $args );
		}
	}

	/**
	 * Save the meta when we save the page.
	 *
	 * @since 1.0.1
	 *
	 * @param integer $post_id Post ID.
	 * @param stdClass $post Post object.
	 *
	 * @return mixed Returns post id if permissions incorrect, null if doing autosave,
	 *               ajax or future post, false if update or delete failed, and true
	 *               on success.
	 */
	function save_meta( $post_id, $post ) {
//		if ( ! $this->is_ok_to_save_meta( $post_id, $post ) ) {
//			return;
//		}
//
//		if ( $this->has_meta_single ) {
			$this->update_meta_single( $post_id );
//		}
//
//		if ( $this->has_meta_array ) {
//			$this->update_meta_array( $post_id );
//		}
	}

	/***********************
	 * Helpers
	 **********************/

	/**
	 * Fetch meta and return back to caller
	 *
	 * @since 1.1.1
	 *
	 * @param $post
	 * @param array $args
	 *
	 * @return array
	 */
	protected function fetch_meta( $post, $args ) {
		$meta        = array();
		$meta_single = array();

		if ( $this->has_meta_array ) {
			$meta = $this->get_meta( $post, $args, $this->meta_array['meta_key'], $this->meta_array['meta_defaults'] );
		}

		foreach ( $this->meta_single as $meta_key => $config ) {
			$meta_single[ $meta_key ] = $this->get_meta( $post, $args, $meta_key, $config['default'], true );
		}

		return array( $meta, $meta_single );
	}

	/**
	 * Sanitize and then update for single Meta data
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id
	 *
	 * @return null
	 */
	protected function update_meta_single( $post_id ) {
		$post = array_key_exists( $this->config['meta_name'], $_POST ) ? $_POST[ $this->config['meta_name'] ] : array();

		foreach ( $this->meta_single as $meta_key => $config ) {
			$value = array_key_exists( $meta_key, $post )
				? $post[ $meta_key ]
				: $config['default'];

			update_post_meta( $post_id, $meta_key, $this->sanitize_data( $value, $config['sanitize'] ) );
		}
	}

	/**
	 * Sanitize and then update for array Meta data
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id
	 *
	 * @return null
	 */
	protected function update_meta_array( $post_id ) {
		$meta_key = $this->meta_array['meta_key'];

		foreach ( $this->meta_array['meta_defaults'] as $key => $value ) {

			// Get the value either from the $_POST or default
			$value = array_key_exists( $key, $_POST[ $meta_key ] )
				? $_POST[ $this->meta_array['meta_key'] ][ $key ]
				: $value;

			// Sanitize and store
			$data[ $key ] = $this->meta_array['sanitize'][ $key ]
				? $this->sanitize_data( $value, $this->meta_array['sanitize'][ $key ] )
				: $value;
		}

		// Now Save it
		update_post_meta( $post_id, $meta_key, $data );
	}

	/**
	 * Sanitize user submitted data before saving
	 *
	 * @since 1.0.1
	 *
	 * @param mixed $value
	 * @param string $filter
	 *
	 * @return mixed
	 */
	protected function sanitize_data( $value, $filter ) {
		if ( $filter ) {
			return $filter( $value );

		} elseif ( current_user_can( 'unfiltered_html' ) ) {
			return $value;

		} else {
			return wp_kses_post( $value );
		}
	}

	/**
	 * Get the meta from the database
	 *
	 * @since 1.0.0
	 *
	 * @param           $post
	 * @param array $args
	 * @param string $meta_key
	 * @param mixed $defaults
	 * @param bool $single
	 *
	 * @return array
	 */
	protected function get_meta( $post, $args, $meta_key, $defaults, $single = false ) {
		$meta = get_post_meta( $post->ID, $meta_key, $single );

		if ( $single ) {
			return $meta ?: $defaults;
		}

		$meta = isset( $meta[0] ) ? $meta[0] : array();

		return wp_parse_args( $meta, $defaults );
	}

	/**
	 * Add the meta box if the conditions are valid
	 *
	 * @since 1.1.0
	 *
	 * @param string $screen
	 * @return bool
	 */
	protected function add_meta_box( $screen ) {
		if ( ! $this->is_ok_to_add_metabox( $screen ) ) {
			return;
		}

		add_meta_box(
			$this->config->id,
			$this->config->title,
			array( $this, 'render_metabox' ),
			$screen,
			$this->config->context,
			$this->config->priority
		);
	}

	/**
	 * Do Pre Render, which allows any extending classes to manipulate data before
	 * the view is rendered.
	 *
	 * @since 1.1.1
	 *
	 * @param $post
	 * @param array $args
	 *
	 * @return null
	 */
	protected function do_pre_render( $post, $args ) { /* do nothing */ }

	/***********************
	 * Validators
	 **********************/

	/**
	 * Checks the post type (screen) is correct for this metabox.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	protected function is_post_type_correct() {
		$post_type = get_post_type( $this->post_id );

		return in_array( $post_type, $this->config->screen );
	}

	/**
	 * Checks if the initial state of the limiter(s) is(are) valid
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	protected function is_add_limiter_valid() {
		if ( $this->use_limit_to_page_id_check() ) {
			return $this->is_limit_to_page_id_valid();
		}

		if ( $this->use_limit_to_template_check() ) {
			return $this->is_limit_to_template_valid();
		}

		return true;
	}

	/**
	 * Checks if the specified page ID (limit_to_page_id) is valid for the current screen
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_limit_to_page_id_valid() {
		return $this->config->limit_to_page_id == $this->post_id;
	}

	/**
	 * Validates the initial configuration of the limit_to_template. It is valid when either condition is true:
	 *      1. It is not configured.
	 *      2. The template and screen are configured.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	protected function is_limit_to_template_valid() {
		return $this->is_template_correct_for_this_screen();
	}

	/**
	 * Checks where to use the limit_to_page_id check for this screen.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	protected function use_limit_to_page_id_check() {
		return 	$this->config->has( 'limit_to_page_id' ) &&
		          $this->config->limit_to_page_id;
	}

	/**
	 * Checks whether to use the limit_to_template check for this screen.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	protected function use_limit_to_template_check() {
		return 	$this->config->has( 'limit_to_template.template' ) &&
		          $this->config->limit_to_template['template'] &&
		          $this->config->limit_to_template['template'] &&
		          $this->config->is_array( 'limit_to_template.screen' );
	}

	/**
	 * Checks if the template file is correct for this screen
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	protected function is_template_correct_for_this_screen() {
		$template_file = fulcrum_get_template_file( $this->post_id );

		return $this->config->limit_to_template['template'] == $template_file;
	}

	/**
	 * Checks if the specified template (i.e. limit_to_template) is valid for the current screen.
	 *
	 * @since 1.1.0
	 *
	 * @param string $screen
	 * @return bool
	 */
	protected function is_ok_to_add_metabox( $screen ) {
		if ( ! $this->is_screen_configured_for_limit_to_template( $screen ) ) {
			return true;
		}

		return $this->is_template_correct_for_this_screen();
	}

	/**
	 * Checks if this screen is configured for the 'limit_to_template' conditional.
	 *
	 * @since 1.1.0
	 *
	 * @param string $screen
	 * @return bool
	 */
	protected function is_screen_configured_for_limit_to_template( $screen ) {
		if ( ! $this->config->limit_to_template['template'] ) {
			return false;
		}
		return in_array( $screen, $this->config->limit_to_template['screen'] );
	}

	/**
	 * Checks if conditions are set to save the meta to the database.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id
	 *
	 * @return bool|false
	 */
	protected function is_ok_to_save_meta( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( ! current_user_can( 'edit_others_posts', $post_id ) ) {
			return false;
		}

		if ( ! isset( $_POST[ $this->config->meta_name ] ) ) {
			return false;
		}

		if ( ! isset( $_POST[ $this->config->nonce_name ] ) ) {
			die( 'nonce not set' );
			return false;
		}

		return wp_verify_nonce( $_POST[ $this->config->nonce_name ], $this->config->nonce_action );
	}
}
