<?php namespace Fulcrum\Tests\Helpers;

use WP_UnitTestCase;
use Fulcrum\Support\Helpers\Arr;

class Arr_Helpers_Test extends WP_UnitTestCase {

    function test_dot() {
        $data = array(
            'name'      => 'Tonya',
            'languages' => array(
                'php'           => true,
                'javascript'    => true,
            )
        );
        $expected = array(
            'name' => 'Tonya',
            'languages.php' => true,
            'languages.javascript' => true
        );
        $this->assertEquals( $expected, Arr::dot( $data ) );
        $this->assertEquals( $expected, fulcrum_array_dot( $data ) );
    }

    function test_except() {
        $data = array(
            'name'          => 'Tonya',
            'favorite_team' => 'Brewers',
        );
        $expected = array( 'favorite_team' => 'Brewers' );
        $this->assertEquals( $expected, Arr::except( $data, array( 'name' ) ) );
        $this->assertEquals( $expected, fulcrum_array_except(  $data, array( 'name' ) ) );
    }

    function test_fetch() {
        $data = array(
            'post-1' => array(
                'comments' => array( 'tags' => array( '#foo', '#bar' ) ),
            ),
            'post-2' => array(
                'comments' => array( 'tags' => array( '#baz' ) ),
            ),
        );
        $expected = array(
            0 => array( 'tags' => array( '#foo', '#bar' ) ),
            1 => array( 'tags' => array( '#baz' ) ),
        );
        $this->assertEquals( $expected, Arr::fetch( $data, 'comments' ) );
        $this->assertEquals( $expected, fulcrum_array_fetch( $data, 'comments' ) );

        $expected = array( array( '#foo', '#bar' ), array( '#baz' ) );
        $this->assertEquals( $expected, Arr::fetch( $data, 'comments.tags' ) );
        $this->assertEquals( $expected, fulcrum_array_fetch( $data, 'comments.tags' ) );

        $this->assertEquals( array(), Arr::fetch( $data, 'foo' ) );
        $this->assertEquals( array(), fulcrum_array_fetch( $data, 'foo' ) );

        $this->assertEquals( array(), Arr::fetch( $data, 'foo.bar' ) );
        $this->assertEquals( array(), fulcrum_array_fetch( $data, 'foo.bar' ) );
    }

    function test_first() {
        $data = array( 'dev1' => 'Tonya', 'dev2' => 'Julie' );

        $this->assertEquals( 'Julie', Arr::first( $data, function( $key, $value) { return $value == 'Julie'; } ) );
        $this->assertEquals( 'Julie', fulcrum_array_first( $data, function( $key, $value) { return $value == 'Julie'; } ) );
    }

    function test_last() {
        $data = array( 100, 250, 290, 320, 500, 560, 670 ) ;

        $this->assertEquals( 670, Arr::last( $data, function( $key, $value ) { return $value > 320; } ) );
        $this->assertEquals( 670, fulcrum_array_last( $data, function( $key, $value ) { return $value > 320; } ) );
    }

    function test_flatten() {
        $data = array(
            array( '#foo', '#bar' ),
            array( '#baz' )
        );
        $expected = array( '#foo', '#bar', '#baz' );

        $this->assertEquals( $expected, Arr::flatten( $data ) );
        $this->assertEquals( $expected, fulcrum_array_flatten( $data ) );
    }

