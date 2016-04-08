<?php namespace Fulcrum\Widgets;

/**
 * Widget Contract
 *
 * @package     Fulcrum\Custom\Widget
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

namespace Fulcrum\Custom\Widget;

interface Widget_Contract {

	/**
	 * Echo the widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args       Display arguments including
	 *                          before_title, after_title, before_widget, & after_widget.
	 * @param array $instance   The settings for the particular instance of the widget
	 * @return void
	 */
	public function widget( $args, $instance );

	/**
	 * Echo the settings update form.
	 *
	 * @since 1.0.0
	 *
	 * @param array     $instance   Current settings.
	 * @return void
	 */
	public function form( $instance );

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If false is returned, the instance won't be saved / updated.
	 *
	 * @since 1.0.0
	 *
	 * @param array     $new_instance New settings for this instance as input by the user via form().
	 * @param array     $old_instance Old settings for this instance.
	 *
	 * @return array    Settings to save or bool false to cancel saving
	 */
	public function update( $new_instance, $old_instance );
}