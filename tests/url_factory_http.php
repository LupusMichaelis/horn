<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/uri');
h\import('lib/uri/path');
h\import('lib/uri/port');
h\import('lib/inet/url');
h\import('lib/http/url');
h\import('lib/test');

class test_suite_url_factory_http
	extends t\suite
{
	private		$factory;

	public		function __construct($message = 'URL Factory')
	{
		parent::__construct($message);

		$factory = new h\uri\factory;
		$factory->do_register_factory(h\text('http')
				, new h\http\uri_factory($factory));
		$factory->do_register_factory(h\text('net_path')
				, new h\uri\net_path_factory($factory));
		$factory->do_register_factory(h\text('hierarchical_part')
				, new h\uri\hierarchical_part_factory($factory));
		$factory->do_register_factory(h\text('host')
				, new h\uri\host_factory($factory));
		$factory->do_register_factory(h\text('port')
				, new h\uri\port_factory($factory));
		$factory->do_register_factory(h\text('absolute_path')
				, new h\uri\path_factory($factory));

		$this->factory = $factory;

		$this->providers[] = function() { return 'http://localhost/'; };
		$this->providers[] = function() { return 'http://projects.lupusmic.org/horn'; };
		$this->providers[] = function() { return 'http://projects.lupusmic.org:8080/horn'; };
	}

	protected	function _test_create_http_uri()
	{

		$messages = array('HTTP URL');
		$expected_exception = null;

		$literal = h\text($this->target);
		$factory = $this->factory;

		$callback = function () use ($literal, $factory)
		{
			$url = $factory->create($literal);
			return $url->scheme->is_equal(h\text('http'))
				&& h\text($url)->is_equal($literal);
		};
		$this->add_test($callback, $messages, $expected_exception);
	}
}