    function test_forget_and_drop() {
        $data = array( 'names' => array( 'developer1' => 'Tonya', 'developer2' => 'Julie', 'developer3' => 'Mike' ) );
        fulcrum_array_forget( $data, 'names.developer1' );
        Arr::forget( $data, 'names.developer3' );

        $this->assertFalse( isset( $data['names']['developer1'] ) );
        $this->assertFalse( isset( $data['names']['developer3'] ) );
        $this->assertTrue( isset( $data['names']['developer2'] ) );
        $this->assertEquals( array( 'names' => array( 'developer2' => 'Julie' ) ), $data );

        $data = array(
            'names'     => array( 'developer1' => 'Tonya', 'developer2' => 'Julie', 'developer3' => 'Mike' ),
            'emails'    => array( 'developer1' => 'foo', 'developer2' => 'bar', 'developer3' => 'baz' ),
        );
        $expected = array(
            'names'     => array( 'developer2' => 'Julie', 'developer3' => 'Mike' ),
            'emails'    => array( 'developer1' => 'foo', 'developer2' => 'bar' ),
        );
        fulcrum_array_forget( $data, 'names.developer1' );
        Arr::forget( $data, 'emails.developer3' );

        $this->assertFalse( isset( $data['names']['developer1'] ) );
        $this->assertFalse( isset( $data['emails']['developer3'] ) );
        $this->assertTrue( isset( $data['names']['developer3'] ) );
        $this->assertTrue( isset( $data['emails']['developer1'] ) );
        $this->assertEquals( $expected, $data );
    }

    function test_get() {
        $data = array( 'names' => array(
	        'developer' => array( 'Tonya', 'Julie' ),
	        'foo'       => array(
		        'bar'   => array( 'baz1', 'baz2' ),
	        ),
        ) );

        $this->assertEquals( array( 'Tonya', 'Julie' ), fulcrum_array_get( $data, 'names.developer' ) );
	    $this->assertEquals( array( 'baz1', 'baz2' ), fulcrum_array_get( $data, 'names.foo.bar' ) );
        $this->assertEquals( 'Frank', Arr::get( $data, 'names.developer2', 'Frank' ) );
        $this->assertEquals( 'Frank', fulcrum_array_get( $data, 'names.developer2', function() { return 'Frank'; } ) );
    }

    function test_has() {
        $data = array(
            'names' => array(
                'developer' => 'Tonya'
            ),
        );
        $this->assertTrue( fulcrum_array_has( $data, 'names' ) );
        $this->assertTrue( Arr::has( $data, 'names.developer', true ) );
        $this->assertFalse( fulcrum_array_has( $data, 'foo' ) );
        $this->assertFalse( Arr::has( $data, 'foo.bar' ) );
    }

    function test_is_array() {
        $data = array(
            'foo'               => array(
                'bar'           => array(
                    'baz'       => array(),
                    'foobar'    => 'Tonya',
                ),
            ),
        );

        $this->assertTrue( Arr::is_array( $data, 'foo' ) );
        $this->assertTrue( Arr::is_array( $data, 'foo.bar' ) );
        $this->assertFalse( Arr::is_array( $data, 'foo.bar.baz' ) );
        $this->assertTrue( Arr::is_array( $data, 'foo.bar.baz', false ) );
        $this->assertFalse( Arr::is_array( $data, 'foobar' ) );
        $this->assertFalse( Arr::is_array( $data, 'foo.bar.foobar', false ) );

        $this->assertTrue( fulcrum_is_array( $data, 'foo.bar' ) );
        $this->assertFalse( fulcrum_is_array( $data, 'foo.bar.baz' ) );

        $this->assertFalse( fulcrum_is_array( '' ) );
        $this->assertFalse( fulcrum_is_array( true ) );
        $this->assertFalse( fulcrum_is_array( array() ) );
        $this->assertFalse( fulcrum_is_array( array( 'foo' ) ) );
    }

    function test_only() {
        $array = array( 'name' => 'Tonya', 'email' => 'foo' );
        $this->assertEquals( array( 'name' => 'Tonya' ), Arr::only( $array, array( 'name' ) ) );
        $this->assertEquals( array( 'name' => 'Tonya' ), fulcrum_array_only( $array, array( 'name' ) ) );
        $this->assertSame( array(), Arr::only( $array, array( 'non_existing_key' ) ) );
        $this->assertSame( array(), fulcrum_array_only( $array, array( 'non_existing_key' ) ) );
    }

