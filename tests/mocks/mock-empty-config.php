<?php namespace Fulcrum\Tests\Mocks;

use ArrayObject;
use Fulcrum\Config\Config_Contract;

class Empty_Config extends ArrayObject implements Config_Contract {

	public function __construct() {
		$config = array();
		parent::__construct( $config, ArrayObject::ARRAY_AS_PROPS );
	}

	public function has( $key ) {
		return false;
	}

	public function get( $key, $default = null ) {
		return $default;
	}

	public function all() {}

	public function is_array( $key ) {
		return false;
	}

	public function push( $parameter_key, $value ) {}
}