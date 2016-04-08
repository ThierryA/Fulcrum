<?php namespace Fulcrum\Tests\Helpers;

use WP_UnitTestCase;
use ArrayAccess;
use StdClass;

class Helpers_Test extends WP_UnitTestCase {

    function test_class_basename() {
        $this->assertEquals( 'Baz', fulcrum_class_basename( 'Foo\Bar\Baz' ) );
        $this->assertEquals( 'Baz', fulcrum_class_basename( 'Baz' ) );
    }

    function test_object_get() {
        $class = new StdClass;
        $class->name = new StdClass;
        $class->name->first = 'Tonya';

        $this->assertEquals( 'Tonya', fulcrum_object_get( $class, 'name.first' ) );
    }

    function test_value() {
        $this->assertEquals( 'foo', fulcrum_value( 'foo' ) );
        $this->assertEquals( 'foo', fulcrum_value( function() { return 'foo'; } ) );
    }

    public function test_data_get() {
        $object = (object) array( 'users' =>  array( 'name' => array( 'Joe', 'TESTER' ) ) );
        $array = array(
            (object) array(
                'users' => array(
                 (object) array( 'name' => 'Joe' )
                )
            )
        );
        $array_access = new Array_Access_Stub( array( 'price' => 56, 'user' => new Array_Access_Stub( array( 'name' => 'Mike' ) ) ) );

        $this->assertEquals( 'Joe', fulcrum_data_get( $object, 'users.name.0' ) );
        $this->assertEquals( 'Joe', fulcrum_data_get( $array, '0.users.0.name' ) );
        $this->assertNull( fulcrum_data_get( $array, '0.users.3' ) );
        $this->assertEquals( 'Not found', fulcrum_data_get( $array, '0.users.3', 'Not found'));
        $this->assertEquals( 'Not found', fulcrum_data_get( $array, '0.users.3', function (){ return 'Not found'; } ) );
        $this->assertEquals( 56, fulcrum_data_get( $array_access, 'price' ) );
        $this->assertEquals( 'Mike', fulcrum_data_get( $array_access, 'user.name' ) );
        $this->assertEquals( 'void', fulcrum_data_get( $array_access, 'foo', 'void' ) );
        $this->assertEquals( 'void', fulcrum_data_get( $array_access, 'user.foo', 'void' ) );
        $this->assertNull( fulcrum_data_get( $array_access, 'foo' ) );
        $this->assertNull( fulcrum_data_get( $array_access, 'user.foo' ) );
    }

    function test_post_id_exists() {

    }

    function test_user_exists() {
        
    }
}

class Array_Access_Stub implements ArrayAccess {
    protected $attributes = array();

    public function __construct( $attributes = array() ) {
        $this->attributes = $attributes;
    }
    public function offsetExists($offset){ return isset($this->attributes[$offset]); }
    public function offsetGet($offset){ return $this->attributes[$offset]; }
    public function offsetSet($offset, $value){ $this->attributes[$offset] = $value; }
    public function offsetUnset($offset){ unset($this->attributes[$offset]); }
}