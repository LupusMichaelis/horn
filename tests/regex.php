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
		$this->providers[] = function() { return null; };
	}

	protected	function _test_match()
	{
		$messages = array('Regex search');
		$expected_exception = null;

		foreach(array
				( array('.*', 'This is up to 3verything <3 !!!11', true)
				, array('^This', 'This is up to 3verything <3 !!!11', true)
				, array('^This$', 'This is up to 3verything <3 !!!11', false)
				) as $target)
		{
			$callback = function () use ($target)
			{
				list ($re, $subject, $expected_exit) = $target;
				$re = h\regex($re);
				$subject = h\text($subject);
				$result = $re->do_execute($subject);
				return $expected_exit === $result->is_match() ;
			};
			$this->add_test($callback, $messages, $expected_exception);
		}
	}

	protected	function _test_match_capture()
	{
		$messages = array('Regex capture');
		$expected_exception = null;

		foreach(array
				( array('^(?:(\w+)\s?)*$', 'Bonjour le monde', true, 1, 'monde')
				) as $target)
		{

			$callback = function () use ($target)
			{
				list( $re
					, $subject
					, $expected_matching
					, $expected_capture_index
					, $expected_capture
					) = $target;

				$re = h\regex($re);
				$subject = h\text($subject);
				$result = $re->do_execute($subject);

				return $expected_matching === $result->is_match()
					&& $result->has_captured(0)
					&& $result->has_captured($expected_capture_index)
					&& $subject == $subject->slice
							( $result->iterate_matches()[0]->begin
							, $result->iterate_matches()[0]->end
							)
					&& $expected_capture == $subject->slice
							( $result->iterate_captures_by_index($expected_capture_index)[0]->begin
							, $result->iterate_captures_by_index($expected_capture_index)[0]->end
							)
					;
			};
			$this->add_test($callback, $messages, $expected_exception);
		}
	}
}

