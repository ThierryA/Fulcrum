<?php namespace Fulcrum\Config;

/**
 * Configuration Factory
 *
 * @package     Fulcrum\Config
 * @since       1.0.0
 * @author      hellofromTonya, Alain Schlesser, Gary Jones
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

class Factory {

	/**
	 * Load and return the Config object
	 *
	 * @since 1.0.0
	 *
	 * @param  string|array $config     File path and filename to the config array; or it is the
	 *                                  configuration array.
	 * @param  string|array $defaults   Specify a defaults array, which is then merged together
	 *                                  with the initial config array before creating the object.
	 * @returns Fulcrum Returns the Config object
	 */
	public static function create( $config, $defaults = '' ) {
		return new Config( $config, $defaults );
	}
}
