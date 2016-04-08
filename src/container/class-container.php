<?php namespace Fulcrum\Container;

/**
 * Dependency Injection Container - extends the functionality of Pimple DI Container.
 *
 * @package     Fulcrum\Container
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

use Pimple\Container as Pimple;

class Container extends Pimple implements Container_Contract {

	/**
	 * Instance of Container
	 *
	 * @var Container_Contract
	 */
	static $instance;

	/**************************
	 * Instantiate & Initialize
	 *************************/

	/**
	 * Instantiate the container
	 *
	 * @since 1.0.0
	 *
	 * @return self
	 */
	public function __construct() {
		self::$instance = $this;
		parent::__construct( $this->config->initial_parameters );
	}

	/****************************
	 * Public Methods
	 ***************************/

	/**
	 * Get the Core Instance
	 *
	 * @since 1.1.0
	 *
	 * @return self
	 */
	public static function getContainer() {
		return self::$instance;
	}

	/**
	 * Gets a parameter or an object.
	 *
	 * @since 1.0.0
	 *
	 * @param string $unique_id The unique identifier for the parameter or object
	 * @return mixed The value of the parameter or an object
	 *
	 * @throws \InvalidArgumentException if the identifier is not defined
	 */
	public function get( $unique_id ) {
		return $this->offsetGet( $unique_id );
	}

	/**
	 * Checks if a parameter or an object is set.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $unique_id The unique identifier for the parameter or object
	 * @return bool
	 */
	public function has( $unique_id ) {
		return $this->offsetExists( $unique_id );
	}

	/**
	 * Register Concrete closures into the Container
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 * @param string $unique_id
	 * @return mixed
	 */
	public function register_concrete( array $config, $unique_id ) {
		$this[ $unique_id ] = $config['concrete'];

		if ( ! $config['autoload'] ) {
			return;
		}

		if ( true === $config['autoload'] ) {
			return $this[ $unique_id ];
		}

		if ( is_callable( $config['autoload'] ) ) {
			call_user_func( $config['autoload'], $this[ $unique_id ] );
		}
	}
}
