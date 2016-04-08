<?php

/**
 * Widget Service Provider
 *
 * @package     Fulcrum\Custom\Widget
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Widget;

use Fulcrum\Foundation\Service_Provider\Provider;
use InvalidArgumentException;

class Widget_Provider extends Provider {

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
		add_action( 'widgets_init', array( $this, 'register_widgets_callback' ) );
	}

	/**
	 * Register each widget classname with WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param array $widget_classnames Array of widget classnames to be registered.
	 * @param string $unique_id Container's unique key ID for this instance.
	 *
	 * @return void
	 */
	public function register( array $widget_classnames, $unique_id = '' ) {
		$this->queued = array_merge( $this->queued, $widget_classnames );
	}

	/**
	 * If there are widgets registered, iterate through and register each one.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_widgets_callback() {
		if ( $this->queue_has_concretes() ) {
			array_walk( $this->queued, array( $this, 'register_widget' ) );
		}

		do_action( 'fulcrum_widget_init', $this->fulcrum );
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
		return array();
	}

	/**
	 * Register the widget (callback) with WordPress using register_widget().
	 *
	 * @since 1.0.0
	 *
	 * @param string $classname Widget classname to be registered.
	 *
	 * @return void
	 */
	protected function register_widget( $classname ) {
		if ( $this->is_valid_widget( $classname ) ) {
			register_widget( $classname );
		}
	}

	/**
	 * Checks if the classname is valid; else it throws an error.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classname Widget classname
	 *
	 * @return bool
	 */
	protected function is_valid_widget( $classname ) {

		if ( class_exists( $classname ) ) {
			return true;
		}

		throw new InvalidArgumentException( sprintf(
			__( 'The class [%s] does not exist.  Therefore the widget cannot be registered.', 'fulcrum' ),
			$classname
		) );
	}
}
