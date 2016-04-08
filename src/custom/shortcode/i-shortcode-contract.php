<?php

/**
 * Shortcode Contract
 *
 * @package     Fulcrum\Custom\Shortcode
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Shortcode;

interface Shortcode_Contract {

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
	public function render_callback( $atts, $content = null ) ;
}
