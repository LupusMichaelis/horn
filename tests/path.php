<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/uri/path');

class test_suite_path
	extends t\suite
{
	private		$factory;

	public		function __construct($message = 'URL Factory')
	{
		parent::__construct($message);

		$factory = new h\uri\factory;
		$factory->do_register_factory(h\string('path')
				, new h\uri\path_factory($factory));
		$factory->do_register_factory(h\string('port')
				, new h\uri\port_factory($factory));
		$factory->do_register_factory(h\string('host')
				, new h\uri\host_factory($factory));
		$factory->do_register_factory(h\string('authority')
				, new h\uri\authority_factory($factory));
		$factory->do_register_factory(h\string('hierarchical_part')
				, new h\uri\hierarchical_part_factory($factory));

		$this->providers[] = function() { return '/'; };
		$this->providers[] = function() { return '/toto'; };
		$this->providers[] = function() { return '//toto'; };
		$this->providers[] = function() { return '//toto/tata'; };
		$this->providers[] = function() { return '//toto/tata/'; };
		$this->providers[] = function() { return '//toto/tata/tutu'; };
		$this->providers[] = function() { return '//example.com/tata/tutu'; };
		//$this->providers[] = function() { return '//example.com:8080/tata/tutu'; };
		$this->factory = $factory;
	}

	protected	function _test_create_http_uri()
	{

		$messages = array('Path');
		$expected_exception = null;

		$factory = $this->factory;
		$suspect = $this->target;

		$callback = function () use ($factory, $suspect)
		{
			$suspect = h\string($suspect);
			$url = $factory->create(clone $suspect);
			return $url instanceof h\uri\hierarchical_part
				&& h\string($url)->is_equal($suspect);
		};
		$this->add_test($callback, $messages, $expected_exception);
	}
}


