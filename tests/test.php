<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/horn');
h\import('lib/test');

class test_suite_test
	extends t\suite
{
	public		function __construct($message = 'Autotest')
	{
		parent::__construct($message);
	}

	public		function _test()
	{
		$this->assert(true);

		$this->_test_equal(1, 1);
		$this->_test_is_scalar(1);
		$this->_test_is_integer(1);

		$this->_test_equal(1., 1.);
#		$this->_test_equal(0.1 * 0.1, .01);
		$this->_test_is_scalar(1.);
		$this->_test_is_float(1.);

		$this->_test_is_scalar(true);
		$this->_test_is_scalar(false);
		$this->_test_is_null(null);
		$this->_test_class_exists('\stdclass');
		$this->_test_is_object(new \stdclass);
		$this->_test_is_a(new \stdclass, '\stdclass');

		// $this->_test_th
	}

	/** This test case checks its own behaviour, so it doesn't have to provide any thing.
	 *	\todo	Think about a separation of concerns between general and object oriented behaviour test case.
	 */
	public		function provides()
	{
		return null;
	}

}


