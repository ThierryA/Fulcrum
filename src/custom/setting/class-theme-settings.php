<?php

/**
 * Base Setting Class
 *
 * @package     Fulcrum\Custom\Setting
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Fulcrum\Custom\Setting;

use Fulcrum\Foundation\Base;
use Fulcrum\Config\Config_Contract;

abstract class Setting extends Base {

	/*****************************
	 * Instantiate & Initialize
	 ****************************/

	/**
	 * Handles the methods upon instantiation
	 *
	 * @since 1.1.0
	 *
	 * @param Config_Contract $config
	 * @return self
	 */
	public function __construct( Config_Contract $config ) {
		if ( ! is_admin() ) {
			return;
		}

		$this->config = $config;

		$this->init_events();
	}

	/**
	 * Initialize the object by hooking into the needed actions and/or filters
	 *
	 * @since  1.0.0
	 *
	 * @return null
	 */
	abstract protected function init_events();

	/***********************
	 * Callbacks
	 **********************/

	/**
	 * Sanitize each of the filters
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	abstract public function sanitizer_filters();

	/**
	 * Register the metabox
	 *
	 * @since 1.0.0
	 *
	 * @param string $pagehook
	 * @return string
	 */
	public function register_metabox( $pagehook ) {
		if ( ! $this->is_ok_to_add_inpost_metaboxes() ) {
			return '';
		}

		add_meta_box(
			$this->config['id'],
			$this->config['title'],
			array( $this, 'render_metabox' ),
			$pagehook,
			$this->config['context'],
			$this->config['priority']
		);
	}

	/**
	 * Renders the metabox HTML
	 *
	 * @since 1.0.0
	 *
	 * @param $post
	 * @param array $args
	 * @return null
	 */
	public function render_metabox( $post, $args ) {
		if ( is_readable( $this->config['view'] ) ) {
			include( $this->config['view'] );
		}
	}

	/***********************
	 * Helpers
	 **********************/

	/**
	 * Checks if it's ok to add the inpost metaboxes
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_ok_to_add_inpost_metaboxes() {
		return current_user_can( 'unfiltered_html' );
	}
}
