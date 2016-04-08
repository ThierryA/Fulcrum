<?php

/**
 * Post Model Contract
 *
 * @package     Fulcrum\Model
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Model;

interface Post_Model_Contract {

	/**
	 * Get the property's value
	 *
	 * @since 1.0.0
	 *
	 * @param string $key
	 * @param string $sub_key
	 * @param mixed $default_value
	 *
	 * @return mixed
	 */
	public function get( $key, $sub_key = '', $default_value = null );

	/**
	 * Checks if there is an adjacent post
	 *
	 * @since 1.0.0
	 *
	 * @param bool $prev
	 *
	 * @return bool
	 */
	public function has_adjacent_post( $prev = true );
}