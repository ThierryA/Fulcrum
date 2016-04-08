<?php

/**
 * Genesis Theme Settings Admin Metabox
 *
 * Adds metabox(es) to the Genesis Theme Settings Admin Page
 *
 * @package     Fulcrum\Custom\Setting
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Fulcrum\Custom\Setting;

use Fulcrum\Config\Config_Contract;

class Genesis_Theme_Settings extends Setting {

	/*****************************
	 * Instantiate & Initialize
	 ****************************/

	/**
	 * Initialize the object by hooking into the needed actions and/or filters
	 *
	 * @since  1.0.0
	 *
	 * @return null
	 */
	protected function init_events() {
		add_filter( 'genesis_theme_settings_defaults', function ( $defaults ) {
			return array_merge( $this->config->theme_setting_defaults, $defaults );
		} );
		add_action( 'genesis_settings_sanitizer_init', array( $this, 'sanitizer_filters' ) );

		add_action( 'genesis_theme_settings_metaboxes', array( $this, 'register_metabox' ) );
	}

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
	public function sanitizer_filters() {
		array_walk( $this->config->sanitizer_filters, function ( $settings, $filter ) {
			genesis_add_option_filter( $filter, GENESIS_SETTINGS_FIELD, $settings );
		} );
	}
}
