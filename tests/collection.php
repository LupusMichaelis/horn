<?php

namespace horn ;

require_once 'horn/lib/collection.php' ;
require_once 'horn/lib/test.php' ;

require_once 'tests/object.php' ;


class test_unit_collection
	extends test\unit
{
	public		$instance = null ;

	protected	function provides()
	{
		return new collection ;
	}

	protected	function run()
	{
		$this->test_instantiation() ;
		$this->test_array($this->instance) ;
	}

	protected	function test_array(collection $o)
	{
		$this->test_access_stack($o) ;
		$this->test_equal($o->count(), 0) ;
		$this->test_add($o) ;
	}

	protected	function test_access_stack(collection $o)
	{
		try { $o->stack ; $this->unexpected() ; }
		catch(\exception $e) { $this->exception_expected($e) ; }
	}

	protected	function test_add(collection $o)
	{
		$this->begin_case('Add an element to a collection.') ;

		try
		{
			$o[] = 'toto' ; $this->expected() ;
			$o[10] = 'toto' ; $this->expected() ;
			$o['key'] = 'toto' ; $this->expected() ;
		}
		catch(\exception $e)
		{
			$this->exception_unexpected($e) ;
		}

		$this->test_equal($o->count(), 3) ;

		$this->end_case() ;
	}

}


