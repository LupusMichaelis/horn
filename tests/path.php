<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/uri/path');

class test_suite_path
	extends t\suite
{
	public		function __construct($message = 'Path')
	{
		parent::__construct($message);

		$this->providers[] = function() { null; };
	}

	protected	function _test_create_http_uri()
	{
		$messages = array('Path');
		$expected_exception = null;

		$callback = function()
		{
			$path = new h\uri\path;
			$path->set_impl(new h\uri\net_path);

			$path->authority->host->set_impl(new h\inet\host);
			$path->path->set_impl(new h\uri\empty_path);

			return h\string('//')->is_equal($path->_to_string());
		};
		$this->add_test($callback, $messages, $expected_exception);
	}

	protected	function _test_create_http_uri_localhost()
	{
		$messages = array('Path with //localhost');
		$expected_exception = null;

		$callback = function()
		{
			$path = new h\uri\path;
			$path->set_impl(new h\uri\net_path);

			$path->authority->host->set_impl(new h\inet\host);
			$path->authority->host->segments[] = 'localhost';
			$path->path->set_impl(new h\uri\empty_path);

			return h\string('//localhost')->is_equal($path->_to_string());
		};
		$this->add_test($callback, $messages, $expected_exception);
	}

	protected	function _test_create_http_uri_example_com()
	{
		$messages = array('Path with //example.com');
		$expected_exception = null;

		$callback = function()
		{
			$path = new h\uri\path;
			$path->set_impl(new h\uri\net_path);

			$path->authority->host->set_impl(new h\inet\host);
			$path->authority->host->segments[] = 'example';
			$path->authority->host->segments[] = 'com';
			$path->path->set_impl(new h\uri\empty_path);

			return h\string('//example.com')->is_equal($path->_to_string());
		};
		$this->add_test($callback, $messages, $expected_exception);
	}

	protected	function _test_create_http_uri_example_com_here()
	{
		$messages = array('Path with //example.com/here');
		$expected_exception = null;

		$callback = function()
		{
			$path = new h\uri\path;
			$path->set_impl(new h\uri\net_path);

			$path->authority->host->set_impl(new h\inet\host);
			$path->authority->host->segments[] = 'example';
			$path->authority->host->segments[] = 'com';
			$path->path->set_impl(new h\uri\absolute_path);
			$path->path->segments[] = 'here';

			return h\string('//example.com/here')->is_equal($path->_to_string());
		};
		$this->add_test($callback, $messages, $expected_exception);
	}

	protected	function _test_create_http_uri_example_com_here_params()
	{
		$messages = array('Path with //example.com/here?first=one');
		$expected_exception = null;

		$callback = function()
		{
			$wrapper = new h\uri\path;
			$wrapper->set_impl(new h\uri\hierarchical_part);
			$wrapper->path->set_impl(new h\uri\net_path);
			$wrapper->authority->host->set_impl(new h\inet\host);

			$wrapper->authority->host->segments[] = 'example';
			$wrapper->authority->host->segments[] = 'com';
			$wrapper->path->path->set_impl(new h\uri\absolute_path);
			$wrapper->path->path->segments[] = 'here';
			$wrapper->query['first'] = 'one';

			return h\string('//example.com/here?first=one')->is_equal($wrapper->_to_string());
		};
		$this->add_test($callback, $messages, $expected_exception);
	}
}
