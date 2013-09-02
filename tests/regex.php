<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/regex');
h\import('lib/test');

class test_suite_regex
	extends t\suite
{
	public		function __construct($message = 'Regex')
	{
		parent::__construct($message);

		$this->providers[] = function()
		{
			return array('.*', 'This is up to 3verything <3 !!!11', true);
		};

		$this->providers[] = function()
		{
			return array('^This', 'This is up to 3verything <3 !!!11', true);
		};

		$this->providers[] = function()
		{
			return array('^This$', 'This is up to 3verything <3 !!!11', false);
		};
	}

	protected	function _test_match()
	{

		$messages = array('Regex search');
		$expected_exception = null;

		list ($re, $subject, $expected_exit) = $this->target;
		$callback = function () use ($re, $subject, $expected_exit)
		{
			$re = h\regex($re);
			$subject = h\string($subject);
			$result = $re->do_execute($subject);
			return $expected_exit === $result->is_match() ;
		};
		$this->add_test($callback, $messages, $expected_exception);
	}
}

