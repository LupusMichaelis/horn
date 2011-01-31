<?php

namespace tests ;
use horn\lib as h ;
use horn\lib\test as t ;

require_once 'horn/lib/collection.php' ;
require_once 'horn/lib/test.php' ;

class test_suite_collection
	extends t\suite_object
{
	public		$instance = null ;

	public		function __construct()
	{
		parent::__construct('Object') ;

		$this->providers[] = function () { return new h\collection ; } ;
	}

	public		function run()
	{
		foreach($this->providers as $provider)
			$this->_test_array($provider()) ;
	}

	protected	function _test_array(h\collection $o)
	{
		$this->_test_stack($o) ;
		$this->_test_equal($o->count(), 0) ;
		$this->_test_add($o) ;
	}

	protected	function _test_stack(h\collection $o)
	{
		$this->_test_stack_getter($o) ;
		$this->_test_stack_setter($o) ;
	}

	protected	function _test_stack_getter(h\collection $o)
	{
		$messages = array('Trying to get readonly stack.') ;
		$expected_exception = '\horn\lib\exception' ;
		$callback = function () use ($o)
			{ $o->stack ; return true ; } ;
		//$callback() ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_stack_setter(h\collection $o)
	{
		$messages = array('Trying to set readonly stack.') ;
		$expected_exception = '\horn\lib\exception' ;
		$callback = function () use ($o)
			{ $o->stack = array() ; return false ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}

	protected	function _test_add(h\collection $o)
	{
		$messages = array('Push an element to a h\collection.') ;
		$callback = function () use ($o)
			{
				$o[] = 'toto' ;
				return $o->count() === 1 ;
				//$this->_test_equal($o->count(), 1) ;
			} ;
		$this->add_test($callback, $messages) ;
		
		/*
		
		$messages = array('Add an element with a numeric index to a h\collection.') ;
		try
		{
			$o[10] = 'toto' ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(\exception $e) { $this->_exception_thrown($e, $expected_exception) ; }
		$this->_end() ;
		
		$this->_test_equal($o->count(), 2) ;
		
		$messages = array('Add an element referenced by a key to a h\collection.') ;
		try {
			$o['key'] = 'toto' ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(\exception $e) { $this->_exception_thrown($e, $expected_exception) ; }
		$this->_end() ;
		
		$this->_test_equal($o->count(), 3) ;
		
		$messages = array('Add an element by push method to a h\collection.') ;
		try
		{
			$o->push('toto') ;
			$this->_exception_not_thrown($expected_exception) ;
		}
		catch(\exception $e) { $this->_exception_thrown($e, $expected_exception) ; }
		$this->_end() ;
		
		$this->_test_equal($o->count(), 4) ;

		*/

	}

	function _test_undefined_offset(h\collection $o)
	{
		$messages = array('Trying to access undefined offset.') ;
		$expected_exception = '\horn\lib\exception' ;

		$callback = function () use ($o)
			{ $v = $o[0] ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;
	}
}


