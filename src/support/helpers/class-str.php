<?php namespace Fulcrum\Support\Helpers;

/**
 * String Helpers - Static Collection of Helpers for Str Data Type
 *
 * @package     Fulcrum\Support\Helpers
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */

use RuntimeException;

class Str {

    /**
     * The cache of snake-cased words.
     *
     * @since 1.0.0
     *
     * @var array
     */
    protected static $snake_cache = array();

    /**
     * The cache of camel-cased words.
     *
     * @since 1.0.0
     *
     * @var array
     */
    protected static $camel_cache = array();

    /**
     * The cache of studly-cased words.
     *
     * @since 1.0.0
     *
     * @var array
     */
    protected static $studly_cache = array();

    /**
     * Convert a value to camel case.
     *
     * @since 1.0.0
     *
     * @param  string  $value
     * @return string
     */
    public static function camel( $value ) {
        if ( isset( static::$camel_cache[ $value ] ) ) {
            return static::$camel_cache[ $value ];
        }

        return static::$camel_cache[ $value ] = lcfirst( static::studly( $value ) );
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @since 1.0.0
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function contains( $haystack, $needles ) {
        foreach ( (array) $needles as $needle ) {
            if ( $needle && strpos( $haystack, $needle ) !== false ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @since 1.0.0
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function ends_with( $haystack, $needles ) {
        foreach ( (array) $needles as $needle ) {
            if ( (string) $needle === substr( $haystack, -strlen( $needle ) ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cap a string with a single instance of a given value.
     *
     * @since 1.0.0
     *
     * @param  string  $value
     * @param  string  $cap
     * @return string
     */
    public static function finish( $value, $cap ) {
        $quoted = preg_quote( $cap, '/' );

        return preg_replace( '/(?:'.$quoted.')+$/', '', $value ).$cap;
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * @since 1.0.0
     *
     * @param  string  $pattern
     * @param  string  $value
     * @return bool
     */
    public static function is( $pattern, $value ) {
        if ( $pattern == $value ) {
            return true;
        }

        $pattern = preg_quote( $pattern, '#' );

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace( '\*', '.*', $pattern ).'\z';

        return (bool) preg_match( '#^'.$pattern.'#', $value );
    }

    /**
     * Return the length of the given string.
     *
     * @since 1.0.0
     *
     * @param  string  $value
     * @return int
     */
    public static function length( $value ) {
        return mb_strlen( $value );
    }

    /**
     * Limit the number of characters in a string.
     *
     * @since 1.0.0
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    public static function limit_by_characters( $value, $limit = 100, $end = '...' ) {
        if ( mb_strlen( $value ) <= $limit ) {
            return $value;
        }

        return rtrim( mb_substr( $value, 0, $limit, 'UTF-8' ) ).$end;
    }

    /**
     * Limit the number of characters in a string.
     *
     * @since 1.0.0
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    public static function limit( $value, $limit = 100, $end = '...' ) {
        self::limit_by_characters( $value, $limit, $end );
    }

    /**
     * Convert the given string to lower-case.
     *
     * @since 1.0.0
     *
     * @param  string  $value
     * @return string
     */
    public static function lower( $value ) {
        return mb_strtolower( $value );
    }

    /**
     * Limit the number of words in a string.
     *
     * @since 1.0.0
     *
     * @param  string  $value
     * @param  int     $words
     * @param  string  $end
     * @return string
     */
    public static function word_limiter( $value, $words = 100, $end = '...' ) {
        preg_match( '/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches );

        if ( ! isset( $matches[0]) || strlen($value) === strlen($matches[0] ) ) {
            return $value;
        }

        return rtrim( $matches[0] ).$end;
    }

    public static function words( $value, $words = 30, $end = '...' ) {
        return self::word_limiter( $value, $words, $end );
    }

    /**
     * Parse a Class@method style callback into class and method.
     *
     * @since 1.0.0
     *
     * @param  string  $callback
     * @param  string  $default
     * @return array
     */
    public static function parse_callback( $callback, $default )  {
        return static::contains( $callback, '@' )
            ? explode( '@', $callback, 2 )
            : array( $callback, $default );
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @since 1.0.0
     *
     * @param  int  $length
     * @return string
     *
     * @throws RuntimeException
     */
    public static function random($length = 16) {
        if ( ! function_exists( 'openssl_random_pseudo_bytes' ) ) {
            throw new RuntimeException( __( 'OpenSSL extension is required.', 'fulcrum' ) );
        }

        $bytes = openssl_random_pseudo_bytes ($length * 2 );

        if ($bytes === false) {
            throw new RuntimeException( __( 'Unable to generate random string.', 'fulcrum' ) );
        }

        return substr( str_replace( array( '/', '+', '=' ), '', base64_encode( $bytes ) ), 0, $length );
    }

    /**
     * Generate a "random" alpha-numeric string.
     *
     * Should not be considered sufficient for cryptography, etc.
     *
     * @since 1.0.0
     *
     * @param  int  $length
     * @return string
     */
    public static function quick_random( $length = 16 ) {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr( str_shuffle( str_repeat( $pool, $length ) ), 0, $length );
    }

    /**
     * Convert the given string to upper-case.
     *
     * @since 1.0.0
     *
     * @param  string  $value
     * @return string
     */
    public static function upper( $value ) {
        return mb_strtoupper( $value );
    }

    /**
     * Convert the given string to title case.
     *
     * @since 1.0.0
     *
     * @param  string  $value
     * @return string
     */
    public static function title( $value ) {
        return mb_convert_case( $value, MB_CASE_TITLE, 'UTF-8' );
    }

    /**
     * Convert a string to snake case.
     *
     * @since 1.0.0
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    public static function snake( $value, $delimiter = '_' ) {
        $key = $value.$delimiter;

        if ( isset( static::$snake_cache[ $key ] ) ) {
            return static::$snake_cache[ $key ];
        }

        if ( ! ctype_lower( $value ) ) {
            $value = strtolower( preg_replace( '/(.)(?=[A-Z])/', '$1'.$delimiter, $value ) );
        }

        return static::$snake_cache[ $key ] = $value;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @since 1.0.0
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function starts_with( $haystack, $needles ) {
        foreach ( (array) $needles as $needle) {
            if ( $needle && strpos( $haystack, $needle ) === 0 ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert a value to studly caps case.
     *
     * @since 1.0.0
     *
     * @param  string  $value
     * @return string
     */
    public static function studly( $value ) {
        $key = $value;

        if ( isset( static::$studly_cache[ $key ] ) ) {
            return static::$studly_cache[ $key ];
        }

        $value = ucwords( str_replace( array( '-', '_' ), ' ', $value ) );

        return static::$studly_cache[ $key ] = str_replace( ' ', '', $value );
    }

    /**
     * Checks if the variable passed in is not empty & is a string type.
     * 
     * @since  1.0.0
     *
     * @param  string  $value
     * @return bool             Returns true if string & not empty
     */
    public static function is_string( $value ) {
        return ( $value && is_string( $value ) );
    }

    /**
     * Checks if the variable passed in is a valid url.
     * 
     * @since  1.0.0
     *
     * @param  string  $value
     * @return bool             Returns true if valid url; else false.
     */
    public static function is_valid_url( $value ) {

        if ( ! self::is_string( $value ) ) {
            return false;
        }

        return ( preg_match('%^https?://[^\s]+$%', $value ) && 
                false !== filter_var( $value, FILTER_VALIDATE_URL ) );

    }
}