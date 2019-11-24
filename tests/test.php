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
		$this->providers[] = function () { return null; };
	}

	public		function _test_all()
	{
		$this->assert(true);

		$this->_assert_equals(1, 1);
		$this->_assert_is_scalar(1);
		$this->_assert_is_integer(1);

		$this->_assert_equals(1., 1.);
		$this->_assert_not_equals(0.1 * 0.1, .01);
		$this->_assert_is_scalar(1.);
		$this->_assert_is_float(1.);

		$this->_assert_is_scalar(true);
		$this->_assert_is_scalar(false);
		$this->_assert_is_null(null);
		$this->_assert_class_exists('\stdclass');
		$this->_assert_is_object(new \stdclass);
		$this->_assert_is_a(new \stdclass, '\stdclass');
	}
}
