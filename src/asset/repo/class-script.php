<?php
/**
 * Script Asset
 *
 * @package     Fulcrum\Asset\Repo
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Asset\Repo;

use Fulcrum\Asset\Asset;

class Script extends Asset {

	/**
	 * Register the script (enqueues it)
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
	 * De-register the script
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function deregister() {
		wp_deregister_script( $this->handle );
	}

	/**
	 * Enqueue the script
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function enqueue() {
		wp_enqueue_script(
			$this->handle,
			$this->config->file,
			$this->config->deps,
			$this->config->version,
			$this->config->in_footer
		);
	}

	/**
	 * Localize the script
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function localize_script() {
		wp_localize_script(
			$this->handle,
			$this->config->localize['js_var_name'],
			$this->config->localize['params']
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
			'file'      => '',
			'deps'      => array( 'jquery' ),
			'version'   => '',
			'in_footer' => true,
			'localize'  => array(
				'params'      => array(
					'ajaxurl' => admin_url( '/admin-ajax.php' ),
				),
				'js_var_name' => 'pluginParams',
			),
		);
	}
}
