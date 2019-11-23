<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/uri');
h\import('lib/uri/factory');
h\import('lib/test');

class tested_uri
    extends h\uri_absolute
{
	public		function is_scheme_supported(h\text $candidate)
    {
        return h\text('a')->is_equal($candidate);
    }
}

class tested_uri_factory
	extends h\uri\specific_factory
{
	public		function do_feed(h\text $scheme_specific_part)
	{
		$uri = new tested_uri;
		$uri->scheme = h\text('a');
		$uri->scheme_specific_part = $scheme_specific_part;
		return $uri;
	}
}

class test_suite_uri_factory
	extends t\suite_object
{
	public		function __construct($message = 'URL Factory')
	{
		parent::__construct($message);

		$factory = new h\uri\factory;
		$factory->do_register_factory(h\text('a'), new tested_uri_factory($factory));

		$this->providers[] = function() use ($factory)
		{
			$uri = $factory->create(h\text('a:b'));
			return $uri;
		};
	}

	protected	function _test_create_uri()
	{

		$messages = array('URL');
		$expected_exception = null;

		$u = $this->target;
		$callback = function () use ($u)
		{
			return h\text('a')->is_equal(h\text($u->scheme))
				&& 'a:b' === (string) $u;
		};
		$this->add_test($callback, $messages, $expected_exception);
	}
}
