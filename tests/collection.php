<?php

namespace horn ;

require_once 'horn/lib/collection.php' ;
require_once 'horn/lib/test.php' ;

class test_unit_collection
	extends test\unit_object
{
	public		$instance = null ;

	public		function __construct()
	{
		parent::__construct('Object') ;

		$this->providers[] = function () { return new collection ; } ;
	}

	public		function run()
	{
		foreach($this->providers as $provider)
			$this->_test_array($provider()) ;
	}

	protected	function _test_array(collection $o)
	{
		$this->_test_stack($o) ;
		$this->_test_equal($o->count(), 0) ;
		$this->_test_add($o) ;
	}

	protected	function _test_stack(collection $o)
	{
		$this->_begin('Properties') ;

		$this->_test_stack_getter($o) ;
		$this->_test_stack_setter($o) ;

		$this->_end() ;
	}

	protected	function _test_stack_getter(collection $o)
	{
		$this->_begin('Trying to get readonly stack.') ;

		$expected_exception = null ;

		try { $o->stack ; $this->_exception_not_thrown($expected_exception) ; }
		catch(\exception $e) { $this->_exception_thrown($e, $expected_exception) ; }

		$this->_end() ;
	}

	protected	function _test_stack_setter(collection $o)
	{
		$this->_begin('Trying to set readonly stack.') ;

		$expected_exception = null ;

		try { $o->stack = array() ; $this->_exception_not_thrown($expected_exception) ; }
		catch(\exception $e) { $this->_exception_thrown($e, $expected_exception) ; }

		$this->_end() ;
	}

	protected	function _test_add(collection $o)
	{
		/* None of tests ought to throw an exception */
		$expected_exception = null ;

		$this->_begin('Push an element to a collection.') ;
		try
		{
			$o[] = 'toto' ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(\exception $e) { $this->_exception_thrown($e, $expected_exception) ; }
		$this->_end() ;
		
		$this->_test_equal($o->count(), 1) ;
		
		$this->_begin('Add an element with a numeric index to a collection.') ;
		try
		{
			$o[10] = 'toto' ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(\exception $e) { $this->_exception_thrown($e, $expected_exception) ; }
		$this->_end() ;
		
		$this->_test_equal($o->count(), 2) ;
		
		$this->_begin('Add an element referenced by a key to a collection.') ;
		try {
			$o['key'] = 'toto' ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(\exception $e) { $this->_exception_thrown($e, $expected_exception) ; }
		$this->_end() ;
		
		$this->_test_equal($o->count(), 3) ;
		
		$this->_begin('Add an element by push method to a collection.') ;
		try
		{
			$o->push('toto') ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(\exception $e) { $this->_exception_thrown($e, $expected_exception) ; }
		$this->_end() ;
		
		$this->_test_equal($o->count(), 4) ;

	}

}


