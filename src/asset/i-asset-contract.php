<?php

/**
 * Assets Contract
 *
 * @package     Fulcrum\Asset
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Asset;

interface Asset_Contract {

	/**
	 * Checks if an asset has been enqueued
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_enqueued();

	/**
	 * Register each of the asset (enqueues it)
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function register();

	/**
	 * De-register each of the asset
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function deregister();
}
