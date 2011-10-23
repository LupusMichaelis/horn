<?php

namespace tests ;
use horn\lib as h ;
use horn\lib\test as t ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/test.php' ;

// Test class
class thing_public
	extends h\object_public
{
	public		$public ;
	protected	$_protected ;
	private		$_private ;

	public		function __construct()
	{
		$this->_object = new h\object_public ;
		parent::__construct() ;
	}

	protected	function _isset_virtual()
	{
		return isset($this->_private) ;
	}

	protected	function _set_virtual($new_value)
	{
		$this->_private = $new_value ;
	}

	protected	function &_get_virtual()
	{
		return $this->_private ;
	}

	protected	$_object ;
}

class thing_of
	extends thing_public
{
}


class test_suite_object
	extends t\suite_object
{
	protected	$_instance = null ;

	public		function __construct($message = 'Object')
	{
		parent::__construct($message) ;
		
		$this->providers[] = function () { return new thing_public ; } ;
		$this->providers[] = function () { return new thing_of ; } ;
	}

	protected	function _test_properties()
	{
		#$this->_begin('Properties') ;

		$methods = array
			( 'getter_undefined', 'setter_undefined', 'isset_undefined'
			, 'getter_public', 'setter_public', 'isset_public'
			, 'getter_protected', 'setter_protected', 'isset_protected'
			, 'getter_private', 'setter_private', 'isset_private'
			, 'getter_virtual', 'setter_virtual', 'isset_virtual'
			, 'assign_object'
			) ;

		$exception_expected = null ;
		foreach($methods as $test_method)
			$this->{"_test_$test_method"}() ;

		//$this->_end() ;
	}

	protected	function _test_getter_undefined()
	{
		$messages = array('Trying to get undefined property.') ;
		$expected_exception = '\horn\lib\exception' ;

		$o = $this->target ;
		$callback = function () use ($o) { return $o->undefined ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_getter_public()
	{
		$messages = array('Trying to get public property.') ;
		$expected_exception = null ;

		$o = $this->target ;
		$callback = function () use ($o) { $o->public ; return true ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_getter_protected()
	{
		$messages = array('Trying to get protected property.') ;
		$expected_exception = null ;

		$o = $this->target ;
		$callback = function () use ($o) { $o->protected ; return true ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_getter_virtual()
	{
		$messages = array('Trying to get virtual property.') ;
		$expected_exception = null ;

		$o = $this->target ;
		$callback = function () use ($o) { $o->virtual ; return true ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_getter_private()
	{
		$messages = array('Trying to get private property.') ;
		$expected_exception = '\horn\exception' ;

		$o = $this->target ;
		$callback = function () use ($o) { return $o->private ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_setter_undefined()
	{
		$messages = array('Trying to set undefined property.') ;
		$expected_exception = '\horn\lib\exception' ;

		$o = $this->target ;
		$callback = function () use ($o) { $o->undefined = 'content' ; return false ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_setter_public()
	{
		$messages = array('Trying to set public property.') ;
		$expected_exception = null ;

		$o = $this->target ;
		$callback = function () use ($o) { return $o->public = 'content' ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_setter_protected()
	{
		$messages = array('Trying to set protected property.') ;
		$expected_exception = '\horn\exception' ;

		$o = $this->target ;
		$callback = function () use ($o) { return $o->property = 'content' ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_setter_virtual()
	{
		$messages = array('Trying to set virtual property.') ;
		$expected_exception = null ;

		$o = $this->target ;
		$callback = function () use ($o) { return $o->virtual = 'content' ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_setter_private()
	{
		$messages = array('Trying to set private property.') ;
		$expected_exception = '\horn\exception' ;

		$o = $this->target ;
		$callback = function () use ($o) { return $o->private = 'content' ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_isset_undefined()
	{
		$messages = array('Trying to test undefined property') ;
		$expected_exception = null ;

		$o = $this->target ;
		$callback = function () use ($o) { return !isset($o->undefined) ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_isset_public()
	{
		$messages = array('Trying to test public property') ;
		$expected_exception = null ;

		$o = $this->target ;
		$callback = function () use ($o) { return isset($o->public) ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_isset_protected()
	{
		$messages = array('Trying to test protected property') ;
		$expected_exception = null ;

		$o = $this->target ;
		$callback = function () use ($o) { return !isset($o->protected) ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_isset_virtual()
	{
		$messages = array('Trying to test virtual property') ;
		$expected_exception = null ;

		$o = $this->target ;
		$callback = function () use ($o) { return isset($o->virtual) ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_isset_private()
	{
		$messages = array('Trying to test private property') ;
		$expected_exception = null ;

		$o = $this->target ;
		$callback = function () use ($o) { return !isset($o->private) ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_assign_object()
	{
		$messages = array('Assign value to typed attribute') ;

		$o = $this->target ;
		$callback = function () use ($o)
			{
				$o->object = new h\object_public ;
				return true ;
			} ;
		$this->add_test($callback, $messages) ;
	}
}

