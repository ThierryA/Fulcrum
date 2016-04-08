<?php

/**
 * Array Configuration Model
 *
 * This model:
 *      1.  handles loading the specified configuration file
 *      2.  provides public accessors to retrieve Runtime Configuration
 *          parameters.
 *
 * @package     Fulcrum\Config
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Config;

use ArrayObject;
use InvalidArgumentException;
use RuntimeException;
use Fulcrum\Support\Helpers\Arr as Arr_Helpers;

class Config extends ArrayObject implements Config_Contract {

	/**
	 * Runtime Configuration Parameters
	 *
	 * @var array
	 */
	protected $config = array();

	/***************************
	 * Instantiate & Initialize
	 **************************/

	/**
	 * Create a new configuration repository.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|array $config     File path and filename to the config array; or it is the
	 *                                  configuration array.
	 * @param  string|array $defaults   Specify a defaults array, which is then merged together
	 *                                  with the initial config array before creating the object.
	 */
	public function __construct( $config, $defaults = '' ) {
		$this->config = $this->fetch_parameters( $config );
		$this->init_defaults( $defaults );

		parent::__construct( $this->config, ArrayObject::ARRAY_AS_PROPS );
	}

	/**
	 * Initialize Default Configuration parameters & merge into the
	 * $config parameters
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $defaults
	 * @return null
	 */
	protected function init_defaults( $defaults ) {
		if ( ! $defaults ) {
			return;
		}

		$defaults = $this->fetch_parameters( $defaults );
		$this->init_defaults_in_config_array( $defaults );
	}

	/**
	 * Fetch the runtime parameters or defaults.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $location_or_array Parameters location or array.
	 *
	 * @return array
	 */
	protected function fetch_parameters( $location_or_array ) {
		if ( is_array( $location_or_array ) ) {
			return $location_or_array;
		}
		
		return $this->load_file( $location_or_array );
	}

	/**
	 * Initializing the Config with its Defaults
	 *
	 * @since 1.0.0
	 *
	 * @param array $defaults
	 * @return null
	 */
	protected function init_defaults_in_config_array( array $defaults ) {
		$this->config = array_replace_recursive( $defaults, $this->config );
	}

	/***************************
	 * Public Methods
	 **************************/

	/**
	 * Retrieves all of the runtime configuration parameters
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function all() {
		return $this->config;
	}

	/**
	 * Checks if the parameters exists.  Uses dot notation for multidimensional keys.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $parameter_key Parameter key, specified in dot notation, i.e. key.key.key
	 * @return bool
	 */
	public function has( $parameter_key ) {
		return Arr_Helpers::has( $this->config, $parameter_key );
	}

	/**
	 * Get the specified configuration value.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $parameter_key Parameter key, specified in dot notation, i.e. key.key.key
	 * @param  mixed $default
	 * @return mixed
	 */
	public function get( $parameter_key, $default = null ) {
		return Arr_Helpers::get( $this->config, $parameter_key, $default );
	}

	/**
	 * Checks if the parameter key is a valid array, which means:
	 *      1. Does it the key exists (which can be dot notation)
	 *      2. If the value is an array
	 *      3. Is the value empty, i.e. when $valid_if_not_empty is set
	 *
	 * @since 1.0.0
	 *
	 * @param string $parameter_key
	 * @param bool $valid_if_not_empty
	 * @return bool
	 */
	public function is_array( $parameter_key, $valid_if_not_empty = true ) {
		return Arr_Helpers::is_array( $this->config, $parameter_key, $valid_if_not_empty );
	}

	/**
	 * Valid the Config.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_valid() {
		if ( $this->validator ) {
			return $this->validator->isValid( $this );
		}

		return true;
	}

	/**
	 * Push a configuration in via the key
	 *
	 * @since 1.0.0
	 *
	 * @param string $parameter_key Key to be assigned, which also becomes the property
	 * @param mixed $value Value to be assigned to the parameter key
	 * @return null
	 */
	public function push( $parameter_key, $value ) {
		$this->config[ $parameter_key ] = $value;
		$this->offsetSet( $parameter_key, $value );
	}

	/**
	 * Merge a new array into this config
	 *
	 * @since 1.0.0
	 *
	 * @param array $array_to_merge
	 * @return null
	 */
	public function merge( array $array_to_merge ) {
		$this->config = array_replace_recursive( $this->config, $array_to_merge );

		array_walk( $this->config, function ( $value, $parameter_key )  {
			$this->offsetSet( $parameter_key, $value );
		} );
	}

	/***************************
	 * Helpers
	 **************************/

	/**
	 * Loads the config file
	 *
	 * @since 1.0.0
	 *
	 * @param string $config_file
	 * @return string
	 */
	protected function load_file( $config_file ) {
		if ( $this->is_file_valid( $config_file ) ) {
			return include $config_file;
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
			throw new InvalidArgumentException( __( 'A config filename must not be empty.', 'fulcrum' ) );
		}

		if ( ! is_readable( $file ) ) {
			throw new RuntimeException( sprintf( '%s %s', __( 'The specified config file is not readable', 'fulcrum' ), $file ) );
		}

		return true;
	}
}