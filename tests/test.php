<?php

namespace horn ;

require_once 'horn/lib/horn.php' ;
require_once 'horn/lib/test.php' ;

class test_unit_test
	extends test\unit_object
{
	public		function __construct($message = 'Test')
	{
		parent::__construct($message) ;
	}

	public		function run()
	{
		$this->_test(true) ;

		$this->_test_equal(1, 1) ;
		$this->_test_is_scalar(1) ;
		$this->_test_is_integer(1) ;

		$this->_test_equal(1., 1.) ;
#		$this->_test_equal(0.1 * 0.1, .01) ;
		$this->_test_is_scalar(1.) ;
		$this->_test_is_float(1.) ;

		$this->_test_is_scalar(true) ;
		$this->_test_is_scalar(false) ;
		$this->_test_is_null(null) ;
		$this->_test_class_exists('\stdclass') ;
		$this->_test_is_object(new \stdclass) ;
		$this->_test_is_a(new \stdclass, '\stdclass') ;
	}

	/** This test case checks its own behaviour, so it doesn't have to provide any thing.
	 *	\todo	Think about a separation of concerns between general and object oriented behaviour test case.
	 */
	public		function provides()
	{
		return null ;
	}

}


