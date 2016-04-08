<?php namespace Fulcrum\Tests\Custom;

use WP_UnitTestCase;
use Fulcrum\Tests\Mocks\Empty_Config;
use Fulcrum\Config\Factory;
use Fulcrum\Custom\Post_Type\Post_Type;
use Fulcrum\Custom\Taxonomy\Taxonomy;

include_once FULCRUM_MOCKS_DIR . 'mock-empty-config.php';

//class Taxonomy_Test extends WP_UnitTestCase {
//
//	protected $post_type = 'foo';
//	protected $cpt;
//	protected $config;
//
//	protected $tax;
//	protected $tax_config;
//
//	function setUp() {
//		parent::setUp();
//
//		_clean_term_filters();
//		wp_cache_delete( 'last_changed', 'terms' );
//
//		$this->config = Factory::create( FULCRUM_CONFIGS_DIR . 'cpt-foo.php' );
//		$this->cpt    = new Post_Type( $this->config, $this->post_type );
//		$this->cpt->register();
//
//		$this->tax_config = Factory::create( FULCRUM_CONFIGS_DIR . 'tax-foo.php' );
//	}
//
//	function tearDown() {
//		parent::tearDown();
//
//		_unregister_post_type( $this->post_type );
//		_unregister_taxonomy( 'foo' );
//	}
//
//	function test_cpt_was_created_first() {
//		$this->assertTrue( post_type_exists( 'foo' ) );
//	}
//
//	function test_exception_thrown_for_no_taxonomy_name() {
//		$name = '';
//		$this->setExpectedException( 'InvalidArgumentException', 'For Custom Taxonomy Configuration, the taxonomy name cannot be empty.' );
//		new Taxonomy( new Empty_Config, $name );
//	}
//
//	function test_exception_thrown_for_invalid_config() {
//		$this->setExpectedException( 'InvalidArgumentException', 'For Custom Taxonomy Configuration, the object_type in config cannot be empty.' );
//		new Taxonomy( new Empty_Config, 'foo' );
//	}
//
//	function test_registering_taxonomy() {
//		$tax    = new Taxonomy( $this->tax_config, $this->post_type );
//		$tax->register();
//
//		$taxonomies = get_taxonomies( array( 'name' => 'foo' ), 'objects' );
//		$obj = $taxonomies['foo'];
//		$this->assertInstanceOf( 'stdClass', $obj );
//		$this->assertEquals( 'foo', $obj->name );
//		$this->assertEquals( 'Foo', $obj->labels->name );
//		$this->assertEquals( 'My Foo', $obj->labels->menu_name );
//		$this->assertEquals( 'foo-category', $obj->rewrite );
//	}
//
//	function test_registering_taxonomy_with_terms() {
//		$tax    = new Taxonomy( $this->tax_config, $this->post_type );
//		$tax->register();
//
//		$bar    = $this->factory->term->create( array( 'name' => 'Bar', 'taxonomy' => 'foo' ) );
//		$baz    = $this->factory->term->create( array( 'name' => 'Baz', 'parent' => $bar, 'taxonomy' => 'foo' ) );
//		$foobar = $this->factory->term->create( array( 'name' => 'FooBar', 'parent' => $bar, 'taxonomy' => 'foo' ) );
//
//		$terms  = get_terms( 'foo', array( 'hide_empty' => false ) );
//		$this->assertNotInstanceOf( 'WP_Error', $terms );
//		$this->assertEquals( array( 'Bar', 'Baz', 'FooBar' ), wp_list_pluck( $terms, 'name' ) );
//	}
//}