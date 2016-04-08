<?php

/**
 * Custom Post Type Service Provider
 *
 * @package     Fulcrum\Custom\Post_Type
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Shortcode;

use Fulcrum\Foundation\Service_Provider\Provider;
use Fulcrum\Config\Config;

class Shortcode_Provider extends Provider {

	/**
	 * Flag for whether to load the defaults or not.
	 *
	 * @var bool
	 */
	protected $has_defaults = false;

	/**
	 * Flag to indicate whether to skip the queue and register directly into the Container.
	 *
	 * @var bool
	 */
	protected $skip_queue = true;

	/**
	 * Initialize events.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_events() {
		add_filter( 'widget_text', 'do_shortcode' );
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
				return new $config['classname'](
					new Config(
						$config['config']
					)
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
			'autoload'  => true,
			'classname' => 'Fulcrum\Custom\Shortcode\Shortcode',
			'config'    => ''
		);
	}
}
