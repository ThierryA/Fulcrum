<?php

/**
 * Db Helpers Base
 *
 * @package     Fulcrum\Database
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Database;

use Fulcrum\Config\Config_Contract;
use Fulcrum\Foundation\Pagination\Pagination_Contract;
use Fulcrum\Model\Models_Cleaner;

abstract class Db_Helpers extends Models_Cleaner implements Db_Helpers_Contract {

	/**
	 * DB Tablename
	 *
	 * @var string
	 */
	protected $tablename;

	/**
	 * Instance of Pagination
	 *
	 * @var PaginationContract
	 */
	protected $pagination;

	/**
	 * SQL Tablename AS name
	 *
	 * @var string
	 */
	protected $sql_as_name = 'a';

	/**
	 * Columns configuration
	 *
	 * @var array
	 */
	protected $columns_config = array();

	/**
	 * Filterby columns configuration
	 *
	 * @var array
	 */
	protected $filterby = array();

	/******************************
	 * Instantiate & Initializers
	 *****************************/

	/**
	 * Instantiate DB Helpers object
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Instance of Config
	 * @param Pagination_Contract $pagination Instance of Pagination
	 *
	 * @return self
	 */
	public function __construct( Config_Contract $config, Pagination_Contract $pagination ) {
		parent::__construct( $config );

		$this->pagination = $pagination;
		$this->tablename  = $this->config->tablename;
	}


	/******************************
	 * Public Methods
	 *****************************/

	/**
	 * Fetch the row (record) from the database and return the Model.
	 *
	 * @since  1.0.0
	 *
	 * @param string $where_sql WHERE SQL
	 * @param array $where_data Array of the data to populate the WHERE SQL unique value, e.g. id, slug
	 * @param bool $return_model_when_non_found When true, returns an empty model when no record is found
	 *
	 * @return mixed|false Returns a model or false
	 */
	public function get_record_by_where_sql( $where_sql, array $where_data, $return_model_when_non_found = true ) {
		global $wpdb;

		$model_class = $this->config->model_classname;

		$sql_query = $wpdb->prepare(
			"
				SELECT *
				FROM {$this->tablename}
				WHERE {$where_sql}
			", $where_data
		);

		$results = $wpdb->get_results( $sql_query );

		if ( empty( $results ) ) {

			return $return_model_when_non_found ? new $model_class( $this->config, '' ) : false;
		}

		if ( is_array( $results ) ) {
			$record = array_shift( $results );
		}

		return new $model_class( $this->config, $record );
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
	 * @return string|false Returns the value or false
	 */
	public function get_column_by_where_sql( $where_sql, array $where_data, $select_column_name ) {
		global $wpdb;

		if ( ! array_key_exists( $select_column_name, $this->config->columns_config ) ) {
			return false;
		}

		$sql_query = $wpdb->prepare(
			"
				SELECT {$select_column_name}
				FROM {$this->tablename}
				WHERE {$where_sql}
			", $where_data
		);

		return $this->get_column_select( $sql_query, $select_column_name );
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
		global $wpdb;

		if ( ! array_key_exists( $where_column_name, $this->config->columns_config ) ) {
			return false;
		}

		$where_column_name = esc_attr( $where_column_name );
		$sql_query         = $wpdb->prepare(
			"
				SELECT {$select_column_name}
				FROM {$this->tablename}
				WHERE {$where_column_name} = {$this->config->formats[ $where_column_name ]}
			", $where_value
		);

		return $this->get_column_select( $sql_query, $select_column_name );
	}

	/**
	 * Get Column from SQL Query
	 *
	 * @since 1.0.0
	 *
	 * @param string $sql_query
	 * @param string $select_column_name
	 *
	 * @return mixed|false                              Returns the value or false
	 */
	protected function get_column_select( $sql_query, $select_column_name ) {
		global $wpdb;

		$result = $wpdb->get_col( $sql_query );

		if ( empty( $result ) ) {
			return false;
		}

		$column_values = array();

		foreach ( $result as $column_value ) {
			$column_values[] = $this->clean_and_store_data( $select_column_name, $column_value, false, true );
		}

		return count( $column_values ) < 2 ? array_shift( $column_values ) : $column_values;
	}

	/**
	 * Get all the records based on the configuration criteria
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_config
	 *
	 * @return array|bool
	 */
	public function get_all_joined( array $query_config ) {
		global $wpdb;

		$this->init_query_config( $query_config );

		$sql = $this->build_the_sql_for_get_all_joined( $query_config );

		list( $pagination_sql, $paged ) = $this->build_limit_sql( $query_config, $sql );

		$sql_query = implode( ' ', $sql );

		$results = $wpdb->get_results( $sql_query );

		if ( array_key_exists( 'debug', $query_config ) && $query_config['debug'] ) {
			d( $query_config );
			d( $this->config );
			d( $sql_query );
			d( $sql );
		}

		if ( empty( $results ) ) {
			return false;
		}

		if ( $results && $query_config['convert_to_models'] ) {
			$results = $this->convert_to_models( $results, $query_config['model_index_property'] );
		}

		if ( $pagination_sql ) {
			return array(
				'page'       => $query_config['page'],
				'per_page'   => $query_config['per_page'],
				'pagination' => $this->pagination->render( $pagination_sql, $paged['page'], $paged['per_page'] ),
				'data'       => $results,
			);
		}

		return $query_config['array_shift'] && is_array( $results ) ? array_shift( $results ) : $results;
	}

	/**
	 * Build the SQL array for get_all_joined
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_config
	 *
	 * @return array
	 */
	protected function build_the_sql_for_get_all_joined( array $query_config ) {
		$sql = array();

		$sql[] = $this->get_select_sql( $query_config['usage'], $query_config['filter'] );
		$sql[] = "FROM {$this->tablename} {$this->config->sql_as_name}";

		$join_sql = $this->get_join_sql( $query_config['usage'], $query_config['includes'] );
		if ( $join_sql ) {
			$sql[] = $join_sql;
		}

		$where_sql = $this->build_where_sql( $query_config );
		if ( $where_sql ) {
			$sql[] = $where_sql;
		}

		$groupby_sql = $this->get_groupby_sql( $query_config['usage'] );
		if ( $groupby_sql ) {
			$sql[] = $groupby_sql;
		}

		$sql[] = $this->get_orderby_sql( $query_config['orderby'] );
		$sql[] = $this->get_order_sql( $query_config['order'], $query_config['usage'] );

		return $sql;
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
		global $wpdb;

		$column_name = esc_attr( $column_name );

		$sql_query = $wpdb->prepare(
			"
				DELETE FROM {$this->tablename}
				WHERE {$column_name} = {$this->config['formats'][ $column_name ]}
			", $primary_key_value
		);

		return $wpdb->query( $sql_query );
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
		return ( $this->getRecordCount( $where_sql, $where_data ) > 0 );
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
		global $wpdb;

		$sql_query         = $wpdb->prepare(
			"
				SELECT COUNT(*)
				FROM {$this->tablename}
				WHERE {$where_sql}
			", $where_data
		);
		$number_of_records = $wpdb->get_var( $sql_query );

		return is_null( $number_of_records ) ? 0 : intval( $number_of_records );
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
	 * @return bool|stdClass
	 */
	public function sum( $column_name, $where_sql, array $where_data ) {
		global $wpdb;

		$sql_query = $wpdb->prepare(
			"
				SELECT SUM({$column_name}) as totals
				FROM {$this->tablename}
				WHERE {$where_sql}
			",
			implode( ', ', $where_data )
		);
		$sum       = $wpdb->get_results( $sql_query );

		return is_null( $sum ) ? false : array_shift( $sum );
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
		global $wpdb;

		if ( ! in_array( 'date_updated', $data ) ) {
			$data['date_updated'] = current_time( 'mysql' );
			$format               = '%s';
		}

		return $wpdb->update(
			$this->tablename,
			$data,
			$where,
			$format,
			$where_format
		);
	}

	/******************************
	 * Abstract SQL Methods
	 *****************************/

	/**
	 * Get the Join SQL Query string
	 *
	 * @since 1.0.0
	 *
	 * @param string $usage
	 * @param array|false $includes
	 *
	 * @return string
	 */
	abstract protected function get_join_sql( $usage, $includes );

	/**
	 * Get the SELECT SQL Query string
	 *
	 * @since 1.0.0
	 *
	 * @param string $usage
	 *
	 * @return string
	 */
	abstract protected function get_select_sql( $usage );

	/**
	 * Get the Orderby SQL Query string
	 *
	 * @since 1.0.0
	 *
	 * @param string $orderby
	 *
	 * @return string
	 */
	abstract protected function get_orderby_sql( $orderby );


	/******************************
	 * Helper Methods
	 *****************************/

	/**
	 * Initialize the SQL Query Configuration Array
	 *
	 * @since 2.0.0
	 *
	 * @param array $query_config
	 *
	 * @return array
	 */
	protected function init_query_config( array &$query_config ) {
		$query_config = wp_parse_args( $query_config, $this->config->get_all_joined );

		if ( $query_config['check_for_convert_to_models_conditions'] ) {
			$query_config['convert_to_models'] = $query_config['convert_to_models'] && $query_config['includes'] ? false : $query_config['convert_to_models'];
		}

		$query_config['per_page'] = $query_config['per_page'] ?: 25;
		if ( $query_config['per_page'] < 2 || $query_config['return_raw'] || is_array( $query_config['in'] || count( $query_config['in'] ) == 1 ) ) {
			$query_config['pagination'] = false;
			$query_config['return_raw'] = true;
		}

		return $query_config;
	}

	/**
	 * Get the Filter SQL Query string
	 *
	 * @since 1.0.0
	 *
	 * @param array $filter
	 *
	 * @return string
	 */
	protected function get_filter_sql( array $filter ) {
		$filter_sql = $this->map_the_filterby_sql( $filter );

		$this->get_additional_filter_sql( $filter_sql, $filter );

		return $this->build_filter_sql( $filter_sql, $filter );
	}

	/**
	 * Get the Filter SQL Query string
	 *
	 * @since 1.0.0
	 *
	 * @param array $filter
	 * @param array $filter_sql
	 *
	 * @return string
	 */
	protected function map_the_filterby_sql( array $filter, array $filter_sql = array() ) {
		$filterby = $this->get_filterby();
		foreach ( $filterby as $column_name => $sql_column ) {
			if ( ! array_key_exists( $column_name, $filter ) ) {
				continue;
			}

			$format = $this->config->columns_config[ $column_name ]['format'] ?: '%s';

			$sql = $this->get_in_operator_sql( $sql_column, $filter[ $column_name ], $format );
			if ( ! empty( $sql ) ) {
				$filter_sql[ $column_name ] = $sql;
			}
		}

		return $filter_sql;
	}

	/**
	 * Separate this out to allow the extending class to change the filterby array
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_filterby() {
		return $this->config->filterby;
	}

	/**
	 * Get additional Filter SQL items
	 *
	 * @since 1.0.0
	 *
	 * @param array $filter_sql
	 * @param array $filter
	 */
	protected function get_additional_filter_sql( &$filter_sql, $filter ) {
		// do nothing
	}

	/**
	 * Get the Search SQL Query string
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $search_terms
	 * @param array $where_conditions
	 *
	 * @return null
	 */
	protected function get_search_sql( $search_terms, &$where_conditions ) {
		if ( empty( $search_terms ) || ! is_array( $search_terms ) ) {
			return '';
		}

		$search_sql = array();
		array_walk( $search_terms, function ( $term, $column_name ) use ( &$search_sql ) {
			global $wpdb;
			$search_sql[] = sprintf( "%s.%s LIKE '%%%s%%'", $this->config->sql_as_name, $column_name, esc_sql( $wpdb->esc_like( $term ) ) );
		} );

		$where_conditions['search_sql'] = implode( ' ', $search_sql );
	}

	/**
	 * Get the order SQL ( DESC or ASC )
	 *
	 * @since 1.0.0
	 *
	 * @param string $order
	 * @param string $usage
	 *
	 * @return string
	 */
	protected function get_order_sql( $order, $usage ) {
		return 'DESC' === $order ? 'DESC' : 'ASC';
	}

	/**
	 * Get GROUP BY SQL
	 *
	 * @since 1.0.0
	 *
	 * @param string $usage
	 *
	 * @return string
	 */
	protected function get_groupby_sql( $usage ) {
		return '';
	}

	/**
	 * Build the WHERE SQL
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_config Array of Query Configuration
	 * @param array $where_conditions Array of WHERE SQL; defaults to array
	 *
	 * @return string
	 */
	protected function build_where_sql( array $query_config, array $where_conditions = array() ) {
		if ( $query_config['search_terms'] ) {
			$this->get_search_sql( $query_config['search_terms'], $where_conditions );
		}

		if ( $query_config['filter'] && ( $filter_sql = $this->get_filter_sql( $query_config['filter'] ) ) ) {
			$where_conditions['filter_sql'] = $filter_sql;
		}

		$this->map_in_where_conditions( $query_config, $where_conditions );

		return empty( $where_conditions )
			? ''
			: 'WHERE ' . implode( ' AND ', $where_conditions );
	}

	/**
	 * Build the IN WHERE SQL - The specific records to which you want to limit the query
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_config Array of Query Configuration
	 * @param array $where_conditions Array of WHERE SQL; defaults to array
	 *
	 * @return null
	 */
	protected function map_in_where_conditions( array $query_config, array &$where_conditions ) {
		if ( empty( $query_config['in'] ) ) {
			return;
		}

		$in                     = implode( ',', wp_parse_id_list( $query_config['in'] ) );
		$where_conditions['in'] = "{$this->config->sql_as_name}.id IN ({$in})";
	}

	/**
	 * Get the Paged SQL
	 *
	 * @since 1.0.0
	 *
	 * @param integer $per_page
	 * @param integer $page
	 *
	 * @return array|string
	 */
	protected function get_paged_sql( $per_page, $page ) {
		global $wpdb;

		$page      = absint( $page );
		$per_page  = false === $per_page ? 0 : absint( $per_page );
		$sql_query = $per_page > 0 && $page > 0
			? $wpdb->prepare( "LIMIT %d, %d", absint( ( $page - 1 ) * $per_page ), $per_page )
			: '';

		return compact( 'page', 'per_page', 'sql_query' );
	}

	/**
	 * Create SQL IN clause for filter queries.
	 *
	 * @since 1.0.0
	 *
	 * @param string $field The database field.
	 * @param array|bool $items The values for the IN clause, or false when none are found.
	 * @param string $format
	 *
	 * @return mixed
	 */
	public function get_in_operator_sql( $field, $items, $format = '%s' ) {
		global $wpdb;

		if ( '' === $items ) {
			return '';
		}

		// split items at the comma
		if ( ! is_array( $items ) ) {
			$items = explode( ',', $items );
		}

		// array of prepared integers or quoted strings
		$items_prepared = array();

		// clean up and format each item
		foreach ( $items as $item ) {
			$item = trim( $item );

			$items_prepared[] = $wpdb->prepare( $format, $item );
		}

		// build IN operator sql syntax
		return count( $items_prepared )
			? sprintf( '%s IN ( %s )', trim( $field ), implode( ',', $items_prepared ) )
			: false;
	}

	/**
	 * Build the filter SQL
	 *
	 * @since 1.0.0
	 *
	 * @param array $filter_sql
	 * @param array $filter
	 *
	 * @return bool|string
	 */
	protected function build_filter_sql( array &$filter_sql, $filter ) {
		if ( ! empty( $filter_array['offset'] ) ) {
			$sid_sql      = absint( $filter_array['offset'] );
			$filter_sql[] = "{$this->sql_as_name}.id >= {$sid_sql}";
		}

		if ( ! empty( $filter_array['since'] ) ) {
			// Validate that this is a proper Y-m-d H:i:s date
			// Trick: parse to UNIX date then translate back
			$translated_date = date( 'Y-m-d H:i:s', strtotime( $filter_array['since'] ) );
			if ( $translated_date === $filter_array['since'] ) {
				$filter_sql[] = "{$this->sql_as_name}.date_created > '{$translated_date}'";
			}
		}

		if ( ! empty( $filter_array['updated'] ) ) {
			// Validate that this is a proper Y-m-d H:i:s date
			// Trick: parse to UNIX date then translate back
			$translated_date = date( 'Y-m-d H:i:s', strtotime( $filter_array['updated'] ) );
			if ( $translated_date === $filter_array['updated'] ) {
				$filter_sql[] = "{$this->sql_as_name}.date_updated > '{$translated_date}'";
			}
		}

		return empty( $filter_sql )
			? false
			: join( ' AND ', $filter_sql );
	}

	/**
	 * Convert the results into Models
	 *
	 * @since 1.0.0
	 *
	 * @param array $results
	 * @param string $key_property
	 *
	 * @return array
	 */
	protected function convert_to_models( array $results, $key_property = 'id' ) {
		$models      = array();
		$model_class = $this->config->model_classname;

		foreach ( $results as $result ) {
			$models[ $result->$key_property ] = new $model_class( $this->config, $result );
		}

		return $models;
	}

	/******************************
	 * Getters
	 *****************************/

	/**
	 * Get database configuration
	 *
	 * @since 1.0.0
	 *
	 * @param string|bool $key
	 *
	 * @return array
	 */
	public function getConfig( $key = false ) {
		if ( $key ) {
			return array_key_exists( $key, $this->config ) ? $this->config[ $key ] : null;
		}

		return $this->config;
	}

	/**
	 * Build the LIMIT SQL
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_config
	 * @param array $sql
	 *
	 * @return array
	 */
	protected function build_limit_sql( array $query_config, array &$sql ) {
		$pagination_sql = '';

		// If this is a paged query, fetch the paged SQL
		if ( $query_config['pagination'] && $query_config['per_page'] > 0 ) {
			$pagination_sql = implode( ' ', $sql );

			$paged = $this->get_paged_sql( $query_config['per_page'], $query_config['page'] );
			$sql[] = $paged['sql_query'];

			return array( $pagination_sql, $paged );
		}

		if ( $query_config['per_page'] > 0 ) {
			$sql[] = 'LIMIT ' . $query_config['per_page'];
		}

		return array( $pagination_sql, array() );
	}
}