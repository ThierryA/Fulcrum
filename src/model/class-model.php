<?php

/**
 * Database Table Model
 *
 * @package     Fulcrum\Model
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Model;

use Fulcrum\Config\Config_Contract;

class Model extends Model_Cleaner implements Model_Contract {

	/**
	 * Record Data
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Date Created
	 *
	 * @var string
	 */
	protected $cache_key = '';

	/**
	 * Model is populated flag
	 *
	 * @var bool
	 */
	protected $is_populated = false;

	/**
	 * Data maybe has an array value within it
	 * This flag is used for insert/update, as data needs to be serialized.
	 *
	 * @var bool
	 */
	protected $data_maybe_has_array = false;

	/********************************
	 * Instantiate & Initialize Model
	 ********************************/

	/**
	 * Instantiate the model
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Configuration parameters
	 * @param null|mixed $value Record StdObj or the value for the unique column name
	 * @param string $unique_column_name Name of the unique column
	 *
	 * @return self
	 */
	public function __construct( Config_Contract $config, $value = null, $unique_column_name = '' ) {
		parent::__construct( $config );

		if ( is_object( $value ) ) {
			$this->convert_object_to_model_handler( $value );


		} elseif ( ! empty( $value ) && $unique_column_name && in_array( $unique_column_name, $this->config->unique_columns ) ) {
			$this->fetch_record( $value, $unique_column_name );

		} else {
			$this->create_empty_model();
		}

		$this->post_init();
	}

	/**
	 * Convert the object to model handler
	 *
	 * @since 1.0.0
	 *
	 * @param $value
	 */
	protected function convert_object_to_model_handler( $value ) {
		$this->clean( $value );
		$this->is_populated = true;
	}

	/**
	 * Provided as an extension method for post processing
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function post_init() {
		// do nothing
	}

	/**
	 * When an ID is passed in, go fetch it and populate this model
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value
	 * @param string $unique_column_name
	 *
	 * @return self
	 */
	protected function fetch_record( $value, $unique_column_name ) {
		$this->cache_key = $this->cache_key ?: $unique_column_name . $value;

		$record        = wp_cache_get( $this->cache_key, $this->config->cache_group );
		$need_to_cache = false === $record ? true : false;

		if ( $need_to_cache ) {
			global $wpdb;

			$value_format       = $this->config->formats[ $unique_column_name ];
			$unique_column_name = strip_tags( $unique_column_name );

			$sql_query = $wpdb->prepare(
				"
					SELECT *
					FROM {$this->config->tablename}
					WHERE {$unique_column_name} = {$value_format}
				",
				$value
			);

			$record = $wpdb->get_row( $sql_query );
		}

		if ( empty( $record ) ) {
			return;
		}

		$this->clean( $record );

		if ( $need_to_cache ) {
			wp_cache_set( $this->cache_key, $this->data, $this->config->cache_group );
		}

		$this->is_populated = true;
	}

	/********************************
	 * Public Methods
	 ********************************/

	/**
	 * Populate the model with the passed in raw record
	 *
	 * @since 1.0.0
	 *
	 * @uses self::clean()
	 *
	 * @param stdObj|array $record Raw record
	 *
	 * @return null
	 */
	public function populate( $record ) {
		$this->clean( $record );
	}

	/**
	 * Save the model to the database
	 *
	 * @since 1.0.0
	 *
	 * @return bool|integer     Returns primary key on success; else false.
	 */
	public function save() {
		return $this->data[ $this->config->primary_key ] > 0
			? $this->update_record()
			: $this->save_record();
	}

	/**
	 * Deletes the record from the database
	 *
	 * @since 1.0.0
	 *
	 * @return bool         Returns true on success; else false.
	 */
	public function delete() {
		global $wpdb;

		if ( $this->data[ $this->config->primary_key ] < 1 ) {
			return false;
		}

		$result = $wpdb->delete(
			$this->config->tablename,
			array( $this->config->primary_key => $this->data[ $this->config->primary_key ] )
		);

		return false === $result ? false : true;
	}

	/********************************
	 * Getters
	 ********************************/

	/**
	 * Get a column's value
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function __get( $column_name ) {
		return $this->getData( $column_name );
	}

	/**
	 * Get a property's value
	 *
	 * @since 1.0.0
	 *
	 * @param string $property
	 *
	 * @return mixed
	 */
	public function get( $property ) {
		if ( property_exists( $this, $property ) ) {
			return $this->$property;
		}
	}

	/**
	 * Get a column's value
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function getData( $column_name ) {
		if ( array_key_exists( $column_name, $this->data ) ) {
			return $this->data[ $column_name ];
		}
	}

	/**
	 * Returns the column's configuration property
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getColumnsConfig() {
		return $this->config->columns_config;
	}

	/**
	 * Returns the tablename
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getTablename() {
		return $this->config->tablename;
	}

	/**
	 * Returns the model's data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getModel() {
		return $this->data;
	}

	/********************************
	 * Setters
	 ********************************/

	/**
	 * Set the column value in the model
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 * @param mixed $value
	 *
	 * @return null
	 */
	public function set( $column_name, $value ) {
		if ( ! array_key_exists( $column_name, $this->data ) ||
		     in_array( $column_name, $this->config->guarded )
		) {
			return;
		}

		$this->clean_and_store_data( $column_name, $value );
	}

	/**
	 * Set a column's value - slower magic setter
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 * @param mixed $value
	 *
	 * @return null
	 */
	public function __set( $column_name, $value ) {
		return $this->set( $column_name, $value );
	}

	/********************************
	 * Protected Methods
	 ********************************/

	/**
	 * Build the save SQL
	 *
	 * @since 1.0.0
	 *
	 * @return int|bool
	 */
	protected function save_record() {
		global $wpdb;

		if ( $this->config->use_datestamps ) {
			$this->data['date_created'] = current_time( 'mysql' );
		}

		$data = $this->get_primary_key_and_data_values( $this->get_data_for_save() );
		$this->get_sql_formats( $data['data'] );

		$result = $wpdb->insert(
			$this->config->tablename,
			$data['data'],
			$this->config->save_update_formats
		);

		if ( false === $result ) {
			return false;
		}

		$this->data[ $this->config->primary_key ] = $wpdb->insert_id;

		return $this->data[ $this->config->primary_key ];
	}

	/**
	 * Build the update SQL
	 *
	 * @since 1.0.0
	 *
	 * @return bool|integer
	 */
	protected function update_record() {
		global $wpdb;

		if ( $this->config->use_datestamps ) {
			$this->data['date_updated'] = current_time( 'mysql' );
		}

		$data = $this->get_primary_key_and_data_values( $this->get_data_for_save() );
		$this->get_sql_formats( $data['data'] );

		$result = $wpdb->update(
			$this->config->tablename,
			$data['data'],
			array( $this->config->primary_key => $data['primary_key'] ),
			$this->config->save_update_formats,
			array( '%d' )
		);

		return false === $result ? false : $data['primary_key'];
	}

	/**
	 * Get the SQL column formats
	 *
	 * @since 1.0.0
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	protected function get_sql_formats( array $data = array() ) {
		if ( ! empty( $this->config->save_update_formats ) ) {
			return $this->config->save_update_formats;
		}

		if ( empty( $data ) ) {
			$data = $this->get_primary_key_and_data_values();
			$data = $data['data'];
		}

		foreach ( $data as $column_name => $value ) {
			$this->config->formats[ $column_name ] = $this->config->columns_config[ $column_name ]['format'];
			if ( $this->config->primary_key != $column_name ) {
				$this->config->save_update_formats[] = $this->config->columns_config[ $column_name ]['format'];
			}
		}
	}

	/**
	 * Get the primary key and data - this is needed to split out the
	 * primary key from the rest of the columns in this record.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	protected function get_primary_key_and_data_values( array $data ) {
		$primary_key = $data[ $this->config->primary_key ];
		unset( $data[ $this->config->primary_key ] );

		if ( $this->config->data_maybe_has_array ) {
			foreach ( $data as $column_name => $value ) {
				if ( is_array( $value ) ) {
					$data[ $column_name ] = empty( $value ) ? '' : maybe_serialize( $value );
				}
			}
		}

		return compact( 'primary_key', 'data' );
	}

	/**
	 * Get Data for Save.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_data_for_save() {
		if ( ! is_array( $this->config->non_save_data_points ) ) {
			return $this->data;
		}

		return $this->remove_non_save_data_points_before_saving();
	}

	/**
	 * Remove the non-save data points before saving.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function remove_non_save_data_points_before_saving() {
		$data = $this->data;

		array_walk( $this->config->non_save_data_points, function ( $data_point_key ) use ( &$data ) {
			if ( array_key_exists( $data_point_key, $data ) ) {
				unset( $data[ $data_point_key ] );
			}
		} );

		return $data;
	}

	/**
	 * Create an empty model - populate data with defaults
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function create_empty_model() {
		array_walk( $this->config->columns_config, function ( $config, $column_name ) {
			$this->data[ $column_name ] = $config['default'];
		} );
	}
}
