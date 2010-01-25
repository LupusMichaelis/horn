<?php

namespace horn ;

require_once 'horn/lib/test.php' ;

class test_unit_time
	extends test\unit_object
{
	public		function __construct($message = 'Time')
	{
		parent::__construct($message) ;

		$this->providers[] = function () { return new time ; } ;
		$this->providers[] = function () { return date::new_today() ; } ;
		$this->providers[] = function () { return date::new_tomorrow() ; } ;
		$this->providers[] = function () { return date::new_yesterday() ; } ;

	}

	public		function run()
	{
		parent::run() ;
	}

	protected	function _test_today()
	{
		$this->_begin('Testing today') ;

		$today = date::today() ;

		$this->_test_object($today) ;

		$this->_test($today->check()) ;

		$this->_test_equal($today->day, date('d')) ;
		$this->_test_equal($today->month, date('m')) ;
		$this->_test_equal($today->year, date('Y')) ;

		$this->_end() ;
	}

}
