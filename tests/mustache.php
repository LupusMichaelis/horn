<?php

namespace tests ;
use horn\lib as h ;
use horn\lib\test as t ;

h\import('lib/mustache') ;
h\import('lib/test') ;

class test_suite_mustache
	extends t\suite
{
	public		function __construct($message = 'Mustache')
	{
		parent::__construct($message) ;

		$this->providers[] = function() { return null; } ;
	}

	protected	function _test_parser()
	{
		$messages = array('Tests mustache parser') ;
		$callback = function ()
			{
				$template = h\string('No tag at all!');
				$parsed = h\mustache\parse_mustache($template);
				return 3 === count($parsed);
			} ;
		$this->add_test($callback, $messages) ;

		$messages = array('Tests mustache one variable') ;
		$callback = function ()
			{
				$template = h\string('This is a {{tag}}!');
				$parsed = h\mustache\parse_mustache($template);
				return 5 === count($parsed);
			} ;
		$this->add_test($callback, $messages) ;

		$messages = array('Tests mustache one section.') ;
		$callback = function ()
			{
				$template = h\string('This is a {{#section}}Ok?{{/section}}');
				$parsed = h\mustache\parse_mustache($template);
				return 7 === count($parsed);
			} ;
		$this->add_test($callback, $messages) ;
	}

	protected	function _test_render()
	{
		$messages = array('Tests mustache render filled variable') ;
		$template = h\string('This is a \'{{variable}}\' with a {{#variable}}section{{/variable}}');
		$parsed = h\mustache\parse_mustache($template);

		$expected = 'This is a \'thing\' with a section';
		$context = (object) array('variable' => 'thing');

		$callback = function () use ($parsed, $expected, $context)
			{
				$render = h\mustache\render_template($parsed, $context);
				return $render === $expected;
			} ;
		$this->add_test($callback, $messages) ;

		$messages = array('Tests mustache render empty variable') ;
		$expected = 'This is a \'\' with a ';
		$parsed = h\mustache\parse_mustache($template);
		$context = (object) array();

		$callback = function () use ($parsed, $expected, $context)
			{
				$render = h\mustache\render_template($parsed, $context);
				return $render === $expected;
			} ;
		$this->add_test($callback, $messages) ;

		$template = h\string('This is a \'{{variable}}\' with no {{^variable}}section{{/variable}}');
		$expected = 'This is a \'thing\' with no ';
		$parsed = h\mustache\parse_mustache($template);
		$context = (object) array('variable' => 'thing');

		$messages = array('Tests mustache not section with set variable') ;
		$callback = function () use ($parsed, $expected, $context)
			{
				$render = h\mustache\render_template($parsed, $context);
				return $render === $expected;
			} ;
		$this->add_test($callback, $messages) ;

		$template = h\string('This is a \'{{variable}}\' with no {{^variable}}section{{/variable}}');
		$expected = 'This is a \'\' with no section';
		$parsed = h\mustache\parse_mustache($template);
		$context = (object) array();

		$messages = array('Tests mustache not section with not set variable') ;
		$callback = function () use ($parsed, $expected, $context)
			{
				$render = h\mustache\render_template($parsed, $context);
				return $render === $expected;
			} ;
		$this->add_test($callback, $messages) ;
	}
}
