<?php

namespace horn ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/test.php' ;

// Test class
class thing_public
	extends object_public
{
	public		$public ;
	protected	$_protected ;
	private		$_private ;
}

class test_unit_object
	extends test\unit_object
{
	protected	$_instance = null ;

	public		function __construct($message = 'Object')
	{
		parent::__construct($message) ;
		
		$this->providers[] = function () { return new thing_public ; } ;
	}

	public		function run()
	{
		parent::run() ;
		foreach($this->providers as $provider)
			$this->_test_properties($provider()) ;
	}

	protected	function _test_properties(object_base $o)
	{
		$this->_begin('Properties') ;

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
			) ;

		$exception_expected = null ;
		foreach($methods as $test_method)
			try
			{
				$this->{"_test_$test_method"}($o) ;
				$this->_exception_not_thrown($exception_expected) ;
			}
			catch(\exception $exception)
			{
				$this->_exception_thrown($exception_expected) ;
			}

		$this->_end() ;
	}

	protected	function _test_getter_undefined(object_base $o)
	{
		$this->_begin('Trying to get undefined property.') ;

		$expected_exception = '\exception' ;

		try { $o->undefined ; $this->_exception_not_thrown($expected_exception) ; }
		catch(exception $e) { $this->_exception_thrown($e, $expected_exception) ; } ;

		$this->_end() ;
	}

	protected	function _test_getter_public(object_base $o)
	{
		$this->_begin('Trying to get public property.') ;

		$expected_exception = null ;

		try { $o->public ; $this->_exception_not_thrown($expected_exception) ; }
		catch(exception $e) { $this->_exception_thrown($e, $expected_exception) ; } ;

		$this->_end() ;
	}

	protected	function _test_getter_protected(object_base $o)
	{
		$this->_begin('Trying to get property protected.') ;

		$expected_exception = '\exception' ;

		try { $o->protected ; $this->_exception_not_thrown($expected_exception) ; }
		catch(exception $e) { $this->_exception_thrown($e, $expected_exception) ; } ;

		$this->_end() ;
	}

	protected	function _test_getter_private(object_base $o)
	{
		$this->_begin('Trying to get property private.') ;

		$expected_exception = '\exception' ;

		try
		{
			$o->private ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(exception $e) { $this->_exception_thrown($e, $expected_exception) ; } ;

		$this->_end() ;
	}

	protected	function _test_setter_undefined(object_base $o)
	{
		$this->_begin('Trying to set undefined property.') ;

		$expected_exception = '\exception' ;

		try
		{
			$o->undefined = 'content' ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(exception $e) { $this->_exception_thrown($e, $expected_exception) ; } ;

		$this->_end() ;
	}

	protected	function _test_setter_public(object_base $o)
	{
		$this->_begin('Trying to set public property.') ;

		$expected_exception = '\exception' ;

		try
		{
			$o->public = 'content' ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(exception $e) { $this->_exception_thrown($e, $expected_exception) ; } ;

		$this->_end() ;
	}

	protected	function _test_setter_protected(object_base $o)
	{
		$this->_begin('Trying to set property protected.') ;

		$expected_exception = '\exception' ;

		try { $o->protected = 'content' ; $this->_exception_not_thrown($expected_exception) ; }
		catch(exception $e) { $this->_exception_thrown($e, $expected_exception) ; } ;

		$this->_end() ;
	}

	protected	function _test_setter_private(object_base $o)
	{
		$this->_begin('Trying to set property private.') ;

		$expected_exception = '\exception' ;

		try
		{
			$o->private = 'content' ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(exception $e) { $this->_exception_thrown($e, $expected_exception) ; } ;

		$this->_end() ;
	}

	protected	function _test_isset_undefined(object_base $o)
	{
		$this->_begin('Isset undefined property.') ;

		$expected_exception = null ;

		try
		{
			isset($o->undefined) ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(exception $e) { $this->_exception_thrown($e, $expected_exception) ; } ;

		$this->_end() ;
	}

	protected	function _test_isset_public(object_base $o)
	{
		$this->_begin('Isset public property.') ;

		$expected_exception = null ;

		try
		{
			isset($o->public) ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(exception $e) { $this->_exception_thrown($e, $expected_exception) ; } ;

		$this->_end() ;
	}

	protected	function _test_isset_protected(object_base $o)
	{
		$this->_begin('Isset property protected.') ;

		$expected_exception = null ;

		try
		{
			isset($o->protected) ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(exception $e) { $this->_exception_thrown($e, $expected_exception) ; } ;

		$this->_end() ;
	}

	protected	function _test_isset_private(object_base $o)
	{
		$this->_begin('Isset property private.') ;

		$expected_exception = null ;

		try
		{
			isset($o->private) ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(exception $e) { $this->_exception_thrown($e, $expected_exception) ; } ;

		$this->_end() ;
	}
}

