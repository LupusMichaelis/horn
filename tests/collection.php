<?php

namespace horn ;

require_once 'horn/lib/collection.php' ;
require_once 'horn/lib/test.php' ;

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
		$this->test_stack($o) ;
		$this->test_equal($o->count(), 0) ;
		$this->test_add($o) ;
	}

	protected	function test_stack(collection $o)
	{
		$this->begin_case('Properties') ;

		$this->test_stack_getter($o) ;
		$this->test_stack_setter($o) ;

		$this->end_case() ;
	}

	protected	function test_stack_getter(collection $o)
	{
		$this->begin_case('Trying to get readonly stack.') ;

		try { $o->stack ; $this->unexpected() ; }
		catch(\exception $e) { $this->exception_expected($e) ; }

		$this->end_case() ;
	}

	protected	function test_stack_setter(collection $o)
	{
		$this->begin_case('Trying to set readonly stack.') ;

		try { $o->stack = array() ; $this->unexpected() ; }
		catch(\exception $e) { $this->exception_expected($e) ; }

		$this->end_case() ;
	}

	protected	function test_add(collection $o)
	{
		$this->begin_case('Push an element to a collection.') ;
		try { $o[] = 'toto' ; $this->expected() ; }
		catch(\exception $e) { $this->exception_unexpected($e) ; }
		$this->end_case() ;
		
		$this->test_equal($o->count(), 1) ;
		
		$this->begin_case('Add an element with a numeric index to a collection.') ;
		try { $o[10] = 'toto' ; $this->expected() ; }
		catch(\exception $e) { $this->exception_unexpected($e) ; }
		$this->end_case() ;
		
		$this->test_equal($o->count(), 2) ;
		
		$this->begin_case('Add an element referenced by a key to a collection.') ;
		try { $o['key'] = 'toto' ; $this->expected() ; }
		catch(\exception $e) { $this->exception_unexpected($e) ; }
		$this->end_case() ;
		
		$this->test_equal($o->count(), 3) ;
		
		$this->begin_case('Add an element by push method to a collection.') ;
		try { $o->push('toto') ; $this->expected() ; }
		catch(\exception $e) { $this->exception_unexpected($e) ; }
		$this->end_case() ;
		
		$this->test_equal($o->count(), 4) ;

	}

}


