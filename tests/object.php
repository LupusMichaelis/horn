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
	extends test\unit
{
	public		function __construct()
	{
		parent::__construct('Object') ;
	}

	public		function run()
	{
		$this->_test_instanciate() ;
		$this->_test_is_a($this->provides(), 'horn\object_base') ;
		$this->_test_properties($this->provides()) ;
	}

	public		function provides()
	{
		return new thing_public ; 
	}

	protected	function _test_properties(object_base $o)
	{
		$this->_begin('Properties') ;

		try
		{
			$this->_test_getter_undefined($o) ;
			$this->_test_getter_public($o) ;
			$this->_test_getter_protected($o) ;
			$this->_test_getter_private($o) ;

			$this->_test_setter_undefined($o) ;
			$this->_test_setter_public($o) ;
			$this->_test_setter_protected($o) ;
			$this->_test_setter_private($o) ;

			$this->_test_isset_undefined($o) ;
			$this->_test_isset_public($o) ;
			$this->_test_isset_protected($o) ;
			$this->_test_isset_private($o) ;
		}
		catch(\exception $exception)
		{
		}

		$this->_end() ;
	}

	protected	function _test_getter_undefined(object_base $o)
	{
		$this->_begin('Trying to get undefined property.') ;

		try { $o->undefined ; $this->exception_not_thrown() ; }
		catch(exception $e) { $this->_exception_was_expected($e) ; } ;

		$this->_end() ;
	}

	protected	function _test_getter_public(object_base $o)
	{
		$this->_begin('Trying to get public property.') ;

		try { $o->public ; $this->_expected() ; }
		catch(exception $e) { $this->_exception_was_expected($e) ; } ;

		$this->_end() ;
	}

	protected	function _test_getter_protected(object_base $o)
	{
		$this->_begin('Trying to get property protected.') ;

		try { $o->protected ; $this->_expected() ; }
		catch(exception $e) { $this->unexception_expected($e) ; } ;

		$this->_end() ;
	}

	protected	function _test_getter_private(object_base $o)
	{
		$this->_begin('Trying to get property private.') ;

		try { $o->private ; $this->_unexpected() ; }
		catch(exception $e) { $this->_exception_was_expected($e) ; } ;

		$this->_end() ;
	}

	protected	function _test_setter_undefined(object_base $o)
	{
		$this->_begin('Trying to set undefined property.') ;

		try { $o->undefined = 'content' ; $this->exception_not_thrown() ; }
		catch(exception $e) { $this->_exception_was_expected($e) ; } ;

		$this->_end() ;
	}

	protected	function _test_setter_public(object_base $o)
	{
		$this->_begin('Trying to set public property.') ;

		try { $o->public = 'content' ; $this->_expected() ; }
		catch(exception $e) { $this->_exception_was_expected($e) ; } ;

		$this->_end() ;
	}

	protected	function _test_setter_protected(object_base $o)
	{
		$this->_begin('Trying to set property protected.') ;

		try { $o->protected = 'content' ; $this->_expected() ; }
		catch(exception $e) { $this->unexception_expected($e) ; } ;

		$this->_end() ;
	}

	protected	function _test_setter_private(object_base $o)
	{
		$this->_begin('Trying to set property private.') ;

		try { $o->private = 'content' ; $this->_unexpected() ; }
		catch(exception $e) { $this->_exception_was_expected($e) ; } ;

		$this->_end() ;
	}

	protected	function _test_isset_undefined(object_base $o)
	{
		$this->_begin('Isset undefined property.') ;

		try { isset($o->undefined) ; $this->exception_not_thrown() ; }
		catch(exception $e) { $this->_exception_was_expected($e) ; } ;

		$this->_end() ;
	}

	protected	function _test_isset_public(object_base $o)
	{
		$this->_begin('Isset public property.') ;

		try { isset($o->public) ; $this->_expected() ; }
		catch(exception $e) { $this->_exception_was_expected($e) ; } ;

		$this->_end() ;
	}

	protected	function _test_isset_protected(object_base $o)
	{
		$this->_begin('Isset property protected.') ;

		try { isset($o->protected) ; $this->_expected() ; }
		catch(exception $e) { $this->unexception_expected($e) ; } ;

		$this->_end() ;
	}

	protected	function _test_isset_private(object_base $o)
	{
		$this->_begin('Isset property private.') ;

		try { isset($o->private) ; $this->_unexpected() ; }
		catch(exception $e) { $this->_exception_was_expected($e) ; } ;

		$this->_end() ;
	}
}

