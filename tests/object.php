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

	public		function run()
	{
		parent::run() ;
		foreach($this->providers as $provider)
			$this->_test_properties($provider()) ;
	}

	protected	function _test_properties(h\object_base $o)
	{
		#$this->_begin('Properties') ;

		$methods = array
			( 'getter_undefined'
			, 'getter_public'
			, 'getter_protected'
			, 'getter_private'

			, 'setter_undefined'
			, 'setter_public'
			, 'setter_protected'
			, 'setter_private'

			, 'isset_undefined'
			, 'isset_public'
			, 'isset_protected'
			, 'isset_private'

			, 'assign_object'
			) ;

		$exception_expected = null ;
		foreach($methods as $test_method)
			$this->{"_test_$test_method"}($o) ;

		//$this->_end() ;
	}

	protected	function _test_getter_undefined(h\object_base $o)
	{
		$messages = array('Trying to get undefined property.') ;
		$expected_exception = '\horn\lib\exception' ;
		$callback = function () use ($o) { return $o->undefined ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_getter_public(h\object_base $o)
	{
		$messages = array('Trying to get public property.') ;
		$expected_exception = null ;
		$callback = function () use ($o) { $o->public ; return true ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_getter_protected(h\object_base $o)
	{
		$messages = array('Trying to get protected property.') ;
		$expected_exception = null ;
		$callback = function () use ($o) { $o->protected ; return true ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_getter_private(h\object_base $o)
	{
		$messages = array('Trying to get private property.') ;
		$expected_exception = '\horn\exception' ;
		$callback = function () use ($o) { return $o->private ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_setter_undefined(h\object_base $o)
	{
		$messages = array('Trying to set undefined property.') ;
		$expected_exception = '\horn\lib\exception' ;
		$callback = function () use ($o) { $o->undefined = 'content' ; return false ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_setter_public(h\object_base $o)
	{
		$messages = array('Trying to set public property.') ;
		$expected_exception = null ;
		$callback = function () use ($o) { return $o->public = 'content' ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_setter_protected(h\object_base $o)
	{
		$messages = array('Trying to set protected property.') ;
		$expected_exception = '\horn\exception' ;
		$callback = function () use ($o) { return $o->property = 'content' ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_setter_private(h\object_base $o)
	{
		$messages = array('Trying to set private property.') ;
		$expected_exception = '\horn\exception' ;
		$callback = function () use ($o) { return $o->private = 'content' ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_isset_undefined(h\object_base $o)
	{
		$messages = array('Trying to test undefined property') ;
		$expected_exception = null ;
		$callback = function () use ($o) { return !isset($o->undefined) ; } ;
		assert($callback()) ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_isset_public(h\object_base $o)
	{
		$messages = array('Trying to test public property') ;
		$expected_exception = null ;
		$callback = function () use ($o) { return isset($o->public) ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_isset_protected(h\object_base $o)
	{
		$messages = array('Trying to test protected property') ;
		$expected_exception = null ;
		$callback = function () use ($o) { return !isset($o->protected) ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_isset_private(h\object_base $o)
	{
		$messages = array('Trying to test private property') ;
		$expected_exception = null ;
		$callback = function () use ($o) { return !isset($o->private) ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_assign_object(h\object_base $o)
	{
		$messages = array('Assign value to typed attribute') ;
		$callback = function () use ($o)
			{
				$o->object = new h\object_public ;
				return true ;
			} ;
		$this->add_test($callback, $messages) ;
	}
}

