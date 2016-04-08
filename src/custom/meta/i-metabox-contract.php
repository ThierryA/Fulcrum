<?php

/**
 * Custom Field Contract
 *
 * @package     Fulcrum\Custom\Meta
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Meta;

interface Metabox_Contract {

	/**
	 * Register the metabox for each screen.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function add_inpost_metaboxes();

	/**
	 * Renders the metabox HTML
	 *
	 * @since 1.0.0
	 *
	 * @param $post
	 * @param array $args
	 * @return null
	 */
	public function render_metabox( $post, $args );

	/**
	 * Save the meta when we save the page.
	 *
	 * @since 1.0.0
	 *
	 * @param integer  $post_id Post ID.
	 * @param stdClass $post    Post object.
	 *
	 * @return mixed Returns post id if permissions incorrect, null if doing autosave,
	 *               ajax or future post, false if update or delete failed, and true
	 *               on success.
	 */
	function save_meta( $post_id, $post );
}
