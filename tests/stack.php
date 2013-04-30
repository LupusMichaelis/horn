<?php

namespace tests ;
use horn\lib as h ;
use horn\lib\test as t ;

h\import('lib/stack') ;
h\import('lib/test') ;

class test_suite_stack
	extends t\suite_object
{
	public		$instance = null ;

	public		function __construct()
	{
		parent::__construct('Object') ;

		$this->providers[] = function () { return new h\stack ; } ;
	}

	protected	function _test_stack()
	{
		$this->_assert_equals(0, $this->target->count()) ;
	}

	protected	function _test_add()
	{
		$messages = array('Push an element to a h\stack.') ;
		$o = $this->target ;
		$callback = function () use ($o)
			{
				$o[] = 'toto' ;
				return $o->count() === 1 ;
				//$this->_test_equal($o->count(), 1) ;
			} ;
		$this->add_test($callback, $messages) ;
	}

	protected	function _test_set_integer_key()
	{
		$messages = array('Push an element (0, \'toto\') to a h\stack.') ;
		$o = $this->target ;
		$callback = function () use ($o)
			{
				$o[0] = 'toto' ;
				return $o->count() === 1 ;
				//$this->_test_equal($o->count(), 1) ;
			} ;
		$this->add_test($callback, $messages) ;
	}

	protected	function _test_set_integer_key_up()
	{
		$messages = array('Push an element (5, \'toto\') to a h\stack.') ;
		$o = $this->target ;
		$callback = function () use ($o)
			{
				$o[5] = 'toto' ;
				return $o->count() === 6 ;
				//$this->_test_equal($o->count(), 1) ;
			} ;
		$this->add_test($callback, $messages) ;
	}
}


