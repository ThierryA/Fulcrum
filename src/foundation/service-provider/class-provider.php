<?php

/**
 * Service Provider base abstract class
 *
 * @package     Fulcrum\Foundation\Service_Provider
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Foundation\Service_Provider;

use Fulcrum\Config\Config;
use InvalidArgumentException;
use RuntimeException;
use Fulcrum\Fulcrum_Contract;

abstract class Provider implements Provider_Contract {

	/**
	 * Flag to indicate whether to skip the queue and register directly into the Container.
	 *
	 * @var bool
	 */
	protected $skip_queue = false;

	/**
	 * Default concrete configuration.
	 *
	 * @var array
	 */
	protected $default_structure;

	/**
	 * Instance of Fulcrum (which is the container)
	 *
	 * @var Fulcrum_Contract
	 */
	protected $fulcrum;

	/**
	 * Concrete queue - awaiting registration or instantiation.
	 *
	 * @var array
	 */
	protected $queued = array();

	/**
	 * Array of Instances
	 *
	 * @var array
	 */
	protected $instances = array();

	/**
	 * Flag for whether to load the defaults or not.
	 *
	 * @var bool
	 */
	protected $has_defaults = true;

	/**
	 * Specifies where the default file is located.
	 *
	 * @var string
	 */
	protected $defaults_location = 'config/defaults.php';

	/**
	 * Default parameters.
	 *
	 * @var array|string
	 */
	protected $defaults = '';

	/**
	 * Array of unique IDs in Container.
	 *
	 * @var array
	 */
	protected $unique_ids = array();

	/**
	 * Factory constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Fulcrum_Contract $fulcrum
	 */
	public function __construct( Fulcrum_Contract $fulcrum ) {
		$this->fulcrum = $fulcrum;

		$this->default_structure = $this->get_concrete_default_structure();

		$this->defaults = $this->init_defaults();

		$this->init_events();
	}

	/**
	 * Initialize the defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_defaults() {
		if ( ! $this->has_defaults ) {
			return '';
		}

		$directory = fulcrum_get_calling_class_directory( $this );

		$this->defaults_location = $directory . $this->defaults_location;

		return $this->load_file( $this->defaults_location );
	}

	/**
	 * Initialize events.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_events() {
		// do nothing.
	}

	/**
	 * Register a concrete into the container.
	 *
	 * @since 1.0.0
	 *
	 * @param array $concrete_config Concrete's runtime configuration parameters.
	 * @param string $unique_id Container's unique key ID for this instance.
	 *
	 * @returns mixed
	 */
	public function register( array $concrete_config, $unique_id ) {
		$concrete_config = $this->parse_with_default_structure( $concrete_config );

		if ( ! $this->is_unique_id_valid( $unique_id ) || ! $this->is_concrete_config_valid( $unique_id, $concrete_config ) ) {
			return false;
		}

		$concrete = $this->get_concrete( $concrete_config, $unique_id );

		if ( $this->skip_queue ) {
			return $this->register_concrete( $concrete, $unique_id );
		}

		return $this->queued[ $unique_id ] = $concrete;
	}

	/**
	 * Register all of the queued concretes into the Container.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_queue() {
		array_walk( $this->queued, array( $this, 'register_concrete' ) );
	}

	/***************************
	 * Helpers
	 **************************/

	/**
	 * Register the concrete into the Container.
	 *
	 * @since 1.0.0
	 *
	 * @param array $concrete Array for the concrete to be registered.
	 * @param string $unique_id Container's unique key ID for this instance.
	 *
	 * @return mixed
	 */
	protected function register_concrete( array $concrete, $unique_id ) {
		$this->unique_ids[] = $unique_id;

		return $this->fulcrum->register_concrete( $concrete, $unique_id );
	}

	/**
	 * Loads the file
	 *
	 * @since 1.0.0
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	protected function load_file( $file ) {
		if ( $this->is_file_valid( $file ) ) {
			return include $file;
		}
	}

	/**
	 * Build the config file's full qualified path
	 *
	 * @since 1.0.0
	 *
	 * @param string $file
	 *
	 * @return bool
	 *
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function is_file_valid( $file ) {
		if ( ! $file ) {
			throw new InvalidArgumentException( __( 'A file name is required for the defaults configuration file.', 'fulcrum' ) );
		}

		if ( ! is_readable( $file ) ) {
			$message = sprintf( '%s %s', __( 'The specified defaults config file is not readable', 'fulcrum' ), $file );

			throw new RuntimeException( $message );
		}

		return true;
	}

	/**
	 * Checks if the unique id is valid.  Else it throws an error.
	 *
	 * @since 1.0.0
	 *
	 * @param string $unique_id Container's unique key ID for this instance.
	 *
	 * @throws InvalidArgumentException
	 * @return bool
	 */
	protected function is_unique_id_valid( $unique_id ) {
		if ( ! $unique_id ) {
			throw new InvalidArgumentException( sprintf(
				__( 'For the service provider [%s], the container unique ID cannot be empty.', 'fulcrum' ),
				__CLASS__
			) );
		}

		return true;
	}

	/**
	 * Checks if the parameters are valid.
	 *
	 * @since 1.0.0
	 *
	 * @param string $unique_id Container's unique key ID for this instance.
	 * @param array $concrete_config Concrete's runtime configuration parameters.
	 *
	 * @throws InvalidArgumentException
	 * @return bool
	 */
	protected function is_concrete_config_valid( $unique_id, array $concrete_config ) {
		$is_valid = false;

		foreach ( $this->default_structure as $key => $value ) {
			$is_valid = $this->element_exists_and_is_configured( $key, $concrete_config );

			if ( ! $is_valid ) {
				throw new InvalidArgumentException( sprintf(
					__( 'For the service provider for unique ID [%s], the %s cannot be empty. [Class %s]', 'fulcrum' ),
					$unique_id, $key, __CLASS__
				) );
			}
		}

		return $is_valid;
	}

	/**
	 * Checks if the element exists and is configured in the array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key to validate.
	 * @param array $array Array to check.
	 *
	 * @return bool
	 */
	protected function element_exists_and_is_configured( $key, $array ) {
		return array_key_exists( $key, $array ) && $array[ $key ] !== '';
	}

	/**
	 * Get the concrete based upon the configuration supplied.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Runtime configuration parameters.
	 * @param string $unique_id Container's unique key ID for this instance.
	 *
	 * @return array
	 */
	abstract public function get_concrete( array $config, $unique_id = '' );

	/**
	 * Get the default structure for the concrete.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_concrete_default_structure() {
		return array(
			'autoload' => false,
			'config'   => ''
		);
	}

	/**
	 * Merge the given array with the default array structure for the concrete.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Array to merge with the default structure.
	 *
	 * @return array
	 */
	protected function parse_with_default_structure( array $args ) {
		return array_merge( $this->default_structure, $args );
	}

	/**
	 * Checks if the queue has concretes that need to be processed.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function queue_has_concretes() {
		return is_array( $this->queued ) && ! empty( $this->queued );
	}

	/**
	 * Instantiate the config.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Runtime parameters.
	 *
	 * @return Config
	 */
	protected function instantiate_config( $config ) {
		return new Config(
			$config['config'],
			$this->has_defaults ? $this->defaults : ''
		);
	}
}
