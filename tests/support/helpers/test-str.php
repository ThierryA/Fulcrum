<?php namespace Fulcrum\Tests\Helpers;

use WP_UnitTestCase;
use Fulcrum\Support\Helpers\Str;

class Str_Test extends WP_UnitTestCase {

    public function test_string_can_be_limited_by_words() {
        $this->assertEquals( 'Benjamin...', Str::word_limiter( 'Benjamin Franklin', 1 ) );
        $this->assertEquals( 'Benjamin___', Str::word_limiter( 'Benjamin Franklin', 1, '___' ) );
        $this->assertEquals( 'Benjamin Franklin', Str::words( 'Benjamin Franklin', 3 ) );
        $this->assertEquals( 'Benjamin Franklin', fulcrum_word_limiter( 'Benjamin Franklin', 3 ) );
    }

    public function test_string_trimmed_only_where_necessary() {
        $this->assertEquals( ' Benjamin Franklin ', Str::words( ' Benjamin Franklin ', 3 ) );
        $this->assertEquals( ' Benjamin...', Str::words( ' Benjamin Franklin ', 1 ) );
        $this->assertEquals( ' Benjamin...', fulcrum_word_limiter( ' Benjamin Franklin ', 1 ) );
    }

    public function test_string_title() {
        $this->assertEquals( 'Benjamin Franklin', Str::title( 'benjamin franklin' ) );
        $this->assertEquals( 'Benjamin Franklin', Str::title( 'benJaMin fraNKliN' ) );
        $this->assertEquals( 'Benjamin Franklin', fulcrum_str_title( 'benJaMin fraNKliN' ) );
    }

    public function test_string_without_words_does_not_produce_error() {
        $nbsp = chr(0xC2).chr(0xA0);
        $this->assertEquals( ' ', Str::words(' ') );
        $this->assertEquals( $nbsp, Str::words( $nbsp ) );
    }

    function test_starts_with() {
        $this->assertTrue( Str::starts_with( 'wpdc', 'wp' ) );
        $this->assertTrue( fulcrum_starts_with( 'wpdc', 'wpdc' ) );
        $this->assertTrue( Str::starts_with( 'wpdc', array( 'wpd' ) ) );
        $this->assertFalse( Str::starts_with( 'wpdc', 'dc' ) );
        $this->assertFalse( fulcrum_starts_with( 'wpdc', array( 'dc' ) ) );
        $this->assertFalse( Str::starts_with( 'wpdc', '' ) );
    }

    function test_ends_with() {
        $this->assertTrue( Str::ends_with( 'wpdc', 'dc' ) );
        $this->assertTrue( fulcrum_ends_with( 'wpdc', 'wpdc' ) );
        $this->assertTrue( Str::ends_with( 'wpdc', array( 'dc' ) ) );
        $this->assertFalse( Str::ends_with( 'wpdc', 'pd' ) );
        $this->assertFalse( fulcrum_ends_with( 'wpdc', array( 'wpd' ) ) );
        $this->assertFalse( Str::ends_with( 'wpdc', '' ) );
        $this->assertFalse( Str::ends_with( '7', ' 7' ) );
    }

    function test_str_contains() {
        $this->assertTrue( Str::contains( 'tonya', 'on' ) );
        $this->assertTrue( fulcrum_str_contains( 'tonya', array( 'ny' ) ) );
        $this->assertFalse( Str::contains( 'tonya', 'xxx' ) );
        $this->assertFalse( fulcrum_str_contains( 'tonya', array( 'xxx' ) ) );
        $this->assertFalse( Str::contains( 'tonya', '' ) );
    }

    public function test_parse_callback() {
        $this->assertEquals( array( 'Class', 'method' ), Str::parse_callback( 'Class@method', 'foo' ) );
        $this->assertEquals( array( 'Class', 'foo' ), fulcrum_parse_callback( 'Class', 'foo' ) );
    }

    public function test_finish() {
        $this->assertEquals( 'abbc', Str::finish( 'ab', 'bc' ) );
        $this->assertEquals( 'abbc', fulcrum_str_finish( 'abbcbc', 'bc' ) );
        $this->assertEquals( 'abcbbc', Str::finish( 'abcbbcbc', 'bc' ) );
    }

