<?php

namespace tests ;
use horn\lib as h ;
use horn\lib\test as t ;

h\import('lib/test') ;
//h\import('lib/date') ;
h\import('lib/time') ;

class test_suite_time
	extends t\suite_object
{
	public		function __construct($message = 'Time')
	{
		parent::__construct($message) ;

		//$this->providers[] = function () { return new h\time ; } ;
		$this->providers[] = function () { return h\today() ; } ;
		$this->providers[] = function () { return h\tomorrow() ; } ;
		$this->providers[] = function () { return h\yesterday() ; } ;

	}

	protected	function _test_today()
	{
		$messages = array('Testing today') ;
		$suite = $this ;
		$o = $this->target ;
		$callback = function () use ($o, $suite)
			{
				$today = h\today() ;
				$suite->assert($today->check()) ;
				$suite->_assert_equals($today->day, \date('d')) ;
				$suite->_assert_equals($today->month, \date('m')) ;
				$suite->_assert_equals($today->year, \date('Y')) ;
				$suite->_assert_equals($today->timestamp, \strtotime(\date('m/d/Y'))) ;
				return $today->check() ;
			} ;
		$this->add_test($callback, $messages) ;
	}

}
