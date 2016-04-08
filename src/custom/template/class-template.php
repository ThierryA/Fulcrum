<?php

/**
 * Template Manager
 *
 * Handles loading templates from a plugin
 *
 * @package     Fulcrum\Custom\Template
 * @since       1.0.3
 * @author      hellofromTonya and Tom McFarlin
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Template;

use Fulcrum\Config\Config_Contract;

class Template {

	/**
	 * Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/***************************
	 * Instantiate & Initialize
	 **************************/

	/**
	 * Instantiate the Template Manager
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Runtime configuration parameters.
	 *
	 * @return self
	 */
	public function __construct( Config_Contract $config ) {
		$this->config = $config;

		$this->init_events();
	}

	/**
	 * Initialize the hooks
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_events() {
		add_filter( 'template_include', array( $this, 'include_template' ) );
		add_filter( 'page_attributes_dropdown_pages_args', array( $this, 'register_templates' ), 10, 2 );
		add_filter( 'wp_insert_post_data', array( $this, 'register_templates' ), 10, 2 );

		add_filter( 'archive_template', array( $this, 'add_archive_template' ) );
		add_filter( 'taxonomy_template', array( $this, 'add_taxonomy_template' ) );
	}

	/**
	 * Add the post type archive template into WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param string $archive_template
	 *
	 * @return string
	 */
	public function add_archive_template( $archive_template ) {
		if ( ! is_post_type_archive( $this->config->post_type ) ) {
			return $archive_template;
		}
		$template = sprintf( 'archive-%s.php', $this->config->post_type );

		$theme_file = locate_template( array( $template ) );

		if ( $theme_file && is_readable( $theme_file ) ) {
			return $theme_file;
		}

		if ( is_readable( $this->config->template_folder_path . $template ) ) {
			return $this->config->template_folder_path . $template;
		}

		return $archive_template;
	}

	/**
	 * Add the taxonomy template into WordPress.
	 *
	 * @since 1.0.4
	 *
	 * @param string $taxonomy_template
	 *
	 * @return string
	 */
	public function add_taxonomy_template( $taxonomy_template ) {
		if ( ! $this->config->use_tax || ! $this->config->has( 'tax' ) || ! is_tax( $this->config->tax ) ) {
			return $taxonomy_template;
		}

		$template = sprintf( 'taxonomy-%s.php', $this->config->tax );

		$theme_file = locate_template( array( $template ) );

		if ( $theme_file && is_readable( $theme_file ) ) {
			return $theme_file;
		}

		if ( is_readable( $this->config->template_folder_path . $template ) ) {
			return $this->config->template_folder_path . $template;
		}

		return $taxonomy_template;
	}

	/**
	 * Register plugin's templates into the Dropdown list for Page Templates
	 *
	 * Hooked into 'page_attributes_dropdown_pages_args'
	 * Filter the arguments used to generate a Pages drop-down element.
	 *
	 * Refer to WP_Theme->get_page_templates() for more details and handling.
	 *
	 * @since 1.0.1
	 *
	 * @param array $dropdown_args Array of arguments used to generate the pages drop-down.
	 * @param WP_Post $post The current WP_Post object.
	 *
	 * @return array
	 */
	public function register_templates( $dropdown_args, $post ) {

		if ( empty( $this->config->templates ) ) {
			return $dropdown_args;
		}

		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list. If it doesn't exist, or it's empty prepare an array
		$page_templates = wp_cache_get( $cache_key, 'themes' );
		$page_templates = empty( $page_templates ) ? array() : $page_templates;

		// Since we've updated the cache, we need to delete the old cache
		wp_cache_delete( $cache_key, 'themes' );

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$page_templates = array_merge( $page_templates, $this->config->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $page_templates, 'themes', 1800 );

		return $dropdown_args;
	}

	/**
	 * Pass back the template file to the front-end loader
	 *
	 * @since 1.0.0
	 *
	 * @param string $template
	 *
	 * @return string
	 */
	public function include_template( $template ) {
		if ( fulcrum_ends_with( $template, 'index.php' ) ) {
			return $template;
		}

		global $post;

		if ( is_null( $post ) ) {
			return $template;
		}

		if ( $this->config->use_page_templates && is_page() ) {
			return $this->get_page_template( $template, $post->ID );
		}

		if ( ! $this->is_post_type_configured() ) {
			return $template;
		}

		if ( $this->is_page_type_configured() ) {
			return $this->get_template( $template );
		}

		return $template;
	}


	/**
	 * Get the {context}-template file
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_fullpath
	 *
	 * @return string
	 */
	protected function get_template( $template_fullpath ) {
		$template = $this->build_template_file_path_and_name( $this->extract_template_slug_from_fullpath( $template_fullpath ) );

		$theme_file = locate_template( array( $template ) );

		if ( $theme_file && is_readable( $theme_file ) ) {
			return $theme_file;
		}

		if ( is_readable( $this->config->template_folder_path . $template ) ) {
			return $this->config->template_folder_path . $template;
		}

		return $template_fullpath;
	}

	/**
	 * If the configured template file is readable, return it; else return the
	 * original template file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template
	 * @param integer $post_id
	 *
	 * @return string
	 */
	function get_page_template( $template, $post_id ) {
		$file = $this->config->template_folder_path . get_post_meta( $post_id, '_wp_page_template', true );

		return is_readable( $file ) ? $file : $template;
	}

	/**
	 * Checks if the post type is configured
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_post_type_configured() {
		global $post;

		$post_type = get_post_type( $post->ID );

		return is_array( $this->config->post_type )
			? in_array( $post_type, $this->config->post_type )
			: $post_type == $this->config->post_type;
	}

	/**
	 * Checks if the page type is configured
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_page_type_configured() {
		return ( $this->config['use_single'] && is_single() ) ||
		       ( $this->config['use_archive'] && is_archive() ) ||
		       ( $this->config['use_category'] && is_category() ) ||
		       ( $this->config['use_tax'] && is_tax() ) ||
		       ( $this->config['use_tag'] && is_tag() );
	}

	/**
	 * Build the templates full path and filename
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_slug
	 *
	 * @return string
	 */
	protected function build_template_file_path_and_name( $template_slug ) {
		$post_type = $this->config['post_type'];

		if ( is_array( $post_type ) ) {
			global $post;
			$post_type = get_post_type( $post->ID );
		}

		return $template_slug . '-' . $post_type . '.php';
	}

	/**
	 * Extract template's slug from the fullpath
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_fullpath
	 *
	 * @return string
	 */
	protected function extract_template_slug_from_fullpath( $template_fullpath ) {
		$parts    = explode( '/', $template_fullpath );
		$template = array_pop( $parts );

		return rtrim( $template, '.php' );
	}
}