<?php namespace Fulcrum;

/**
 * Fulcrum Contract
 *
 * @package     Fulcrum
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        http://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

use Fulcrum\Config\Config_Contract;

interface Fulcrum_Contract {

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

//	/**
//	 * Register Service Providers into the container
//	 *
//	 * @since 1.0.0
//	 *
//	 * @param Config_Contract|array $service_providers
//	 * @return null
//	 */
//	public function register_service_providers( &$service_providers );
//
//	/**
//	 * Register Assets into the Asset Manager
//	 *
//	 * @since 1.0.0
//	 *
//	 * @param Config_Contract $config
//	 * @return null|false
//	 */
//	public function register_assets( Config_Contract $config );
//
//	/**
//	 * Checks if the asset is registered in the Assets Manager
//	 *
//	 * @since 1.0.0
//	 *
//	 * @param string $asset_handle
//	 * @return bool
//	 */
//	public function is_asset_registered( $asset_handle );


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
