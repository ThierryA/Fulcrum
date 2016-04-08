<?php
/**
 * Style Asset
 *
 * @package     Fulcrum\Asset\Repo
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Asset\Repo;

use Fulcrum\Asset\Asset;

class Style extends Asset {

	/**
	 * Register the style (enqueues it)
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function register() {
		parent::register();

		if ( $this->config->is_array( 'localize' ) ) {
			$this->localize_script();
		}
	}

	/**
	 * De-register the style
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function deregister() {
		wp_deregister_style( $this->handle );
	}

	/**
	 * Enqueue the script
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function enqueue() {

		wp_enqueue_style(
			$this->handle,
			$this->config->file,
			$this->config->deps,
			$this->config->version
		);
	}

	/**
	 * Get the default structure.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_default_structure() {

		return array(
			'file'    => '',
			'deps'    => array(),
			'version' => '',
		);
	}
}
