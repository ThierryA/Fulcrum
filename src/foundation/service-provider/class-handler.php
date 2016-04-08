<?php

/**
 * Service Provider Manager - Handles loading the service providers
 *
 * @package     Fulcrum\Foundation\Service_Provider
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Foundation\Service_Provider;

use InvalidArgumentException;
use Fulcrum\Fulcrum_Contract;

class Handler {

	/**
	 * Instance of Fulcrum (which is the container)
	 *
	 * @var Fulcrum_Contract
	 */
	protected $fulcrum;

	/**
	 * Factory constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Fulcrum_Contract $fulcrum
	 */
	public function __construct( Fulcrum_Contract $fulcrum ) {
		$this->fulcrum = $fulcrum;
	}

	/**
	 * Register the providers.
	 *
	 * @since 1.0.0
	 *
	 * @param array $providers Array of providers.
	 *
	 * @return void
	 */
	public function register( array $providers ) {
		array_walk( $providers, array( $this, 'register_into_container' ) );
	}

	/**
	 * Register the provider into the container.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classname Provider's classname
	 * @param string $unique_id Unique ID for the provider when in the container.
	 *
	 * @return void
	 */
	protected function register_into_container( $classname, $unique_id ) {
		$concrete = $this->get_concrete_config( $classname );

		if ( is_array( $concrete ) ) {
			$this->fulcrum->register_concrete( $concrete, $unique_id );
		}
	}

	/**
	 * Build the provider's concrete configuration array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classname Provider's classname
	 *
	 * @return null|array
	 */
	protected function get_concrete_config( $classname ) {

		if ( ! $this->is_valid_class( $classname ) ) {
			return;
		}

		$concrete = array(
			'autoload' => true,
			'concrete' => function ( $container ) use ( $classname ) {
				return new $classname( $this->fulcrum );
			},
		);

		return $concrete;
	}

	/**
	 * Checks if the classname is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classname Provider's classname
	 *
	 * @throws InvalidArgumentException
	 * @return bool
	 */
	protected function is_valid_class( $classname ) {
		if ( class_exists( $classname ) ) {
			return true;
		}

		throw new InvalidArgumentException( sprintf(
			__( 'The classname of [%s] was not found and could not be registered as a service provider.', 'fulcrum' ),
			$classname
		) );
	}
}
