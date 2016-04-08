<?php

/**
 * Array Helpers Functions
 *
 * @package     Fulcrum
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+ and MIT Licence (MIT)
 */

/**
 * This class has been adapted from the Laravel Illuminate framework, which
 * is copyrighted to Taylor Otwell and carries a MIT Licence (MIT).
 * Changes reflect WordPress coding standard, compliance with PHP 5.3, +
 * additional functionality.
 */

use Fulcrum\Support\Helpers\Arr;

if ( ! function_exists( 'fulcrum_remove_empty_elements_from_array' ) ) {
	/**
	 * Remove all of the empty elements from the given array.  A new array is built
	 * from the filter and returned.
	 *
	 * @since 1.0.0
	 *
	 * @param array $array Array to be filtered.
	 *
	 * @returns Returns a new array without the empties.
	 */
	function fulcrum_remove_empty_elements_from_array( $array ) {
		return array_filter( $array, function ( $value ) {
			return $value;
		} );
	}
}

if ( ! function_exists( 'fulcrum_array_add' ) ) {
	/**
	 * Add an element to an array using "dot" notation if it doesn't exist.
	 *
	 * @param  array $array
	 * @param  string $key
	 * @param  mixed $value
	 *
	 * @return array
	 */
	function fulcrum_array_add( $array, $key, $value ) {
		return Arr::add( $array, $key, $value );
	}
}

if ( ! function_exists( 'fulcrum_array_dot' ) ) {
	/**
	 * Flatten a multi-dimensional associative array with dots.
	 *
	 * @param  array $array
	 * @param  string $prepend
	 *
	 * @return array
	 */
	function fulcrum_array_dot( $array, $prepend = '' ) {
		return Arr::dot( $array, $prepend );
	}
}

if ( ! function_exists( 'fulcrum_array_except' ) ) {
	/**
	 * Get all of the given array except for a specified array of items.
	 *
	 * @param  array $array
	 * @param  array|string $keys
	 *
	 * @return array
	 */
	function fulcrum_array_except( $array, $keys ) {
		return Arr::except( $array, $keys );
	}
}

if ( ! function_exists( 'fulcrum_array_fetch' ) ) {
	/**
	 * Fetch a flattened array of a nested array element.
	 *
	 * @param  array $array
	 * @param  string $key
	 *
	 * @return array
	 */
	function fulcrum_array_fetch( $array, $key ) {
		return Arr::fetch( $array, $key );
	}
}

if ( ! function_exists( 'fulcrum_array_first' ) ) {
	/**
	 * Return the first element in an array passing a given truth test.
	 *
	 * @param  array $array
	 * @param  \Closure $callback
	 * @param  mixed $default
	 *
	 * @return mixed
	 */
	function fulcrum_array_first( $array, $callback, $default = null ) {
		return Arr::first( $array, $callback, $default );
	}
}

if ( ! function_exists( 'fulcrum_array_head' ) ) {
	/**
	 * Get the first element of an array. Useful for method chaining.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $array
	 *
	 * @return mixed
	 */
	function fulcrum_array_head( $array ) {
		return reset( $array );
	}
}

if ( ! function_exists( 'fulcrum_is_array' ) ) {
	/**
	 * Checks if the element within the array is a valid array - uses key dot notation.
	 *
	 * @since 1.0.0
	 *
	 * @param array|mixed $array
	 * @param string $key
	 * @param bool|true $valid_if_not_empty
	 *
	 * @return bool
	 */
	function fulcrum_is_array( $array, $key = '', $valid_if_not_empty = true ) {
		return Arr::is_array( $array, $key, $valid_if_not_empty );
	}
}

if ( ! function_exists( 'fulcrum_array_last' ) ) {
	/**
	 * Return the last element in an array passing a given truth test.
	 *
	 * @param  array $array
	 * @param  \Closure $callback
	 * @param  mixed $default
	 *
	 * @return mixed
	 */
	function fulcrum_array_last( $array, $callback, $default = null ) {
		return Arr::last( $array, $callback, $default );
	}
}

if ( ! function_exists( 'fulcrum_array_flatten' ) ) {
	/**
	 * Flatten a multi-dimensional array into a single level.
	 *
	 * @param  array $array
	 *
	 * @return array
	 */
	function fulcrum_array_flatten( $array ) {
		return Arr::flatten( $array );
	}
}

