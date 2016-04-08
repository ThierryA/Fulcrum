<?php

/**
 * Post Type Permalink Service Provider
 *
 * @package     Fulcrum\Custom\Post_Type\Permalink
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Post_Type\Permalink;

use Fulcrum\Foundation\Service_Provider\Provider;
use Fulcrum\Config\Config;

class Permalink_Provider extends Provider {

	/**
	 * Flag to indicate whether to skip the queue and register directly into the Container.
	 *
	 * @var bool
	 */
	protected $skip_queue = true;

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
	public function get_concrete( array $config, $unique_id = '' ) {

		$service_provider = array(
			'autoload' => $config['autoload'],
			'concrete' => function ( $container ) use ( $config ) {
				$config_obj = $this->instantiate_config( $config );

				return new Custom_Permalink(
					$config_obj,
					new Post_Name_Slug( $config_obj ),
					new Permalink_Query( $config_obj )
				);
			},
		);

		return $service_provider;
	}

	/**
	 * Get the default structure for the concrete.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_concrete_default_structure() {
		return array(
			'autoload'       => false,
			'config'         => array(),
		);
	}
}
