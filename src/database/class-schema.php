<?php

/**
 * Schema Abstract
 *
 * @package     Fulcrum\Database
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Database;

use Fulcrum\Config\Config_Contract;

class Schema {

	/**
	 * Runtime configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Db Version Option Key
	 *
	 * @var string
	 */
	protected $option_name = '';

	/**
	 * Charset Collate
	 *
	 * @var string
	 */
	protected $charset_collate;

	/**
	 * Use Seed Tables when creating
	 *
	 * @var bool
	 */
	protected $use_seed_tables = false;

	/**
	 * Path to the Seed Data
	 *
	 * @var string
	 */
	protected $seed_data_path;

	/*****************************
	 * Instantiate & Initializers
	 ****************************/

	/**
	 * Handles the methods upon instantiation
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Instance of Config
	 * @return  self
	 */
	public function __construct( Config_Contract $config ) {
		$this->config = $config;
		$this->init_properties();

		if ( $this->has_version_changed() ) {
			$this->update_schema();
		}
	}

	/**
	 * Initialize the properties
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_properties() {
		global $wpdb;

		$this->charset_collate = $wpdb->get_charset_collate();
	}

	/**
	 * Run the schema
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function update_schema() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$this->add_schema();

		if ( $this->config->use_seed_tables ) {
			$this->seed_tables();
		}

		update_option( $this->config->option_name, $this->config->version );
	}

	/*****************************
	 * Workers
	 ****************************/

	/**
	 * Add Schema
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function add_schema() {
		$this->append_charset_collate_to_each_sql_item();

		dbDelta( $this->config->sql );
	}

	/*****************************
	 * Seeder
	 ****************************/

	/**
	 * Seed Tables
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function seed_tables() {
		array_walk( $this->config->seeder, array( $this, 'do_seed_table' ) );
	}

	/**
	 * Time to seed the table
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 * @param string $tablename
	 * @return null
	 */
	protected function do_seed_table( array $config, $tablename ) {
		if ( $config['seed_only_on_empty'] && ! $this->is_db_table_empty( $tablename ) ) {
			return;
		}

		$seed_data = $this->load_seed_data( $config['seed_file'] );
		if ( empty( $seed_data ) ) {
			return;
		}

		array_walk( $seed_data, function( $db_row ) use ( $tablename ) {
			global $wpdb;

			$db_row['date_created'] = current_time('mysql');
			$db_row['date_updated'] = current_time('mysql');

			$wpdb->insert( $tablename, $db_row );
		} );
	}

	/**
	 * Checks if the database table is empty
	 *
	 * @since 1.0.0
	 *
	 * @param string $tablename
	 *
	 * @return bool
	 */
	protected function is_db_table_empty( $tablename ) {
		global $wpdb;

		return is_null( $wpdb->get_row( "SELECT * FROM {$tablename} LIMIT 1" ) );
	}

	/**
	 * Loads and returns the seed data
	 *
	 * @since 1.0.0
	 *
	 * @param string $seed_file
	 *
	 * @return array|null
	 */
	protected function load_seed_data( $seed_file ) {
		if ( is_readable( $seed_file ) ) {
			return include( $seed_file );
		}
	}

	/*****************************
	 * Helpers
	 ****************************/

	/**
	 * Append the Charset Collate string to each SQL item.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function append_charset_collate_to_each_sql_item() {
		array_walk( $this->config->sql, function( &$sql ) {
			$sql .= $this->charset_collate . ';';
		});
	}

	/**
	 * Check the db version - it does a hard check as well
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function has_version_changed() {
		$version             = get_option( $this->config->option_name );
		$has_version_changed = $version != $this->config->version;

		if ( $has_version_changed ) {
			$has_version_changed = $this->has_version_changed_hard_check();
		}

		return $has_version_changed;
	}

	/**
	 * Making sure version has really changed.
	 * Gets around aggressive caching issue on some sites that cause setup to run multiple times.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function has_version_changed_hard_check() {
		$version = $this->get_wp_option_from_db();
		return $version != $this->config->version;
	}

	/**
	 * Get the Option form the database
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	protected function get_wp_option_from_db() {
		global $wpdb;

		$sql_query = $wpdb->prepare(
			"
				SELECT option_value
				FROM {$wpdb->prefix}options
				WHERE option_name=%s
			", $this->option_name
		);

		return $wpdb->get_var( $sql_query );
	}
}
