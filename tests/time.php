<?php

namespace tests ;
use horn\lib as h ;
use horn\lib\test as t ;

require_once 'horn/lib/test.php' ;
//require_once 'horn/lib/date.php' ;
require_once 'horn/lib/time.php' ;

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
				//$suite->_test_object($today) ;
				return $today->check() ;
				//$suite->_test($today->check()) ;
				//$suite->_test_equal($today->day, \date('d')) ;
				//$suite->_test_equal($today->month, \date('m')) ;
				//$suite->_test_equal($today->year, \date('Y')) ;
			} ;
		$this->add_test($callback, $messages) ;
	}

}
