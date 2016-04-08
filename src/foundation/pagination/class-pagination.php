<?php

/**
 * Pagination
 *
 * @package     Fulcrum\Foundation\Pagination
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Foundation\Pagination;

class Pagination implements Pagination_Contract {

	public $view = 'views/pagination.php';

	protected $current_page_number;
	protected $per_page;
	protected $total;
	protected $total_pages;
	protected $permalink;
	protected $permalink_query_string;

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
	public function render( $query, $current_page_number, $per_page, $echo = false, $total = false ) {
		$this->init_properties( $query, $current_page_number, $per_page, $total );
		if ( $this->total_pages < 2 ) {
			return '';
		}

		$this->permalink              = get_permalink();
		$this->permalink_query_string = $this->remove_query_string_arg();
		$prev_uri                     = $this->get_previous_uri();
		$next_uri                     = $this->get_next_uri();

		if ( ! $echo ) {

			ob_start();
			include( $this->view );
			$pagination = ob_get_clean();

			return $pagination;
		}

		include( $this->view );
	}

	/**
	 * Initialize the properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $query
	 * @param integer $current_page_number
	 * @param integer $per_page
	 * @param bool $total
	 *
	 * @return void
	 */
	protected function init_properties( $query, $current_page_number, $per_page, $total ) {
		$this->current_page_number = (int) $current_page_number;
		$this->per_page            = (int) $per_page;
		$this->total               = $this->validate_total( $query, $total );

		$this->total_pages = $this->per_page > 0 ? ceil( $this->total / $this->per_page ) : 0;
	}

	/**
	 * Remove query string arg
	 *
	 * Note: need to strip off the page from the incoming query string
	 *
	 * @param  string $arg_to_remove Default 'page'
	 *
	 * @return string                Returns the query string
	 */
	protected function remove_query_string_arg( $arg_to_remove = 'pageNum' ) {
		$query_string = $_SERVER['QUERY_STRING'];
		$qsargs       = explode( '&', $query_string );

		foreach ( $qsargs as $key => $arg ) {
			if ( false === strpos( $arg, $arg_to_remove ) ) {
				continue;
			}

			unset( $qsargs[ $key ] );

			return implode( '&', $qsargs );
		}

		return $query_string;
	}

	/**
	 * Build the URI with all of the query strings (including the original)
	 *
	 * @since  1.0.0
	 *
	 * @param  integer $page_number Page number to append
	 *
	 * @return string                 URI
	 */
	protected function build_uri( $page_number ) {
		return sprintf( '%s?%spageNum=%s', $this->permalink, $this->permalink_query_string
			? "{$this->permalink_query_string}&"
			: '', $page_number
		);
	}

	/**
	 * Validate the total
	 *
	 * @since 2.0.0
	 *
	 * CHG 06132015 To fix the pagination issue
	 *
	 * @param $query
	 * @param $total
	 *
	 * @return int
	 */
	protected function validate_total( $query, $total ) {
		if ( false !== $total ) {
			return $total;
		}

		global $wpdb;

		$total = 0 === strpos( $query, 'SELECT' )
			? count( $wpdb->get_results( $query ) )
			: absint( $wpdb->get_var( "SELECT COUNT(*) {$query}" ) );

		return $total;
	}

	/**
	 * Get Previous URI.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_previous_uri() {
		if ( $this->current_page_number > 2 ) {
			return $this->build_uri( $this->current_page_number - 1 );
		}

		return sprintf( '%s:%s', $this->permalink, $this->permalink_query_string );
	}

	/**
	 * Get the "next" URI.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_next_uri() {
		return $this->current_page_number < $this->total_pages
			? $this->build_uri( $this->current_page_number + 1 )
			: '';
	}

	/**
	 * Get the page URI.
	 *
	 * @since 1.0.0
	 *
	 * @param int $page_number
	 *
	 * @return string
	 */
	protected function get_page_uri( $page_number ) {
		if ( $page_number > 1 ) {
			return $this->build_uri( $page_number );
		}

		return sprintf( '%s?%s', $this->permalink, $this->permalink_query_string );
	}

	/**
	 * Get the page class.
	 *
	 * @since 1.0.0
	 *
	 * @param int $page_number
	 *
	 * @return string
	 */
	protected function get_page_class( $page_number ) {
		return $page_number == $this->current_page_number ? 'active' : '';
	}
}
