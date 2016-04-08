<?php

/**
 * Database Table Model Contract (Interface)
 *
 * @package     Fulcrum\Model
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Model;

interface Model_Contract {

	/**
	 * Clean the record and populate the model
	 *
	 * @since 1.0.0
	 *
	 * @param stdObj $record Raw record
	 *
	 * @return null
	 */
	public function clean( $record );

	/**
	 * Populate the model with the passed in raw record
	 *
	 * @since 1.0.0
	 *
	 * @uses self::clean()
	 *
	 * @param stdObj $record Raw record
	 *
	 * @return null
	 */
	public function populate( $record );

	/**
	 * Save the model to the database
	 *
	 * @since 1.0.0
	 *
	 * @return bool Returns true on success; else false.
	 */
	public function save();

	/**
	 * Deletes the record from the database
	 *
	 * @since 1.0.0
	 *
	 * @return bool Returns true on success; else false.
	 */
	public function delete();


	/********************************
	 * Getters
	 ********************************/

	/**
	 * Get a property's value
	 *
	 * @since 1.0.0
	 *
	 * @param string $property
	 *
	 * @return mixed
	 */
	public function get( $property );

	/**
	 * Get a column's value
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function getData( $column_name );

	/**
	 * Returns the column's configuration property
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getColumnsConfig();

	/**
	 * Returns the tablename
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getTablename();


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
	public function set( $column_name, $value );
}
