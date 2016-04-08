<?php

/**
 * Date and Time Helpers Functions
 *
 * @package     Fulcrum
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+ and MIT Licence (MIT)
 */

if ( ! function_exists( 'fulcrum_get_current_datetime' ) ) {
	/**
	 * Get the Current DateTime
	 *
	 * @since 1.0.0
	 *
	 * @param string    $timezone       Defaults to America/Chicago
	 * @return DateTime
	 */
	function fulcrum_get_current_datetime( $timezone = 'America/Chicago' ) {
		return fulcrum_convert_string_to_datetime( '', $timezone );
	}
}

if ( ! function_exists( 'fulcrum_convert_string_to_datetime' ) ) {
	/**
	 * Convert time to DateTime and specified timezone
	 *
	 * @since 1.0.0
	 *
	 * @param string    $time_to_set
	 * @param string    $timezone       Defaults to America/Chicago
	 * @return DateTime
	 */
	function fulcrum_convert_string_to_datetime( $time_to_set = '', $timezone = 'America/Chicago' ) {

		date_default_timezone_set( $timezone );
		$dt = new DateTime( $time_to_set );
		$dt->setTimezone( new DateTimeZone( $timezone ) );

		return $dt;
	}
}

if ( ! function_exists( 'fulcrum_format_string_to_datetime' ) ) {
	/**
	 * Formats a datetime string to the specified timezone and format
	 *
	 * @since 1.0.0
	 *
	 * @param string    $time_to_set
	 * @param string    $format         Defaults to "Y-m-d H:i:s"
	 * @param string    $timezone       Defaults to America/Chicago
	 * @return DateTime
	 */
	function fulcrum_format_string_to_datetime( $time_to_set = '', $format = 'Y-m-d H:i:s', $timezone = 'America/Chicago' ) {

		date_default_timezone_set( $timezone );
		$dt = new DateTime( $time_to_set );
		$dt->setTimezone( new DateTimeZone( $timezone ) );

		return $dt->format( $format );
	}
}

if ( ! function_exists( 'fulcrum_is_later_than_now' ) ) {
	/**
	 * Checks if the time passed in is past the current datetime
	 *
	 * @since 1.0.0
	 *
	 * @param string        $time_to_compare
	 * @param string        $now
	 * @param string        $timezone           Defaults to America/Chicago
	 * @return DateTime
	 */
	function fulcrum_is_later_than_now( $time_to_compare, $now = '', $timezone = 'America/Chicago' ) {
		$now = $now ?: fulcrum_convert_string_to_datetime( '', $timezone );
		$dt  = fulcrum_convert_string_to_datetime( $time_to_compare, $timezone );
		return $dt > $now;
	}
}

if ( ! function_exists( 'fulcrum_add_hours_to_datetime' ) ) {
	/**
	 * Add to DateTime
	 *
	 * @since 1.0.0
	 *
	 * @param int           $number_of_hours_to_add
	 * @param string        $datetime
	 * @param bool          $return_as_formated_string
	 * @param string        $format             Defaults to "Y-m-d H:i:s"
	 * @param string        $timezone           Defaults to America/Chicago
	 * @return DateTime
	 */
	function fulcrum_add_hours_to_datetime( $number_of_hours_to_add, $datetime, $return_as_formated_string = false, $format = 'Y-m-d H:i:s', $timezone = 'America/Chicago' ) {

		$dt = fulcrum_convert_string_to_datetime( $datetime, $timezone );
		$dt->add( new DateInterval( 'PT' . $number_of_hours_to_add . 'H' ) );

		return $return_as_formated_string
			? $dt->format( $format )
			: $dt;
	}
}