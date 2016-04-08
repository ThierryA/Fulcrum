<?php

/**
 * Base WordPress Post Model
 *
 * @package     Fulcrum\Model
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Model;

use Fulcrum\Config\Config_Contract;

class Post_Model implements Post_Model_Contract {

	/**
	 * Runtime configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Meta & Terms
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Post ID
	 *
	 * @var int
	 */
	protected $post_id = 0;

	/**
	 * Previous Post
	 *
	 * @var array
	 */
	protected $prev_post = array();

	/**
	 * Next Post
	 *
	 * @var array
	 */
	protected $next_post = array();

	/**
	 * Meta keys
	 *
	 * @var array
	 */
	protected $meta_keys = array();

	/******************************
	 * Instantiate & Initializers
	 *****************************/

	/**
	 * Instantiate the Model
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Instance of config
	 * @param int $post_id (optional) Post ID for this model.
	 *
	 * @return self
	 */
	public function __construct( Config_Contract $config, $post_id = 0 ) {
		$this->config = $config;

		global $post;
		$this->post_id = $post_id > 0 ? $post_id : $post->ID;

		if ( is_single() ) {
			$this->init_adjacent_post();
			$this->init_adjacent_post( false );
		}

		$this->init_model();
	}

	/**
	 * Initialize the Previous & Next Adjacent Posts
	 *
	 * @since 1.0.0
	 *
	 * @param bool $prev
	 *
	 * @return null
	 */
	protected function init_adjacent_post( $prev = true ) {
		$property = $prev ? 'prev_post' : 'next_post';
		$post_id  = $this->get_adjacent_post_id( $prev );

		if ( $post_id < 1 ) {
			return;
		}
		$this->$property = array(
			'id'         => $post_id,
			'url'        => get_permalink( $post_id ),
			'title_attr' => the_title_attribute( array( 'post' => $post_id, 'echo' => false ) ),
			'thumbnail'  => get_the_post_thumbnail( $post_id, 'thumbnail' ),
			'title'      => get_the_title( $post_id ),
		);
	}

	/**
	 * Initialize
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_model() {
		foreach ( $this->config->meta_keys as $meta_key => $single ) {
			$this->init_meta( $meta_key, $single );
			$this->meta_keys[] = $meta_key;
		}

		if ( array_key_exists( 'taxonomy', $this->config ) ) {
			$this->init_terms();
		}
	}

	/**
	 * Initialize the Properties from the Post Meta
	 *
	 * @since 1.0.0
	 *
	 * @param string $meta_key
	 * @param bool $single
	 *
	 * @return null
	 */
	protected function init_meta( $meta_key, $single = false ) {
		$meta = get_post_meta( $this->post_id, $meta_key, $single );

		if ( $single ) {
			$this->data[ $meta_key ] = $meta;
		} else {
			$this->data[ $meta_key ] = isset( $meta[0] ) ? $meta[0] : array();
		}
	}

	/**
	 * Initialize the terms & return as a comma-separated list
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_terms() {
		foreach ( $this->config->taxonomy as $property => $taxonomy ) {
			$terms = get_the_terms( $this->post_id, $taxonomy );

			if ( $terms && ! is_wp_error( $terms ) ) {
				$arr = array();

				foreach ( $terms as $term ) {
					$arr[] = $term->name;
				}

				$this->data[ $property ] = join( ', ', $arr );

			} else {
				$this->data[ $property ] = '';
			}
		}
	}

	/***********************
	 * Public
	 **********************/

	/**
	 * Checks if there is an adjacent post
	 *
	 * @since 1.0.0
	 *
	 * @param bool $prev
	 *
	 * @return bool
	 */
	public function has_adjacent_post( $prev = true ) {
		$property = $prev ? 'prev_post' : 'next_post';

		return ! ( empty( $this->$property ) );
	}

	/**
	 *  Get the property's value
	 *
	 * @since 1.0.0
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get_property( $key );
	}

	/**
	 * Get the property's value
	 *
	 * @since 1.0.0
	 *
	 * @param string $key
	 * @param string $sub_key
	 * @param mixed $default_value
	 *
	 * @return mixed
	 */
	public function get( $key, $sub_key = '', $default_value = null ) {
		return $this->get_property( 'data', $key, $sub_key, $default_value );
	}

	/**
	 * Get Property
	 *
	 * @since 1.0.0
	 *
	 * @param $property
	 *
	 * @return null|mixed
	 */
	public function getProperty( $property ) {
		if ( property_exists( $this, $property ) ) {
			return $this->$property;
		}
	}

	/**
	 * Post ID Getter
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Get the meta value
	 *
	 * @since 1.0.0
	 *
	 * @param string $sub_key
	 * @param string $meta_key Defaults to the first meta key in $this->config['meta_keys']
	 * @param mixed $default_value
	 *
	 * @return mixed
	 */
	public function get_meta( $sub_key, $meta_key = '', $default_value = null ) {
		$meta_key = $meta_key ?: $this->meta_keys[0];

		return $this->get_property( 'data', $meta_key, $sub_key, $default_value );
	}

	/**
	 * Get the configuration parameter
	 *
	 * @since 1.0.0
	 *
	 * @param string $key
	 * @param null $default_value
	 *
	 * @return mixed
	 */
	public function get_config( $key = '', $default_value = null ) {
		return $key ? $this->get_property( 'config', $key, '', $default_value ) : $this->config;
	}

	/**
	 * Get Prev Post
	 *
	 * @since 1.0.0
	 *
	 * @param string $key
	 * @param mixed $default_value
	 *
	 * @return mixed
	 */
	public function get_prev_post( $key = '', $default_value = '' ) {
		return $this->get_property( 'prev_post', $key, '', $default_value );
	}

	/**
	 * Get Next Post
	 *
	 * @since 1.0.0
	 *
	 * @param string $key
	 * @param mixed $default_value
	 *
	 * @return mixed
	 */
	public function get_next_post( $key = '', $default_value = '' ) {
		return $this->get_property( 'next_post', $key, '', $default_value );
	}


	/**
	 * Get the Subtitle
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_subtitle() {
		return $this->get_property( 'data', $this->meta_keys[0], '_subtitle' );
	}

	/****************************
	 * Helpers
	 ***************************/

	/**
	 * Get Property
	 *
	 * @since 1.0.0
	 *
	 * @param string $property
	 * @param string $key
	 * @param string $sub_key
	 * @param mixed $default_value
	 *
	 * @return mixed
	 */
	protected function get_property( $property, $key = '', $sub_key = '', $default_value = '' ) {

		if ( ! property_exists( $this, $property ) ) {
			return $default_value;
		}

		if ( $key && is_array( $this->$property ) ) {

			$value = array_key_exists( $key, $this->$property )
				? $this->{$property}[ $key ]
				: $default_value;

			if ( $sub_key ) {
				return is_array( $value ) && array_key_exists( $sub_key, $value )
					? $value[ $sub_key ]
					: $default_value;
			}

			return $value;
		}

		return $this->$property;
	}

	/**
	 * Get Adjacent Post ID
	 *
	 * @since 1.0.0
	 *
	 * @param bool $prev
	 *
	 * @return int
	 */
	protected function get_adjacent_post_id( $prev = true ) {

		$post = $prev
			? get_previous_post()
			: get_next_post();

		return empty( $post ) || ! is_object( $post ) ? 0 : $post->ID;
	}
}