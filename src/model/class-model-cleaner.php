<?php

/**
 * Model Cleaner Abstract Class
 *
 * @package     Fulcrum\Model
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Model;

use Fulcrum\Config\Config_Contract;

abstract class Model_Cleaner {

	/**
	 * Runtime configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Record Data
	 *
	 * @var array
	 */
	protected $data = array();

	/******************************
	 * Instantiate & Initializers
	 *****************************/

	/**
	 * Instantiate DB Helpers object
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Instance of Config
	 *
	 * @return self
	 */
	public function __construct( Config_Contract $config = null ) {
		$this->config = $config;
	}

	/********************************
	 * Cleaner Methods
	 ********************************/

	/**
	 * Clean the record and populate the model
	 *
	 * @since 1.0.0
	 *
	 * @param stdClass $record Raw record
	 * @param bool $store_in_property When true, assigns to the object; else in $this->data.
	 *
	 * @return null
	 */
	public function clean( $record, $store_in_property = false ) {
		foreach ( $record as $column_name => $value ) {
			$this->clean_and_store_data( $column_name, $value, $store_in_property );
		}
	}

	/**
	 * Clean the value per the columns configuration
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 * @param mixed $value
	 * @param bool $store_in_property When true, assigns to the object; else in $this->data.
	 * @param bool $return_value When true, returns the value instead of storing it.
	 *
	 * @return null|bool
	 */
	protected function clean_and_store_data( $column_name, $value, $store_in_property = false, $return_value = false ) {
		if ( ! array_key_exists( $column_name, $this->config->columns_config ) ) {
			return false;
		}

		if ( is_null( $value ) && $this->config->store_after_cleaning ) {
			$this->store_value_after_cleaning( $column_name, $value, $store_in_property );
		}

		$value =  $this->config->columns_config[ $column_name ]['is_array']
			? $this->sanitize_each_element_in_array( $column_name, $value )
			: $this->clean_individual_value( $column_name, $value );

		if ( ! $return_value && $this->config->store_after_cleaning ) {
			$this->store_value_after_cleaning( $column_name, $value, $store_in_property );
		} else {
			return $value;
		}
	}

	/**
	 * Clean an individual value
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	protected function clean_individual_value( $column_name, $value ) {
		if ( is_array( $value ) ) {
			return stripslashes_deep( $value );
		}

		$filter = $this->config->columns_config[ $column_name ]['filter'];
		if ( is_array( $filter ) ) {
			foreach ( $filter as $f ) {
				$value = $this->filter_value( $value, $f );
			}
		} else {
			$value = $this->filter_value( $value, $filter );
		}

		return is_numeric( $value ) ? $value : stripslashes( $value );
	}

	/**
	 * Filter the value
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value
	 * @param string $filter
	 *
	 * @return mixed
	 */
	protected function filter_value( $value, $filter ) {
		switch ( $filter ) {
			case 'bool':
				return true === $value || 1 == $value ? true : false;
			case 'int' :
				return (int) $value;
			default :
				return $filter( $value );
		}
	}

	/**
	 * Store value after cleaning
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 * @param mixed $value
	 * @param bool $store_in_property When true, assigns to the object; else in $this->data.
	 *
	 * @return null
	 */
	protected function store_value_after_cleaning( $column_name, $value, $store_in_property ) {
		if ( $store_in_property ) {
			$this->$column_name = $value;
		} else {
			$this->data[ $column_name ] = $value;
		}
	}

	/**
	 * Sanitize each element in the array
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 * @param mixed $value
	 *
	 * @return array
	 */
	protected function sanitize_each_element_in_array( $column_name, $value ) {
		if ( ! is_array( $value ) ) {
			$value = (array) maybe_unserialize( $value );
		}

		foreach ( $value as $index => $val ) {
			$value[ $index ] = $this->clean_individual_value( $column_name, $val );
		}

		return $value;
	}
}