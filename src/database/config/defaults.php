<?php

/**
 * Database Schema defaults.
 *
 * @package     Fulcrum\Database
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Database;

return array(
	/*****************************************
	 * Tablenames
	 *****************************************/
	'tablenames' => array(),
	/*****************************************
	 * Database Version
	 *****************************************/

	'version' => '1.0.0',
	/*****************************************
	 * Db Version Option Key
	 *****************************************/

	'option_name' => '',
	/*****************************************
	 * SQL Schema
	 *****************************************/

	'sql' => array(),
	/*****************************************
	 * Use Seed Tables when creating
	 *****************************************/

	'use_seed_tables'    => false,
	'seed_only_on_empty' => false,
	'seeder'             => array(),
);
