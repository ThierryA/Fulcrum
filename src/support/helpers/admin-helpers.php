<?php namespace Fulcrum\Support\Helpers;

/**
 * Admin Helpers
 *
 * @package     Fulcrum\Support\Helpers
 * @since       1.1.1
 * @author      hellofromTonya
 * @link        https://wpdevelopersclub.com/
 * @license     GNU General Public License 2.0+
 */

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\temporary_admin_chrome_fix' );
/**
 * Temporary admin fix for Chrome per Trac ticket
 *
 * @since       1.1.1
 *
 * @link https://core.trac.wordpress.org/ticket/33199
 * @return null
 */
function temporary_admin_chrome_fix() {
	if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Chrome' ) ) {
		wp_add_inline_style( 'wp-admin', '#adminmenu { transform: translateZ(0); }' );
	}
}