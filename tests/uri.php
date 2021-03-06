<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/uri');
h\import('lib/test');

class tested_a_uri
    extends h\uri\absolute
{
	protected function is_scheme_supported(h\text $candidate)
    {
        return h\text('a')->is_equal($candidate);
    }
}

class tested_a_factory

    extends h\uri\specific_factory
{
	public		function do_feed(h\text $meat)
	{
	}
}

class test_suite_uri
	extends t\suite_object
{
	public		function __construct($message = 'URL')
	{
		parent::__construct($message);

		$this->providers[] = function()
		{
			$uri = new tested_a_uri;
			$uri->scheme = h\text('a');
			$uri->scheme_specific_part = h\text('b');
			return $uri;
		};
	}

	protected	function _test_create_tested_uri()
	{

		$messages = array('Mock URL');
		$expected_exception = null;

		$u = $this->target;
		$callback = function () use ($u)
		{
			return h\text($u->scheme)->is_equal(h\text('a'))
				&& 'a:b' === (string) $u;
		};
		$this->add_test($callback, $messages, $expected_exception);
	}

	protected	function _test_create_unknown_scheme()
	{

		$messages = array('Unknown scheme URL');
		$expected_exception = '\horn\lib\exception';

		$u = $this->target;
		$callback = function () use ($u)
		{
			$factory = new h\uri\factory;
			$factory->base_uri = new tested_a_uri;
			$factory->do_register_factory(h\text('scheme'), new h\uri\scheme_factory($factory));

			$factory->create(h\text('a:plop'));
		};
		$this->add_test($callback, $messages, $expected_exception);
	}
}

if(false): ?>
class test_suite_path
	extends t\suite
{
	private		$factory;

	public		function __construct($message = 'URL Factory')
	{
		parent::__construct($message);

		$factory = new h\uri\factory;
		$factory->base_url = new h\uri\absolute;
		$factory->base_url->scheme = new h\scheme('http');
		$factory->do_register_factory(h\text('path')
				, new h\uri\path_factory($factory));
		$factory->do_register_factory(h\text('port')
				, new h\uri\port_factory($factory));
		$factory->do_register_factory(h\text('host')
				, new h\uri\host_factory($factory));
		$factory->do_register_factory(h\text('authority')
				, new h\uri\authority_factory($factory));
		$factory->do_register_factory(h\text('hierarchical_part')
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
			$suspect = h\text($suspect);
			$url = $factory->create(clone $suspect);
			return $url instanceof h\uri\hierarchical_part
				&& h\text($url)->is_equal($suspect);
		};
		$this->add_test($callback, $messages, $expected_exception);
	}
}
<?php  endif;// false