    public function test_is() {
        $this->assertTrue( Str::is( '/', '/' ) );
        $this->assertFalse( fulcrum_str_is( '/', ' /' ) );
        $this->assertFalse( Str::is( '/', '/a' ) );
        $this->assertTrue( fulcrum_str_is( 'foo/*', 'foo/bar/baz' ) );
        $this->assertTrue( Str::is( '*/foo', 'blah/baz/foo' ) );
    }

    public function test_lower() {
        $this->assertEquals( 'foo bar baz', Str::lower( 'FOO BAR BAZ' ) );
        $this->assertEquals( 'foo bar baz', fulcrum_to_lowercase( 'fOo Bar bAz' ) );
    }
    public function test_upper() {
        $this->assertEquals( 'FOO BAR BAZ', Str::upper( 'foo bar baz' ) );
        $this->assertEquals( 'FOO BAR BAZ', fulcrum_to_uppercase( 'foO bAr BaZ' ) );
    }

    public function test_limit_by_characters() {
        $this->assertEquals( 'WordPress...', Str::limit_by_characters( 'WordPress is web software you can use to create a beautiful website or blog.', 9 ) );
        $this->assertEquals( 'WordPress is...', fulcrum_limit_by_characters( 'WordPress is web software you can use to create a beautiful website or blog.', 13 ) );
    }

    public function test_word_limiter() {
        $this->assertEquals( 'WordPress is web software...', Str::word_limiter( 'WordPress is web software you can use to create a beautiful website or blog.', 4 ) );
        $this->assertEquals( 'WordPress is...', fulcrum_word_limiter( 'WordPress is web software you can use to create a beautiful website or blog.', 2 ) );
    }

    public function test_length() {
        $this->assertEquals( 11, Str::length( 'foo bar baz' ) );
        $this->assertEquals( 11, fulcrum_str_length( 'foo bar baz' ) );
    }

    public function test_quick_random() {
        $random_integer = mt_rand(1, 100);

        $this->assertEquals($random_integer, strlen( Str::quick_random( $random_integer ) ) );
        $this->assertInternalType( 'string', Str::quick_random() );
        $this->assertEquals( 16, strlen( Str::quick_random() ) );
    }

    public function test_random() {
        $this->assertEquals( 16, strlen( Str::random() ) );

        $random_integer = mt_rand( 1, 100 );
        $this->assertEquals( $random_integer, strlen( Str::random( $random_integer ) ) );
        $this->assertEquals( $random_integer, strlen( fulcrum_str_random( $random_integer ) ) );

        $this->assertInternalType( 'string', Str::random() );
    }

    public function test_snake() {
        $this->assertEquals( 'w_p_devs_club', Str::snake( 'WPDevsClub' ) );
        $this->assertEquals( 'word_press', Str::snake( 'WordPress' ) );
        $this->assertEquals('wpdc', fulcrum_snake_case( 'wpdc' ) );
    }

    function test_string_is() {
        $this->assertFalse( fulcrum_is_string( '' ) );
        $this->assertFalse( fulcrum_is_string( array() ) );
        $this->assertFalse( fulcrum_is_string( true ) );
        $this->assertTrue( fulcrum_is_string( 'foo' ) );
    }

    function test_is_url() {
        
        $this->assertFalse( Str::is_valid_url( 'foo' ) );
        $this->assertFalse( fulcrum_is_url( 'foo' ) );

        $this->assertFalse( Str::is_valid_url( '//foo.com' ) );
        $this->assertFalse( fulcrum_is_url( '//foo.com' ) );

        $this->assertTrue( Str::is_valid_url( 'http://foo.com' ) );
        $this->assertTrue( fulcrum_is_url( 'http://foo.com' ) );

        $this->assertTrue( Str::is_valid_url( 'https://foo.com' ) );
        $this->assertTrue( fulcrum_is_url( 'https://foo.com' ) );

        $this->assertFalse( Str::is_valid_url( 'mailto:foo@foo.com' ) );
        $this->assertFalse( fulcrum_is_url( 'mailto:foo@foo.com' ) );

        $this->assertFalse( Str::is_valid_url( 'http:foo@foo.com' ) );
        $this->assertFalse( fulcrum_is_url( 'http:foo@foo.com' ) );

        $this->assertTrue( Str::is_valid_url( 'http://foo.compage/?id=1' ) );
        $this->assertTrue( fulcrum_is_url( 'http://foo.compage/?id=1' ) );
    }

}