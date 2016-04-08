<?php

/**
 * Base Widget (abstract) - All Widgets extend this class
 *
 * @package     Fulcrum\Custom\Widget
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Widget;

use WP_Widget;
use RuntimeException;
use Fulcrum\Fulcrum;
use Fulcrum\Fulcrum_Contract;
use Fulcrum\Config\Config_Contract;

abstract class Widget extends WP_Widget implements Widget_Contract {

	/**
	 * Instance of Fulcrum
	 *
	 * @var Fulcrum_Contract
	 */
	protected $fulcrum;

	/**
	 * Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/****************************
	 * Instantiate & Initialize
	 ****************************/

	/**
	 * Constructor. Set the default widget options and create widget.
	 *
	 * @since 1.1.1
	 *
	 * @return self
	 */
	public function __construct() {
		$this->init_properties();

		$this->init();

		parent::__construct(
			$this->config['id_base'],
			$this->config['name'],
			$this->config['widget_options'],
			$this->config['control_options']
		);
	}

	/**
	 * Initialize the Properties
	 *
	 * Sadly we must be coupled to Fulcrum directly as Widgets are instantiated in the
	 * registration process within the Widget Factory itself.  We could use the global
	 * to gain access directly to the "public" widget registry within the factory;
	 * however,then we are coupled to that implementation, meaning if down the road the
	 * process changes within WordPress Core, our widget will break.
	 *
	 * A compromise then is to fetch hub and then instantiate the config stored
	 * in its Container here within this method.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 * @throws RuntimeException
	 */
	protected function init_properties() {
		$this->fulcrum           = Fulcrum::getFulcrum();
		$widget_container_key = get_class( $this );

		if ( ! $this->fulcrum->has( $widget_container_key ) ) {
			throw new RuntimeException( sprintf( '%s %s', __( 'The specified widget config file is not available in the container.', 'fulcrum' ), $widget_container_key ) );
		}

		$this->config = $this->fulcrum[ $widget_container_key ];
	}

	/**
	 * Initialize the widget
	 *
	 * @since 1.0.0
	 *
	 * @@return null
	 */
	protected function init() { /* do nothing */ }

	/****************************
	 * Render to Front-end
	 ****************************/

	/**
	 * Echo the widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Display arguments including
	 *                          before_title, after_title, before_widget, & after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 *
	 * @return null
	 */
	public function widget( $args, $instance ) {
		$this->init_instance( $instance );

		echo $this->modify_before_widget_html( $args['before_widget'], $instance );

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		$this->render_widget( $args, $instance );

		echo $args['after_widget'];
	}

	/**
	 * Render the HTML for the widget
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Display arguments including
	 *                          before_title, after_title, before_widget, & after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 *
	 * @return null
	 */
	abstract protected function render_widget( array &$args, array &$instance );

	/****************************
	 * Render to Back-end
	 ****************************/

	/**
	 * Echo the settings update form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current settings.
	 *
	 * @return null
	 */
	public function form( $instance ) {
		$this->init_instance( $instance );

		if ( is_readable( $this->config['form_view'] ) ) {
			include( $this->config['form_view'] );
		}
	}

	/****************************
	 * Helpers
	 ****************************/

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If false is returned, the instance won't be saved / updated.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array                    Settings to save or bool false to cancel saving
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * Initialize the widget's instance by merging it with the defaults
	 *
	 * @since 1.1.1
	 *
	 * @param $instance
	 */
	protected function init_instance( &$instance ) {
		$instance = empty( $instance )
			? $this->config->defaults
			: wp_parse_args( (array) $instance, $this->config->defaults );
	}

	/**
	 * Modifies the before widget HTML by inserting the class, when
	 * configured.
	 *
	 * @since 1.1.1
	 *
	 * @param string $before_widget
	 * @param array $instance
	 *
	 * @return string
	 */
	protected function modify_before_widget_html( $before_widget, array $instance ) {
		if ( array_key_exists( 'class', $instance ) && ! empty( $instance['class'] ) ) {
			$before_widget = str_replace( 'class="widget ', 'class="' . esc_attr( $instance['class'] ) . ' widget ', $before_widget );
		}

		return $before_widget;
	}

}