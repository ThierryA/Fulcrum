<?php

/**
 * Base Controller
 *
 * @package     Fulcrum\Routing
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Routing;


use Fulcrum\Config\Config_Contract;
use Fulcrum\Database\Db_Helpers_Contract;
use Fulcrum\Foundation\AJAX;
use Fulcrum\Model\Model;

class Controller extends AJAX implements Controller_Contract {

	/**
	 * Instance of Database Helpers
	 *
	 * @var Db_Helpers_Contract
	 */
	protected $db_helpers;

	/******************************
	 * Instantiate & Initializers
	 *****************************/

	/**
	 * Instantiate Controller object
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract|null $config Instance of config
	 * @param Db_Helpers_Contract $db_helpers Instance of DbHelpers
	 *
	 * @return self
	 */
	public function __construct( Config_Contract $config, Db_Helpers_Contract $db_helpers ) {
		$this->db_helpers = $db_helpers;
		parent::__construct( $config );

		$this->init_events();
	}

	/**
	 * Initialize Hooks (extensible by the child class)
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_events() {
		if ( $this->config->ajax_delete_action ) {
			add_action( "wp_ajax_{$this->config->ajax_delete_action}", array( $this, 'ajax_delete_record' ) );
		}
	}

	/******************************
	 * Public Methods
	 *****************************/

	/**
	 * Get the empty model
	 *
	 * @since 1.0.0
	 *
	 * @return Model
	 */
	public function get_empty_model() {
		return new Model( $this->db_helpers->getConfig() );
	}

	/**
	 * Get the specific record
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $record_value Column's value within the database
	 * @param string $unique_column_name Optional.
	 *              Column name for the unique value, e.g. id, slug
	 *
	 * @return Model
	 */
	public function get_record( $record_value, $unique_column_name = 'id' ) {
		return new Model( $this->db_helpers->getConfig(), $record_value, $unique_column_name );
	}

	/**
	 * Get the specific record
	 *
	 * @since 1.0.0
	 *
	 * @param string $where_sql WHERE SQL
	 * @param array $where_data Array of the data to populate the WHERE SQL
	 *                                                  unique value, e.g. id, slug
	 * @param bool $return_model_when_non_found When true, returns an empty model when no record is found
	 *
	 * @return ModelContract|false
	 */
	public function get_record_by_where_sql( $where_sql, array $where_data, $return_model_when_non_found = true ) {
		return $this->db_helpers->get_record_by_where_sql( $where_sql, $where_data, $return_model_when_non_found );
	}

	/**
	 * Get all of the records per the specified query args
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args
	 *
	 * @return array|false
	 */
	public function get_all_records( $query_args = array() ) {
		if ( empty( $query_args ) ) {
			$query_args = $this->config->get_all_query_args;
		}

		return $this->db_helpers->get_all_joined( $query_args );
	}

	/**
	 * Get all of the records per the specified query args
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args
	 *
	 * @return array|false
	 */
	public function get_all_joined( $query_args = array() ) {
		return $this->db_helpers->get_all_joined( $query_args );
	}

	/**
	 * Fetch the specified column from the db based on where configuration
	 *
	 * @since  2.0.0
	 *
	 * @param  string $select_column_name Db column name to fetch
	 * @param mixed $where_value Value to fetch the column
	 * @param  string $where_column_name Column name for the WHERE.
	 *                                                      Default: id
	 *
	 * @return mixed|false                              Returns the value or false
	 */
	public function get_column( $select_column_name, $where_value, $where_column_name = 'id' ) {
		return $this->db_helpers->get_column( $select_column_name, $where_value, $where_column_name );
	}

	/**
	 * Fetch the specified column from the db based on where configuration
	 *
	 * @since  1.0.0
	 *
	 * @param string $where_sql WHERE SQL
	 * @param array $where_data Array of the data to populate the WHERE SQL
	 * @param string $select_column_name Column Name to pull the value from
	 *
	 * @return string|false                             Returns the value or false
	 */
	public function get_column_by_where_sql( $where_sql, array $where_data, $select_column_name ) {
		return $this->db_helpers->get_column_by_where_sql( $where_sql, $where_data, $select_column_name );
	}

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
	public function has_records( $where_sql, array $where_data ) {
		return $this->db_helpers->has_records( $where_sql, $where_data );
	}

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
	public function get_record_count( $where_sql, array $where_data ) {
		return $this->getRecordCount( $where_sql, $where_data );
	}

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
	public function getRecordCount( $where_sql, array $where_data ) {
		return $this->db_helpers->getRecordCount( $where_sql, $where_data );
	}

	/**
	 * Sum a column for all pulled records
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 * @param string $where_sql
	 * @param array $where_data
	 *
	 * @return bool|float|int
	 */
	public function sum( $column_name, $where_sql, array $where_data ) {
		return $this->db_helpers->sum( $column_name, $where_sql, $where_data );
	}

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
	 * @return int|false                    Returns the number of rows affected on success;
	 *                                      else returns false.
	 */
	public function update_columns( array $data, array $where, array $format, array $where_format ) {
		return $this->db_helpers->update_columns( $data, $where, $format, $where_format );
	}

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
	public function delete_record( $primary_key_value, $column_name = 'id' ) {
		return $this->db_helpers->delete_record( $primary_key_value, $column_name );
	}

	/******************************
	 * Callbacks
	 *****************************/

	/**
	 * AJAX Delete record callback
	 *
	 * @since 1.0.0
	 *
	 * return null
	 */
	public function ajax_delete_record() {
		$this->security_check();

		$record_id = isset( $_POST['record_id'] ) ? intval( $_POST['record_id'] ) : 0;
		if ( $record_id < 1 ) {
			$this->error_message = $this->config->message_invalid_record_id;

		} else {
			$this->db_helpers->delete_record( $record_id );
		}

		$this->ajax_response_handler();
	}
}
