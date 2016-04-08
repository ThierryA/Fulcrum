<?php namespace Fulcrum\Container;

/**
 * IoC Container Contract
 *
 * @package     Fulcrum\Container
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

interface Container_Contract {

	/**
	 * Get the Core Instance
	 *
	 * @since 1.1.0
	 *
	 * @return self
	 */
	public static function getContainer();

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
	public function get( $unique_id );

	/**
	 * Checks if a parameter or an object is set.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $unique_id The unique identifier for the parameter or object
	 * @return bool
	 */
	public function has( $unique_id );

	/**
	 * Register Concrete closures into the Container
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 * @param string $unique_id
	 * @return mixed
	 */
	public function register_concrete( array $config, $unique_id );
}