if ( ! function_exists( 'fulcrum_array_forget' ) ) {
	/**
	 * Remove one or many array items from a given array using "dot" notation.
	 *
	 * @param  array $array
	 * @param  array|string $keys
	 *
	 * @return void
	 */
	function fulcrum_array_forget( &$array, $keys ) {
		return Arr::forget( $array, $keys );
	}
}

if ( ! function_exists( 'fulcrum_array_get' ) ) {
	/**
	 * Get an item from an array using "dot" notation.
	 *
	 * @param  array $array
	 * @param  string $key
	 * @param  mixed $default
	 *
	 * @return mixed
	 */
	function fulcrum_array_get( $array, $key, $default = null ) {
		return Arr::get( $array, $key, $default );
	}
}

if ( ! function_exists( 'fulcrum_array_has' ) ) {
	/**
	 * Check if an item exists in an array using "dot" notation.
	 *
	 * @param  array $array
	 * @param  string $key
	 *
	 * @return bool
	 */
	function fulcrum_array_has( $array, $key ) {
		return Arr::has( $array, $key );
	}
}

if ( ! function_exists( 'fulcrum_array_only' ) ) {
	/**
	 * Get a subset of the items from the given array.
	 *
	 * @param  array $array
	 * @param  array|string $keys
	 *
	 * @return array
	 */
	function fulcrum_array_only( $array, $keys ) {
		return Arr::only( $array, $keys );
	}
}

if ( ! function_exists( 'fulcrum_array_pluck' ) ) {
	/**
	 * Pluck an array of values from an array.
	 *
	 * @param  array $array
	 * @param  string $value
	 * @param  string $key
	 *
	 * @return array
	 */
	function fulcrum_array_pluck( $array, $value, $key = null ) {
		return Arr::pluck( $array, $value, $key );
	}
}

if ( ! function_exists( 'fulcrum_array_pull' ) ) {
	/**
	 * Get a value from the array, and remove it.
	 *
	 * @param  array $array
	 * @param  string $key
	 * @param  mixed $default
	 *
	 * @return mixed
	 */
	function fulcrum_array_pull( &$array, $key, $default = null ) {
		return Arr::pull( $array, $key, $default );
	}
}

if ( ! function_exists( 'fulcrum_array_set' ) ) {
	/**
	 * Set an array item to a given value using "dot" notation.
	 *
	 * If no key is given to the method, the entire array will be replaced.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $array
	 * @param  string $key
	 * @param  mixed $value
	 * @param bool $append When true, appends the value to the current value
	 *
	 * @return array
	 */
	function fulcrum_array_set( &$array, $key, $value, $append = false ) {
		return Arr::set( $array, $key, $value, $append );
	}
}

if ( ! function_exists( 'fulcrum_array_sort' ) ) {
	/**
	 * Sort the array using the given Closure.
	 *
	 * @param  array $array
	 * @param  \Closure $callback
	 *
	 * @return array
	 */
	function fulcrum_array_sort( $array, Closure $callback ) {
		return Arr::sort( $array, $callback );
	}
}

if ( ! function_exists( 'fulcrum_array_where' ) ) {
	/**
	 * Filter the array using the given Closure.
	 *
	 * @param  array $array
	 * @param  \Closure $callback
	 *
	 * @return array
	 */
	function fulcrum_array_where( $array, Closure $callback ) {
		return Arr::where( $array, $callback );
	}
}

if ( ! function_exists( 'fulcrum_preg_replace_sub' ) ) {
	/**
	 * Replace a given pattern with each value in the array in sequentially.
	 *
	 * @param  string $pattern
	 * @param  array $replacements
	 * @param  string $subject
	 *
	 * @return string
	 */
	function fulcrum_preg_replace_sub( $pattern, &$replacements, $subject ) {
		return preg_replace_callback( $pattern, function ( $match ) use ( &$replacements ) {
			return array_shift( $replacements );

		}, $subject );
	}
}

if ( ! function_exists( 'fulcrum_flatten_array_into_delimited_list' ) ) {
	/**
	 * Flatten an array into a delimited list.
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $to_be_converted Data to be converted.
	 * @param string $list_delimiter Delimiter to be used.
	 *
	 * @return string
	 */
	function fulcrum_flatten_array_into_delimited_list( $to_be_converted, $list_delimiter = ', ' ) {
		if ( is_array( $to_be_converted ) ) {
			return implode( $list_delimiter, $to_be_converted );
		}

		return $to_be_converted;
	}
}