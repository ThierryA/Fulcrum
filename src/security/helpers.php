<?php
/**
 * Security Helps
 *
 * @package     Fulcrum
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knownthecode.io
 * @license     GNU General Public License 2.0+
 */

if ( ! function_exists( 'fulcrum_load_login_form_styling' ) ) {
	/**
	 * Load in the custom login form styling.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $config Runtime configuration parameters
	 *
	 * @return void
	 */
	function fulcrum_load_login_form_styling( $config ) {
		if ( $GLOBALS['pagenow'] != 'wp-login.php' ) {
			return;
		}

		$default_config = __DIR__ . '/config/defaults.php';

		new \Fulcrum\Security\Login_Form(
			new \Fulcrum\Config\Config( $config, $default_config )
		);
	}

}

add_action( 'get_header', 'fulcrum_wp_head_cleanup' );
/**
 * Cleanup `wp_head` to remove the unnecessary stuff.
 *
 * @since 1.0.0
 *
 * @return void
 */
function fulcrum_wp_head_cleanup() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

	if ( is_single() || is_page() ) {
		remove_action( 'wp_head', 'feed_links_extra', 3 );
	}
}
