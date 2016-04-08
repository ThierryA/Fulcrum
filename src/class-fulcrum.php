<?php

/**
 * Fulcrum - The central custom repository for WordPress.
 *
 * @package     Fulcrum
 * @since       1.0.4
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum;

fulcrum_prevent_direct_file_access();

use Fulcrum\Config\Config_Contract;
use Fulcrum\Container\Container;

class Fulcrum extends Container implements Fulcrum_Contract {

	/**
	 * The plugin's version
	 *
	 * @var string
	 */
	const VERSION = '1.0.4';

	/**
	 * The plugin's minimum WordPress requirement
	 *
	 * @var string
	 */
	const MIN_WP_VERSION = '3.5';

	/**
	 * Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Instance of Fulcrum
	 *
	 * @var Fulcrum_Contract
	 */
	public static $fulcrum;

	/*************************
	 * Getters
	 ************************/

	public function version() {
		return self::VERSION;
	}

	public function min_wp_version() {
		return self::MIN_WP_VERSION;
	}

	public static function getFulcrum() {
		return self::$fulcrum;
	}

	/**************************
	 * Instantiate & Initialize
	 *************************/

	/**
	 * Instantiate the plugin
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config
	 */
	public function __construct( Config_Contract $config ) {
		$this->config = $config;
		parent::__construct();

		$this['fulcrum']                         = self::$fulcrum = $this;
		$this['is_flush_rewrite_rules_required'] = false;

		$this->init_handlers();
		$this->init_service_providers();

		$this->init_events();
	}

	/**
	 * Initialize the handlers.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_handlers() {
		$config = $this->config->handlers;

		if ( $this->is_dev_env() ) {
			$config = array_merge( $config, $this->config->dev_env['handlers'] );
		}

		array_walk( $config, function ( $concrete, $unique_id ) {
			$this->register_concrete( $concrete, $unique_id );
		} );
	}

	/**
	 * Initialize provider handler, which registers each of the service providers.
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	protected function init_service_providers() {
		$this['provider.handler']->register( $this->config->service_providers );

		if ( is_admin() ) {
			$this['provider.handler']->register( $this->config->admin_service_providers );
		}
	}

	/**
	 *
	 * NEXT - All of this needs to move to the Add-On handler.  Let it
	 * handle call each of the service providers to do their thang.
	 *
	 * The bootstrap here does not need to handle that.
	 */

	/**
	 * Initialize events
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_events() {
		add_action( 'plugins_loaded', function () {
			do_action( 'fulcrum_is_loaded', $this );
		}, 1 );
	}

	/***************
	 * Public
	 *************/

	/**
	 * Checks if this is the development environment.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_dev_env() {
		return $this['is_dev_env'];
	}
}
