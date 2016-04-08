<?php

/**
 * Service Provider Contract
 *
 * @package     Fulcrum\Foundation\Service_Provider
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Foundation\Service_Provider;

interface Provider_Contract {

	/**
	 * Register a Post Type instance into the container.
	 *
	 * @since 1.0.0
	 *
	 * @param array $concrete_config Concrete's runtime configuration parameters.
	 * @param string $unique_id Container's unique key ID for this instance.
	 *
	 * @returns mixed
	 */
	public function register( array $concrete_config, $unique_id );

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
	public function get_concrete( array $config, $unique_id = '' );
}
