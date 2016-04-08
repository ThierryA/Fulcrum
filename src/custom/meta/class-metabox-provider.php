<?php

/**
 * Metabox Service Provider
 *
 * @package     Fulcrum\Custom\Meta
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Meta;

use Fulcrum\Foundation\Service_Provider\Provider;
use Fulcrum\Config\Config;

class Metabox_Provider extends Provider {

	/**
	 * Flag for whether to load the defaults or not.
	 *
	 * @var bool
	 */
	protected $has_defaults = false;

	/**
	 * Initialize events.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_events() {
		if ( is_admin() ) {
			add_action( 'load-post.php', array( $this, 'load_metaboxes' ) );
			add_action( 'load-post-new.php', array( $this, 'load_metaboxes' ) );
		}
	}

	/**
	 * Register a concrete into the container.
	 *
	 * @since 1.0.0
	 *
	 * @param array $concrete_config Concrete's runtime configuration parameters.
	 * @param string $unique_id Container's unique key ID for this instance.
	 *
	 * @returns mixed
	 */
	public function register( array $concrete_config, $unique_id ) {
		if ( ! is_admin() ) {
			return;
		}
		parent::register( $concrete_config, $unique_id );
	}

	/**
	 * Instantiate the metaboxes out of the container
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function load_metaboxes() {
		if ( ! $this->queue_has_concretes() ) {
			return;
		}

		foreach ( $this->queued as $unique_id => $concrete ) {
			$this->register_concrete( $concrete, $unique_id );

			$this->instances[ $unique_id ] = $this->fulcrum[ $unique_id ];
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
				return new Metabox(
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
			'autoload'       => false,
			'config'         => ''
		);
	}
}
