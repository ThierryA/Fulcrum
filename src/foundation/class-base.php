<?php namespace Fulcrum\Foundation;

/**
 * Base Class (abstract)
 *
 * @package     Fulcrum\Foundation
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://wpdevelopersclub.com/
 * @license     GNU General Public License 2.0+
 */

use Fulcrum\Config\Config_Contract;
use Fulcrum\Fulcrum_Contract;

abstract class Base {

	/**
	 * Instance of Fulcrum
	 *
	 * @var Fulcrum_Contract
	 */
	protected $fulcrum;

	/**
	 * Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Path to this class' defaults folder
	 *
	 * @var string
	 */
	protected static $defaultsFolder = 'defaults/';

	/**
	 * Defaults filename
	 *
	 * @var string
	 */
	protected static $defaultsFile = '';

	/**************
	 * Getters
	 *************/

	/**
	 * Get a property if it exists; else return the default_value
	 *
	 * @since 1.0.0
	 *
	 * @param string $property
	 * @param mixed $default_value
	 *
	 * @return mixed|null
	 */
	public function get( $property, $default_value = null ) {
		if ( property_exists( $this, $property ) ) {
			return $this->$property;
		}

		return $default_value;
	}

	/**
	 * Slower magical getter
	 *
	 * @since 1.0.0
	 *
	 * @param string $property
	 *
	 * @return null|mixed
	 */
	public function __get( $property ) {
		return $this->get( $property );
	}

	/*********************************
	 * Instantiation & Initialization
	 ********************************/

	/**
	 * Instantiate the object
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config
	 * @param Fulcrum_Contract $fulcrum
	 *
	 * @return self|null
	 */
	public function __construct( Config_Contract $config, Fulcrum_Contract $fulcrum = null ) {
		$this->config  = $config;
		$this->fulcrum = $fulcrum;
	}

	/*********************************
	 * Public Methods
	 ********************************/

	/**
	 * Wrapper which checks if the parameter exists in $config.
	 *
	 * @since 1.0.0
	 *
	 * @param string $parameter
	 *
	 * @return bool
	 */
	public function config_has( $parameter ) {
		if ( empty( $this->config ) ) {
			return false;
		}

		return $this->config->has( $parameter );
	}

	/*************************
	 * Defaults File Locator
	 ************************/

	/**
	 * Get the Defaults File path + name
	 *
	 * @since 1.0.0
	 *
	 * @param string $defaults_folder (Optional) Specify the path to the defaults file
	 *
	 * @return string
	 */
	public static function getDefaultsFile( $defaults_folder = '' ) {
		if ( ! $defaults_folder ) {
			$defaults_folder = FULCRUM_PLUGIN_DIR . 'config/' . static::$defaultsFolder;
		}

		if ( ! $defaults_folder ) {
			return '';
		}

		$default_file = static::$defaultsFile ?: self::get_classname() . '.php';

		return $defaults_folder . $default_file;
	}

	/**
	 * Get the class shortname and format in a filename structure
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	protected static function get_classname() {
		$classname = explode( '\\', get_called_class() );
		$classname = array_pop( $classname );

		return str_replace( '_', '-', strtolower( $classname ) );
	}

	/**
	 * Get child's directory.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_child_directory() {
		$class_info = new \ReflectionClass( get_class( $this ) );
		$directory = trailingslashit( dirname( $class_info->getFileName() ) );

		return $directory;
	}
}
