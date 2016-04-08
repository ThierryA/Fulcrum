<?php

/**
 * Custom Taxonomy Contract
 *
 * This class handles a custom taxonomy object.
 *
 * @package     Fulcrum\Custom\Taxonomy
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io/
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Taxonomy;

interface Taxonomy_Contract {

	/**
	 * Time to register this taxonomy
	 *
	 * @since 1.0.0
	 *
	 * @uses self::build_args() to build up the args needed to register this taxonomy
	 * @return null
	 */
	public function register();
}
