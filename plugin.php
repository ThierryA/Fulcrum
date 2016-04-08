<?php
/**
 * Fulcrum Plugin
 *
 * @package         Fulcrum
 * @author          hellofromTonya
 * @license         GPL-2.0+
 * @link            https://knowthecode.io
 *
 * @wordpress-plugin
 * Plugin Name:     Fulcrum Plugin
 * Plugin URI:      https://knowthecode.io
 * Description:     Fulcrum - The customization central repository to extend and custom WordPress. This plugin provides the centralized infrastructure for the custom plugins and theme.
 * Version:         1.0.4
 * Author:          hellofromTonya
 * Author URI:      https://knowthecode.io
 * Text Domain:     fulcrum
 * Requires WP:     3.5
 * Requires PHP:    5.4
 */

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

namespace Fulcrum;

use Fulcrum\Config\Config;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Cheatin&#8217; uh?' );
}

require_once( __DIR__ . '/assets/vendor/autoload.php' );

fulcrum_declare_plugin_constants( 'FULCRUM', __FILE__ );

if ( version_compare( $GLOBALS['wp_version'], Fulcrum::MIN_WP_VERSION, '>' ) ) {
	launch();
}

/**
 * Launch the plugin
 *
 * @since 1.0.0
 *
 * @return void
 */
function launch() {

	$config = __DIR__ . '/config/plugin.php';

	new Fulcrum(
		new Config( $config )
	);
}
