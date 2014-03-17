<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/escaper');
h\import('lib/test');

class test_escaper
	extends h\escaper\base
{
	public		function do_escape(h\string $text)
	{
		$text = strtr($text, array('\\' => '\\\\'));
		return h\string($text);
	}

	public		function do_unescape(h\string $text)
	{
		$text = strtr($text, array('\\\\' => '\\'));
		return h\string($text);
	}
}

class test_suite_escaper
	extends t\suite_object
{
	public		function __construct($message = 'Escaper')
	{
		parent::__construct($message);
		$this->providers[] = function() { return new test_escaper(h\string('UTF-8')); };
	}

	protected	function _test_escape()
	{
		$messages = array('Tests escape character in a string');
		$e = $this->target;
		$callback = function () use($e)
			{
				$s = h\string('\\$');
				$es = $e->do_escape($s);
				return h\string('\\\\$')->is_equal($es);
			};
		$this->add_test($callback, $messages);

		$messages = array('Tests unescape character in a string');
		$e = $this->target;
		$callback = function () use($e)
			{
				$s = h\string('\\\\$');
				$ues = $e->do_unescape($s);
				return h\string('\\$')->is_equal($ues);
			};
		$this->add_test($callback, $messages);

	}
}
