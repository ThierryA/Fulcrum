<?php

/**
 * Base Shortcode
 *
 * @package     Fulcrum\Custom\Shortcodes
 * @since       1.0.1
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Shortcode;

use InvalidArgumentException;
use RuntimeException;
use Fulcrum\Config\Config_Contract;

class Shortcode implements Shortcode_Contract {

	/**
	 * Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Shortcode attributes
	 *
	 * @var array
	 */
	protected $atts = array();

	/**
	 * Shortcode content
	 *
	 * @var string|null
	 */
	protected $content;

	/**
	 * Instantiate the Shortcode object
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Runtime configuration parameters.
	 */
	public function __construct( Config_Contract $config ) {
		$this->config = $config;

		if ( $this->is_config_valid() ) {
			add_shortcode( $this->config->shortcode, array( $this, 'render_callback' ) );
		}
	}

	/**
	 * Shortcode callback which merges the attributes, calls the render() method to build
	 * the HTML, and then returns it.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Shortcode attributes
	 * @param string $content Content between the opening & closing shortcode declarations
	 *
	 * @return string               Shortcode HTML
	 */
	public function render_callback( $atts, $content = null ) {
		$this->atts    = shortcode_atts( $this->config->defaults, $atts, $this->config->shortcode );
		$this->content = $content;

		return $this->render();
	}

	/**************
	 * Helpers
	 *************/

	/**
	 * Build the Shortcode HTML and then return it.
	 *
	 * NOTE: This is the method to extend for enhanced shortcodes (i.e. which extend this class).
	 *
	 * @since 1.0.0
	 *
	 * @return string Shortcode HTML
	 */
	protected function render() {

		ob_start();
		include( $this->config->view );

		return ob_get_clean();
	}

	/**
	 * Get the ID from the attributes.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_id() {
		if ( ! $this->atts['id'] ) {
			return '';
		}

		return sprintf( ' id="%s"', esc_attr( $this->atts['id'] ) );
	}

	/**
	 * Get the classname from the attributes.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_class() {
		if ( ! $this->atts['class'] ) {
			return '';
		}

		return ' ' . esc_attr( $this->atts['class'] );
	}

	/**
	 * Checks if the config is valid to start
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	protected function is_config_valid() {
		if ( ! $this->config->has( 'shortcode' ) ||
		     ! $this->config->is_array( 'defaults' ) ||
		     ! $this->config->has( 'view' )
		) {
			throw new InvalidArgumentException( __( 'Invalid config for shortcode.', 'fulcrum' ) );
		}

		if ( ! $this->is_no_view_required() && ! is_readable( $this->config->view ) ) {
			throw new RuntimeException( sprintf( __( 'The specified view file [%s] is not readable.', 'fulcrum' ), $this->config->view ) );
		}

		return true;
	}

	/**
	 * Checks if a no view is required.
	 *
	 * @since 1.0.1
	 *
	 * @return bool
	 */
	protected function is_no_view_required() {
		return $this->config->has( 'no_view' ) && $this->config->no_view;
	}
}
