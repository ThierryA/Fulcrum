<?php

/**
 * Str Helpers Functions
 *
 * @package     Fulcrum
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+ and MIT License (MIT)
 */

/**
 * This class has been adapted from the Laravel Illuminate framework, which
 * is copyrighted to Taylor Otwell and carries a MIT Licence (MIT).
 * Changes reflect WordPress coding standard, compliance with PHP 5.3, +
 * additional functionality.
 */

use Fulcrum\Support\Helpers\Str;

if ( ! function_exists( 'fulcrum_ends_with') ) {
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     */
    function fulcrum_ends_with( $haystack, $needles ) {
        return Str::ends_with( $haystack, $needles );
    }
}

if ( ! function_exists( 'fulcrum_parse_callback' ) ) {
    /**
     * Parse a Class@method style callback into class and method.
     *
     * @param  string  $callback
     * @param  string  $default
     * @return array
     */
    function fulcrum_parse_callback( $callback, $default ) {
        return Str::parse_callback( $callback, $default );
    }
}

if ( ! function_exists( 'fulcrum_starts_with' ) ) {
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    function fulcrum_starts_with( $haystack, $needles ) {
        return Str::starts_with( $haystack, $needles );
    }
}

if ( ! function_exists( 'fulcrum_snake_case' ) ) {
    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    function fulcrum_snake_case( $value, $delimiter = '_' ) {
        return Str::snake( $value, $delimiter );
    }
}

if ( ! function_exists( 'fulcrum_str_contains' ) ) {
    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    function fulcrum_str_contains( $haystack, $needles ) {
        return Str::contains( $haystack, $needles );
    }
}

if ( ! function_exists( 'fulcrum_str_finish' ) ) {
    /**
     * Cap a string with a single instance of a given value.
     *
     * @param  string  $value
     * @param  string  $cap
     * @return string
     */
    function fulcrum_str_finish( $value, $cap ) {
        return Str::finish( $value, $cap );
    }
}

if ( ! function_exists('fulcrum_str_is') ) {
    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  string  $pattern
     * @param  string  $value
     * @return bool
     */
    function fulcrum_str_is( $pattern, $value ) {
        return Str::is( $pattern, $value );
    }
}

if ( ! function_exists( 'fulcrum_str_length' ) ) {
    /**
     * Return the length of the given string.
     *
     * @param  string  $value
     * @return int
     */
    function fulcrum_str_length( $value ) {
        return Str::length( $value );
    }
}

if ( ! function_exists( 'fulcrum_limit_by_characters' ) ) {
    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    function fulcrum_limit_by_characters( $value, $limit = 100, $end = '...' ) {
        return Str::limit_by_characters( $value, $limit, $end );
    }
}

if ( ! function_exists( 'fulcrum_word_limiter' ) ) {
    /**
     * Limit the number of words in a string.
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    function fulcrum_word_limiter( $value, $limit = 30, $end = '...' ) {
        return Str::word_limiter( $value, $limit, $end );
    }
}

if ( ! function_exists( 'fulcrum_str_random' ) ) {
    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int  $length
     * @return string
     *
     * @throws \RuntimeException
     */
    function fulcrum_str_random( $length = 16 ) {
        return Str::random( $length );
    }
}

if ( ! function_exists( 'fulcrum_str_replace_array' ) ) {
    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param  string  $search
     * @param  array   $replace
     * @param  string  $subject
     * @return string
     */
    function fulcrum_str_replace_array( $search, array $replace, $subject ) {
        foreach ( $replace as $value ) {
            $subject = preg_replace( '/'.$search.'/', $value, $subject, 1 );
        }

        return $subject;
    }
}

if ( ! function_exists( 'fulcrum_studly_case' ) ) {
    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    function fulcrum_studly_case( $value ) {
        return Str::studly( $value );
    }
}

if ( ! function_exists( 'fulcrum_str_title' ) ) {
    /**
     * Convert the given string to title case.
     *
     * @param  string  $value
     * @return string
     */
    function fulcrum_str_title( $value ) {
        return Str::title( $value );
    }
}

if ( ! function_exists( 'fulcrum_to_lowercase' ) ) {
    /**
     * Convert the given string to lower-case.
     *
     * @param  string  $value
     * @return string
     */
    function fulcrum_to_lowercase( $value ) {
        return Str::lower( $value );
    }
}

if ( ! function_exists( 'fulcrum_to_uppercase' ) ) {
    /**
     * Convert the given string to upper-case.
     *
     * @param  string  $value
     * @return string
     */
    function fulcrum_to_uppercase( $value ) {
        return Str::upper( $value );
    }
}

if ( ! function_exists( 'fulcrum_is_string' ) ) {
    /**
     * Checks if the variable passed in is not empty & is a string type.
     * 
     * @since  1.0.0
     *
     * @param  string  $value
     * @return bool             Returns true if string & not empty
     */
    function fulcrum_is_string( $value ) {
        return Str::is_string( $value );
    }
}

if ( ! function_exists( 'fulcrum_is_url' ) ) {
    /**
     * Checks if the variable passed in is a valid url.
     * 
     * @since  1.0.0
     *
     * @param  string  $value
     * @return bool             Returns true if valid url; else false.
     */
    function fulcrum_is_url( $value ) {
        return Str::is_valid_url( $value );
    }
}