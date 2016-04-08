<?php

/**
 * Custom post type permalinks defaults.
 *
 * @package     Fulcrum\Custom\Post_Type\Permalink\Config
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Post_Type\Permalink\Config;

return array(
	'custom_post_type'      => '',
	'taxonomy'              => '',
	'rewrite_with_taxonomy' => array(
		'enable'        => true,
		'pattern'       => '',
		'taxonomy_name' => '',
	),
	'debugger'              => false,
);
