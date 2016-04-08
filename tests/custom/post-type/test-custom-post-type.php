<?php namespace Fulcrum\Tests\Custom\Post_Type;

use WP_UnitTestCase;
use Fulcrum\Tests\Mocks\Empty_Config;
use Fulcrum\Config\Factory;
use Fulcrum\Custom\Post_Type\Post_Type;

include_once FULCRUM_MOCKS_DIR . 'mock-empty-config.php';

class Post_Type_Test extends WP_UnitTestCase {

	protected $post_type = 'foo';
	protected $cpt;
	protected $config;

	function setUp() {
		parent::setUp();

		$this->config = Factory::create( FULCRUM_CONFIGS_DIR . 'cpt-foo.php' );
		$this->cpt    = new Post_Type( $this->config, $this->post_type );

		$this->cpt->register();
	}

	function tearDown() {
		parent::tearDown();

		_unregister_post_type( $this->post_type );
	}

	function test_exception_thrown_for_no_post_type() {
		$post_type = '';
		$this->setExpectedException( 'InvalidArgumentException', 'For Custom Post Type Configuration, the Post type cannot be empty' );
		new Post_Type( new Empty_Config, $post_type );
	}

	function test_exception_thrown_for_invalid_config() {
		$post_type = 'foo';
		$this->setExpectedException( 'InvalidArgumentException', 'For Custom Post Type Configuration, the config for [foo] cannot be empty.' );
		new Post_Type( new Empty_Config, $post_type );

		$this->assertFalse( post_type_exists( 'foo' ) );
	}

	function test_registering() {
		$this->assertTrue( post_type_exists( $this->post_type ) );
		$post_obj = get_post_type_object( $this->post_type );
		$this->assertInstanceOf( 'stdClass', $post_obj );
	}

	function test_cpt_default_labels() {
		$post_obj = get_post_type_object( $this->post_type );
		$this->assertInstanceOf( 'stdClass', $post_obj );
		$this->assertEquals( $this->post_type, $post_obj->name );
		$this->assertEquals( 'Foo', $post_obj->label );
		$this->assertEquals( 'Foo', $post_obj->labels->name );
		$this->assertEquals( 'Add New Foo', $post_obj->labels->add_new_item );
	}

	function test_unregistering_cpt() {

		$post_obj = get_post_type_object( $this->post_type );
		$this->assertInstanceOf( 'stdClass', $post_obj );

		$this->cpt->__destruct();

		$this->assertNull( get_post_type_object( $this->post_type ) );
		$this->assertEmpty( get_post_types( array( '_builtin' => false ) ) );
		$this->assertFalse( post_type_exists( $this->post_type ) );
	}

	function test_columns_filter() {

		$actual         = $this->cpt->columns_filter( array() );
		$expected       = $this->config['columns_filter'];
		$expected['cb'] = '<input type="checkbox" />';

		$this->assertSame( $actual, $expected );
	}

	function test_columns_data_throws_error() {
		$this->setExpectedException(
			'WPDevsClub_Core\Support\Exceptions\Configuration_Exception',
			'The callback [invalid_function_name], for the custom post type [foo], was not found, as call_user_func_array() expects a valid callback function/method.' );
		$this->cpt->config['columns_data']['date'] = array(
			'callback' => 'invalid_function_name',
			'echo'     => false,
			'args'     => array( 5 ),
		);

		$this->cpt->columns_data( 'date', 5 );
	}

	function test_columns_data_and_callback() {
		$this->cpt->config['columns_data'] = array(
			'col1' => array(
				'callback'      => array( $this, 'callback_test_columns_data_and_callback' ),
				'echo'          => true,
				'args'          => array( 5, 'somedata' ),
			),
		);

		ob_start();
		$this->cpt->columns_data( 'col1', 5 );
		$actual = ob_get_clean();

		$this->assertSame( '5, somedata', $actual );
	}

	public function callback_test_columns_data_and_callback( $post_id, $data ) {
		return $post_id . ', ' . $data;
	}

	function test_add_to_feed() {
		$qv_mock = array(
			'feed'      => '',
			'post_type' => get_post_types(),
		);
		$this->config['add_feed'] = true;

		$qv = $this->cpt->add_or_remove_to_from_rss_feed( $qv_mock );
		$this->assertTrue( in_array( 'foo' , $qv['post_type'] ) );
	}

	function test_add_to_feed_when_no_post_types_in_feed() {
		$this->config->add_feed = true;

		$qv_mock = array(
			'feed'      => '',
			'post_type' => '',
		);
		$qv = $this->cpt->add_or_remove_to_from_rss_feed( $qv_mock );
		$this->assertTrue( in_array( 'foo' , $qv['post_type'] ) );
	}

	function test_remove_from_feed() {
		$qv_mock = array(
			'feed'      => '',
			'post_type' => get_post_types(),
		);

		$this->config->add_feed = false;
		$qv = $this->cpt->add_or_remove_to_from_rss_feed( $qv_mock );
		$this->assertFalse( in_array( 'foo' , $qv['post_type'] ) );
	}
}
