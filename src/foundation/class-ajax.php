<?php namespace Fulcrum\Foundation;

/**
 * AJAX Class (abstract)
 *
 * @package     Fulcrum\Foundation
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://wpdevelopersclub.com/
 * @license     GNU General Public License 2.0+
 */

use Fulcrum\Fulcrum_Contract;
use Fulcrum\Config\Config_Contract;

abstract class AJAX {

	/**
	 * Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Instance of Fulcrum
	 *
	 * @var Fulcrum_Contract
	 */
	protected $fulcrum;

	/**
	 * Error message
	 *
	 * @var string
	 */
	protected $error_message = '';

	/**
	 * Return data packet
	 *
	 * @var array
	 */
	protected $data_packet = array();

	/**
	 * Error code
	 *
	 * @var int
	 */
	protected $error_code = 0;

	/******************************
	 * Instantiation & Initialization
	 *****************************/

	/**
	 * Instantiate the AJAX Object
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config
	 * @param Fulcrum_Contract $fulcrum
	 */
	public function __construct( Config_Contract $config, Fulcrum_Contract $fulcrum = null ) {
		$this->config  = $config;
		$this->fulcrum = $fulcrum;

		$this->init();
		$this->init_events();
	}

	/**
	 * Initialize
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init() { /* do nothing */
	}

	/**
	 * Initialize the events
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	abstract protected function init_events();

	/******************************
	 * Helpers
	 *****************************/

	/**
	 * AJAX Response Handler - Builds the response and returns it to the JavaScript
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function ajax_response_handler() {
		echo json_encode( array(
			'success'      => $this->error_message ? 0 : 1,
			'errorMessage' => $this->error_message,
			'errorCode'    => $this->error_code,
			'data'         => $this->error_message ? '' : $this->data_packet,
		) );

		die();
	}

	/**
	 * Initialize AJAX
	 *
	 * @since  1.0.0
	 *
	 * @return null
	 */
	protected function init_ajax() {
		$this->init_properties();

		$this->security_check();
	}

	/**
	 * Initialize the properties
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_properties() {
		$this->error_message = '';
		array_walk( $this->config->data_packet, array( $this, 'init_data_packet' ) );
		$this->error_code = 0;
	}

	/**
	 * Initialize data packet
	 *
	 * @since 1.0.0
	 *
	 * @param string $filter
	 * @param string $key
	 *
	 * @return null
	 */
	protected function init_data_packet( $filter, $key ) {
		if ( ! array_key_exists( $key, $_POST ) ) {
			$this->error_message = $this->config->messages[ $key ];
			$this->ajax_response_handler();
		}
		$this->data_packet[ $key ] = $filter( $_POST[ $key ] );
	}

	/**
	 * Checks the AJAX Referer.  If invalid, dies with a security message.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function security_check() {
		check_ajax_referer( $this->config->nonce_key, 'security' );
	}
}
