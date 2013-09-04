<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/object/wrapper');
h\import('lib/test');
h\import('tests/object');

// Test class
class thing_wrapped
	extends h\object\public_
{
	public		$public;
	protected	$_protected;
	private		$_private;

	public		function __construct()
	{
		$this->_object = new h\object_public;
		parent::__construct();
	}

	protected	function _isset_virtual()
	{
		return isset($this->_private);
	}

	protected	function _set_virtual($new_value)
	{
		$this->_private = $new_value;
	}

	protected	function &_get_virtual()
	{
		return $this->_private;
	}

	protected	$_object;
}

class test_suite_wrapper
	extends test_suite_object
{
	protected	$_instance = null;

	public		function __construct($message = 'Object Wrapper')
	{
		parent::__construct($message);

		$this->providers->reset();
		$this->providers[] = function ()
		{
			$wrapped = new thing_wrapped ;
			$wrapper = new h\object\wrapper;
			$wrapper->set_impl($wrapped);
			return $wrapper;
		};
	}

}