    function test_pluck_with_array_and_object_values() {
        $array = array(
            (object) array( 'name' => 'Tonya', 'email' => 'foo' ),
            array( 'name' => 'Julie', 'email' => 'bar' )
        );
        $expected = array( 'Tonya', 'Julie' );
        $this->assertEquals( $expected, Arr::pluck( $array, 'name' ) );
        $this->assertEquals( $expected, fulcrum_array_pluck( $array, 'name' ) );

        $expected = array( 'Tonya' => 'foo', 'Julie' => 'bar' );
        $this->assertEquals( $expected, Arr::pluck( $array, 'email', 'name' ) );
        $this->assertEquals( $expected, fulcrum_array_pluck( $array, 'email', 'name' ) );
    }

	function test_pluck_object_values() {
		$array = array(
			new AccessObjPropStub( 'Packers', 'Brewers', 'Bucks' ),
			new AccessObjPropStub( 'summer', 'fall', 'winter' )
		);
		$expected = array( 'Packers', 'summer' );
		$this->assertEquals( $expected, Arr::pluck( $array, 'foo' ) );
		$this->assertEquals( $expected, fulcrum_array_pluck( $array, 'foo' ) );

		$expected = array( 'Bucks' => 'Packers', 'winter' => 'summer' );
		$this->assertEquals( $expected, Arr::pluck( $array, 'foo', 'baz' ) );
		$this->assertEquals( $expected, fulcrum_array_pluck( $array, 'foo', 'baz' ) );

		$array = array(
			new AccessObjPropStub( array( 'name' => 'Tonya', 'email' => 'foo' ), 'Brewers', 'Bucks' ),
			new AccessObjPropStub( array( 'name' => 'Julie', 'email' => 'bar' ), 'fall', 'winter' ),
			new AccessObjPropStub( 'summer', 'fall', 'winter' ),
		);
		$expected = array( 'Tonya', 'Julie' );
		$this->assertEquals( $expected, Arr::pluck( $array, 'foo.name' ) );
		$this->assertEquals( $expected, fulcrum_array_pluck( $array, 'foo.name' ) );
	}

    function test_pull() {
        $data = array( 'developer1' => 'Tonya', 'developer2' => 'Julie' );
        $this->assertEquals( 'Julie', Arr::pull( $data, 'developer2' ) );
        $this->assertEquals( array( 'developer1' => 'Tonya' ), $data );

        $this->assertEquals( 'Tonya', fulcrum_array_pull( $data, 'developer1' ) );
        $this->assertEquals( array(), $data );
    }

    function test_set() {
        $array = array();
        Arr::set( $array, 'names.developer', 'Tonya');
        $this->assertEquals( 'Tonya', $array['names']['developer']);
        fulcrum_array_set( $array, 'names.developer', 'Julie' );
        $this->assertEquals( 'Julie', $array['names']['developer']);
    }

//    function test_sort() {
//        $array = array(
//            array( 'name' => 'baz' ),
//            array( 'name' => 'foo' ),
//            array( 'name' => 'bar' ),
//        );
//
//        $expected = array(
//            array( 'name' => 'bar' ),
//            array( 'name' => 'baz' ),
//            array( 'name' => 'foo' )
//        );
//
//        $this->assertEquals( $expected, array_values( fulcrum_array_sort( $array, function( $v ) { return $v['name']; } ) ) );
//    }

    function test_where() {
        $array = array(
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
            'e' => 5,
            'f' => 6,
            'g' => 7,
            'h' => 8
        );
        $this->assertEquals( array( 'b' => 2, 'd' => 4, 'f' => 6, 'h' => 8 ), Arr::where(
            $array,
            function( $key, $value )
            {
                return $value % 2 === 0;
            }
        ));

        $this->assertEquals(array( 'c' => 3, 'f' => 6 ), fulcrum_array_where(
            $array,
            function( $key, $value )
            {
                return $value % 3 === 0;
            }
        ));
    }

    function test_head() {
        $array = array( 'a', 'b', 'c' );
        $this->assertEquals( 'a', fulcrum_array_head( $array ) );
    }
}

class AccessObjPropStub {
	public $foo;
	public $bar;
	public $baz;
	public function __construct( $foo, $bar, $baz ) {
		$this->foo = $foo;
		$this->bar = $bar;
		$this->baz = $baz;
	}
}