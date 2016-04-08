<?php

/**
 * Db Helpers Contract
 *
 * @package     Fulcrum\Database
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Database;

interface Db_Helpers_Contract {

	/**
	 * Fetch the specified column from the db based on where configuration
	 *
	 * @since  1.0.0
	 *
	 * @param  string $select_column_name Db column name to fetch
	 * @param mixed $where_value Value to fetch the column
	 * @param  string $where_column_name Column name for the WHERE.
	 *                                                      Default: id
	 *
	 * @return mixed|false                              Returns the value or false
	 */
	public function get_column( $select_column_name, $where_value, $where_column_name = 'id' );

	/**
	 * Fetch the specified column from the db based on where configuration
	 *
	 * @since  1.0.0
	 *
	 * @param string $where_sql WHERE SQL
	 * @param array $where_data Array of the data to populate the WHERE SQL
	 * @param string $select_column_name Column Name to pull the value from
	 *
	 * @return string|false Returns the value or false
	 */
	public function get_column_by_where_sql( $where_sql, array $where_data, $select_column_name );


	/**
	 * Get all the records based on the configuration criteria
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_config
	 *
	 * @return array|bool
	 */
	public function get_all_joined( array $query_config );

	/**
	 * Delete the specified record
	 *
	 * @since 1.0.0
	 *
	 * @param integer $primary_key_value Record's primary key value
	 * @param string $column_name Optional. Primary Key column name
	 *
	 * @return mixed
	 */
	public function delete_record( $primary_key_value, $column_name = 'id' );

	/**
	 * Checks if the table has records per the WHERE SQL supplied
	 *
	 * @since 1.0.0
	 *
	 * @param string $where_sql
	 * @param array $where_data
	 *
	 * @return bool
	 */
	public function has_records( $where_sql, array $where_data );

	/**
	 * Sum a column for all pulled records
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 * @param string $where_sql
	 * @param array $where_data
	 *
	 * @return bool|stdClass
	 */
	public function sum( $column_name, $where_sql, array $where_data );

	/**
	 * Update columns
	 *
	 * @since 1.0.0
	 *
	 * @link    https://codex.wordpress.org/Class_Reference/wpdb#UPDATE_rows
	 *
	 * @param array $data Data to be updated
	 * @param array $where WHERE clauses in column_name => value format
	 * @param array $format Format mapping for the data, i.e. '%d'
	 * @param array $where_format Format mapping for the WHERE values
	 *
	 * @return int|false Returns the number of rows affected on success;
	 *                      else returns false.
	 */
	public function update_columns( array $data, array $where, array $format, array $where_format );


	/*******************
	 * Getters
	 ******************/

	/**
	 * Get database configuration
	 *
	 * @since 1.0.0
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	public function getConfig( $key );

	/**
	 * Count the number of records per the supplied WHERE SQL
	 *
	 * @since 1.0.0
	 *
	 * @param string $where_sql
	 * @param array $where_data
	 *
	 * @return int
	 */
	public function getRecordCount( $where_sql, array $where_data );
}
