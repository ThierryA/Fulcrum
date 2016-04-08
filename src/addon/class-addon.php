<?php

/**
 * Addon abstract class - all plugins should extend
 * this class in order to extend Fulcrum and utilize
 * it's functionality and features.
 *
 * @package     Fulcrum\Addon
 * @since       1.0.2
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Addon;

use Fulcrum\Fulcrum;
use Fulcrum\Fulcrum_Contract;
use Fulcrum\Config\Config_Contract;

abstract class Addon {

	/**
	 * Runtime Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Instance of Fulcrum
	 *
	 * @var Fulcrum_Contract
	 */
	protected $fulcrum;

	/**
	 * Array of configured providers
	 *
	 * @var array
	 */
	protected $providers = array();

	/**
	 * Addon plugin file.
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * Flag for if the flush_rewrite_rules is required.
	 *
	 * @var bool
	 */
	protected $is_flush_rewrite_rules_required = false;

	/*************************
	 * Getters
	 ************************/

	public function version() {
		return self::VERSION;
	}

	public function min_wp_version() {
		return self::MIN_WP_VERSION;
	}

	/*************************
	 * Instantiate & Init
	 ************************/

	/**
	 * Instantiate the plugin
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Runtime configuration parameters
	 * @param string $plugin_file File for the addon plugin.
	 * @param Fulcrum_Contract $fulcrum Instance of Fulcrum
	 */
	public function __construct( Config_Contract $config, $plugin_file, Fulcrum_Contract $fulcrum = null ) {
		$this->config      = $config;
		$this->plugin_file = plugin_basename( $plugin_file );
		$this->fulcrum     = is_null( $fulcrum ) ? Fulcrum::getFulcrum() : $fulcrum;

		$this->init_addon();
		$this->init_parameters();
		$this->init_service_providers();
		$this->init_admin_service_providers();
		$this->register_concretes();
		$this->init_events();
	}

	/**
	 * Addons can overload this method for additional functionality
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_addon() {
		// it's here if you need it.
	}

	/**
	 * Description.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_events() {
		// it's here if you need it.
	}

	/**
	 * Initialize the initial parameters by loading each into the Container.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_parameters() {
		if ( ! $this->config->is_array( 'initial_parameters' ) ) {
			return;
		}
		array_walk( $this->config->initial_parameters, function ( $value, $unique_id ) {
			$this->fulcrum[ $unique_id ] = $value;
		} );
	}

	/**
	 * Initialize service providers
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_service_providers() {
		if ( ! $this->config->has( 'service_providers' ) ) {
			return;
		}

		$this->load_service_providers_into_container( $this->config->service_providers );
	}

	/**
	 * Initialize admin service providers
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_admin_service_providers() {
		if ( ! is_admin() ) {
			return;
		}

		if ( ! $this->config->has( 'admin_service_providers' ) ) {
			return;
		}

		$this->load_service_providers_into_container( $this->config->admin_service_providers );
	}

	/**
	 * Load each service provider into the container.
	 *
	 * @since 1.0.0
	 *
	 * @param array $service_provider Array of service provider configurations
	 *
	 * @return void
	 */
	protected function load_service_providers_into_container( array $service_provider ) {
		foreach ( $service_provider as $unique_id => $provider_config ) {
			$config            = $this->load_config_file( $provider_config['config'] );
			$this->providers[] = $provider = $provider_config['provider'];

			$this->fulcrum[ $provider ]->register( $config, $unique_id );
		}
	}

	/**
	 * Register the concretes into Fulcrum.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function register_concretes() {
		if ( ! $this->config->has( 'register_concretes' ) ) {
			return;
		}

		foreach ( $this->config->register_concretes as $unique_id => $config ) {
			$this->fulcrum->register_concrete( $config, $unique_id );
		}
	}

	/**
	 * Load the configuration file.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $config
	 *
	 * @return mixed
	 */
	protected function load_config_file( $config ) {
		if ( is_array( $config ) ) {
			return $config;
		}

		if ( is_readable( $config ) ) {
			return include $config;
		}
	}

	/*******************************
	 * Activation Workers
	 ******************************/

	/**
	 * Plugin activation stuff.  When the plugin activates,
	 * we may need to handle flushing the rewrites for things
	 * like custom post type and/or taxonomies.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public function activate() {
		if ( ! $this->config->has( 'plugin_activation_keys' ) ) {
			return;
		}

		$this->add_rewrite_rules();

		$activation_keys = $this->get_activation_keys();
		foreach( $activation_keys as $key ) {
			$instance = $this->fulcrum[ $key ];
			
			$instance->register();
		}

		flush_rewrite_rules();
	}

	/**
	 * If you need to add rewrite rules, overload this method.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	protected function add_rewrite_rules() {
		// it's here if you need it.
	}

	/**
	 * Get activation keys (getter).
	 *
	 * @since 1.0.2
	 *
	 * @return array
	 */
	public function get_activation_keys() {
		if ( $this->config->has( 'plugin_activation_keys' ) ) {
			return $this->config->plugin_activation_keys;
		}
		return array();
	}
}