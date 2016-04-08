<?php

/**
 * Asset base abstract class
 *
 * @package     Fulcrum\Asset
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Fulcrum\Asset;

use InvalidArgumentException;
use Fulcrum\Config\Config_Contract;

abstract class Asset implements Asset_Contract {

	/**
	 * Unique handle ID for the WordPress system.
	 *
	 * @var string
	 */
	protected $handle;

	/**
	 * Registered assets configurations
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/***************************
	 * Instantiate & Initialize
	 **************************/

	/**
	 * Instantiate the Asset object
	 *
	 * @since 1.0.0
	 *
	 * @param string $handle Unique ID which is registered with WordPress.
	 * @param Config_Contract $config Default configuration file
	 *
	 * @return self
	 */
	public function __construct( $handle, Config_Contract $config ) {
		$this->handle = $handle;
		$this->config = $config;

		$this->validate_config();
	}


	/**
	 * Merge the given array with the default array structure for the config.
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Default configuration file
	 *
	 * @return array
	 */
	protected function parse_with_default_structure( Config_Contract $config ) {
		$defaults = $this->get_default_structure();

		return array_merge( $defaults, $config );
	}

	/*****************
	 * Public
	 ****************/

	/**
	 * Checks if an asset has been enqueued
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_enqueued() {
		return wp_script_is( $this->handle, 'enqueued' );
	}

	/**
	 * Register each of the asset (enqueues it)
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function register() {
		if ( $this->are_conditions_set_to_enqueue() ) {
			$this->enqueue();
		}
	}

	/**
	 * De-register each of the asset
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	abstract public function deregister();

	/*****************
	 * Helpers
	 ****************/

	/**
	 * Description.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function are_conditions_set_to_enqueue() {
		if ( ! $this->config->pre_conditional_load ) {
			return true;
		}

		if ( $this->config->has( 'load_on_page' ) ) {
			return is_page( $this->config->load_on_page );
		}

		return true;
	}

	/**
	 * Validates the configuration parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	protected function validate_config() {
		$message = '';

		if ( ! $this->handle ) {
			$message = __( 'A unique ID is required for the asset.', 'fulcrum' );

		} elseif ( ! $this->config->file ) {
			$message = sprintf( '%s [%s]', __( 'This asset requires a fully qualified file path and name', 'fulcrum' ), $this->handle );

		} elseif ( ! $this->config->version ) {
			$message = sprintf( '%s [%s]', __( 'This asset requires a version', 'fulcrum' ), $this->handle );
		}

		if ( $message ) {
			throw new InvalidArgumentException( $message );
		}
	}
}
