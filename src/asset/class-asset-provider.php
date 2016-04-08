<?php

/**
 * Asset Service Provider
 *
 * @package     Fulcrum\Asset
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Asset;

use Fulcrum\Foundation\Service_Provider\Provider;

class Asset_Provider extends Provider {

	/**
	 * Flag to indicate whether to skip the queue and register directly into the Container.
	 *
	 * @var bool
	 */
	protected $skip_queue = true;

	protected function init_events() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue each of the assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue() {
		foreach ( $this->unique_ids as $unique_id ) {
			$this->fulcrum[ $unique_id ]->register();
		}
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
	public function get_concrete( array $config, $unique_id = '' ) {

		$service_provider = array(
			'autoload' => $config['autoload'],
			'concrete' => function ( $container ) use ( $config ) {
				$classname = $config['is_script'] ? 'Fulcrum\Asset\Repo\Script' : 'Fulcrum\Asset\Repo\Style';

				return new $classname(
					$config['handle'],
					$this->instantiate_config( $config )
				);
			},
		);

		return $service_provider;
	}

	/**
	 * Checks if an asset is registered
	 *
	 * @since 1.0.0
	 *
	 * @param string $handle Specify either the full dot notation path to the asset handle (i.e. with the config)
	 *                          or just the handle itself.  If only the handle, then we use $is_js to find the
	 *                          the asset handle's full path.
	 *
	 * @return bool
	 */
	public function is_registered( $handle ) {
		return $this->queued->has( $handle );
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
			'autoload'  => true,
			'handle'    => '',
			'is_script' => true,
			'config'    => array(),
		);
	}
}
