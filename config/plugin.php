<?php
/**
 * Fulcrum runtime configuration parameters.
 *
 * @package     Fulcrum
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knwothecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum;

use Whoops\Run;
use Fulcrum\Support\Exceptions\Whoops_Displayer;

return array(

	/*********************************************************
	 * Initial Core Parameters, which are loaded into the
	 * Container before anything else occurs.
	 *
	 * Format:
	 *    $unique_id => $value
	 ********************************************************/
	'initial_parameters' => array(
		'is_dev_env'         => WP_DEBUG,
		'fulcrum.plugin_dir' => FULCRUM_PLUGIN_DIR,
		'fulcrum.plugin_url' => FULCRUM_PLUGIN_URL,
		'fulcrum.config_dir' => FULCRUM_PLUGIN_DIR . 'config/',
	),

	/*********************************************************
	 * Handlers - Handlers need to be loaded first as they
	 * handle registering the service providers.
	 ********************************************************/
	'handlers'           => array(
		'provider.handler' => array(
			'autoload' => true,
			'concrete' => function ( $container ) {
				return new Foundation\Service_Provider\Handler( $container['fulcrum'] );
			}
		),
	),

	/*********************************************************
	 * Service Providers - these providers are the object factories for the
	 * add-on plugins and theme to use.
	 ********************************************************/
	'service_providers'  => array(
		'provider.asset'               => 'Fulcrum\Asset\Asset_Provider',
		'provider.post_type'           => 'Fulcrum\Custom\Post_Type\Post_Type_Provider',
		'provider.post_type_permalink' => 'Fulcrum\Custom\Post_Type\Permalink\Permalink_Provider',
		'provider.shortcode'           => 'Fulcrum\Custom\Shortcode\Shortcode_Provider',
		'provider.taxonomy'            => 'Fulcrum\Custom\Taxonomy\Taxonomy_Provider',
		'provider.template'            => 'Fulcrum\Custom\Template\Template_Provider',
		'provider.widget'              => 'Fulcrum\Custom\Widget\Widget_Provider',
	),

	'admin_service_providers' => array(
		'provider.metabox' => 'Fulcrum\Custom\Meta\Metabox_Provider',
		'provider.schema'  => 'Fulcrum\Database\Schema_Provider',
	),

	/*********************************************************
	 * Dev Environment
	 ********************************************************/

	'dev_env' => array(

		/*********************************************************
		 * Handlers - Handlers need to be loaded first as they
		 * handle registering the service providers.
		 ********************************************************/
		'handlers' => array(
			'whoops_displayer' => array(
				'autoload' => true,
				'concrete' => function ( $container ) {
					return new Whoops_Displayer( new Run() );
				}
			),
		),
	),
);
