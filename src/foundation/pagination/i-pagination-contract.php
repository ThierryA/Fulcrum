<?php

/**
 * Pagination Contract
 *
 * @package     Fulcrum\Foundation\Pagination
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Foundation\Pagination;

interface Pagination_Contract {

	/**
	 * Render the pagination
	 *
	 * @since 1.0.0
	 *
	 * @param string $query
	 * @param integer $current_page_number
	 * @param integer $per_page
	 * @param bool $echo
	 * @param bool $total
	 *
	 * @return string
	 */
	public function render( $query, $current_page_number, $per_page, $echo = false, $total = false );
}