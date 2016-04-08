<?php

/**
 * Plugin Helpers Functions - these functions help launch and setup plugins.
 *
 * @package     Fulcrum
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+ and MIT Licence (MIT)
 */

if ( ! function_exists( 'fulcrum_declare_plugin_constants' ) ) {
	/**
	 * Get the plugin's URL, obtained from the plugin's root file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $prefix Constant prefix
	 * @param string $plugin_root_file Plugin's root file
	 * @returns string Returns the plugin URL
	 */
	function fulcrum_declare_plugin_constants( $prefix, $plugin_root_file ) {
		if ( ! defined( $prefix . '_PLUGIN_DIR' ) ) {
			define( $prefix . '_PLUGIN_DIR', plugin_dir_path( $plugin_root_file ) );
		}

		if ( ! defined( $prefix . '_PLUGIN_URL' ) ) {
			define( $prefix . '_PLUGIN_URL', fulcrum_get_plugin_url( $plugin_root_file ) );
		}
	}
}

if ( ! function_exists( 'fulcrum_get_plugin_url' ) ) {
	/**
	 * Get the plugin's URL, obtained from the plugin's root file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_root_file Plugin's root file
	 * @returns string Returns the plugin URL
	 */
	function fulcrum_get_plugin_url( $plugin_root_file ) {
		$plugin_url = plugin_dir_url( $plugin_root_file );
		if ( is_ssl() ) {
			$plugin_url = str_replace( 'http://', 'https://', $plugin_url );
		}

		return $plugin_url;
	}
}
