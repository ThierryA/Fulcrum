<?php

/**
 * Controller Contract
 *
 * @package     Fulcrum\Routing
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Routing;

interface Controller_Contract {

	/**
	 * Get the specific record
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $record_value
	 * @param string $unique_column_name Optional. Column name for the unique value, e.g. id, slug
	 *
	 * @return Model
	 */
	public function get_record( $record_value, $unique_column_name = 'id' );

	/**
	 * Get all of the records per the specified query args
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args
	 *
	 * @return array|false
	 */
	public function get_all_records( $query_args = array() );

	/**
	 * Fetch the specified column from the db based on where configuration
	 *
	 * @since  1.0.0
	 *
	 * @param  string $where_column_name Column name for the WHERE
	 * @param mixed $where_value Value to fetch the column
	 * @param  string $select_column_name Db column name to fetch
	 *
	 * @return string|false Returns the value or false
	 */
	public function get_column( $where_column_name, $where_value, $select_column_name );
}