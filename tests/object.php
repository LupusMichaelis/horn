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
	public		$instance = null ;

	protected	function run()
	{
		$this->test_instanciate() ;
		$this->test_is_a($this->instance, 'horn\object_base') ;
		$this->test_properties($this->instance) ;
	}

	public		function provides()
	{
		return new thing_public ; 
	}

	protected	function test_properties(object_base $o)
	{
		$this->begin_case('Properties') ;

		$this->test_getter_undefined($o) ;
		$this->test_getter_public($o) ;
		$this->test_getter_protected($o) ;
		$this->test_getter_private($o) ;

		$this->test_setter_undefined($o) ;
		$this->test_setter_public($o) ;
		$this->test_setter_protected($o) ;
		$this->test_setter_private($o) ;

		$this->test_isset_undefined($o) ;
		$this->test_isset_public($o) ;
		$this->test_isset_protected($o) ;
		$this->test_isset_private($o) ;

		$this->end_case() ;
	}

	protected	function test_getter_undefined(object_base $o)
	{
		$this->begin_case('Trying to get undefined property.') ;

		try { $o->undefined ; $this->exception_not_thrown() ; }
		catch(exception $e) { $this->exception_was_expected($e) ; } ;

		$this->end_case() ;
	}

	protected	function test_getter_public(object_base $o)
	{
		$this->begin_case('Trying to get public property.') ;

		try { $o->public ; $this->expected() ; }
		catch(exception $e) { $this->exception_was_expected($e) ; } ;

		$this->end_case() ;
	}

	protected	function test_getter_protected(object_base $o)
	{
		$this->begin_case('Trying to get property protected.') ;

		try { $o->protected ; $this->expected() ; }
		catch(exception $e) { $this->unexception_expected($e) ; } ;

		$this->end_case() ;
	}

	protected	function test_getter_private(object_base $o)
	{
		$this->begin_case('Trying to get property private.') ;

		try { $o->private ; $this->unexpected() ; }
		catch(exception $e) { $this->exception_was_expected($e) ; } ;

		$this->end_case() ;
	}

	protected	function test_setter_undefined(object_base $o)
	{
		$this->begin_case('Trying to set undefined property.') ;

		try { $o->undefined = 'content' ; $this->exception_not_thrown() ; }
		catch(exception $e) { $this->exception_was_expected($e) ; } ;

		$this->end_case() ;
	}

	protected	function test_setter_public(object_base $o)
	{
		$this->begin_case('Trying to set public property.') ;

		try { $o->public = 'content' ; $this->expected() ; }
		catch(exception $e) { $this->exception_was_expected($e) ; } ;

		$this->end_case() ;
	}

	protected	function test_setter_protected(object_base $o)
	{
		$this->begin_case('Trying to set property protected.') ;

		try { $o->protected = 'content' ; $this->expected() ; }
		catch(exception $e) { $this->unexception_expected($e) ; } ;

		$this->end_case() ;
	}

	protected	function test_setter_private(object_base $o)
	{
		$this->begin_case('Trying to set property private.') ;

		try { $o->private = 'content' ; $this->unexpected() ; }
		catch(exception $e) { $this->exception_was_expected($e) ; } ;

		$this->end_case() ;
	}

	protected	function test_isset_undefined(object_base $o)
	{
		$this->begin_case('Isset undefined property.') ;

		try { isset($o->undefined) ; $this->exception_not_thrown() ; }
		catch(exception $e) { $this->exception_was_expected($e) ; } ;

		$this->end_case() ;
	}

	protected	function test_isset_public(object_base $o)
	{
		$this->begin_case('Isset public property.') ;

		try { isset($o->public) ; $this->expected() ; }
		catch(exception $e) { $this->exception_was_expected($e) ; } ;

		$this->end_case() ;
	}

	protected	function test_isset_protected(object_base $o)
	{
		$this->begin_case('Isset property protected.') ;

		try { isset($o->protected) ; $this->expected() ; }
		catch(exception $e) { $this->unexception_expected($e) ; } ;

		$this->end_case() ;
	}

	protected	function test_isset_private(object_base $o)
	{
		$this->begin_case('Isset property private.') ;

		try { isset($o->private) ; $this->unexpected() ; }
		catch(exception $e) { $this->exception_was_expected($e) ; } ;

		$this->end_case() ;
	}
}

