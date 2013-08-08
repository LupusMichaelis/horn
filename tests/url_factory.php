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
	static protected function is_scheme_supported(h\string $candidate)
    {
        return h\string('a')->is_equal($candidate);
    }
}

class tested_uri_factory
	extends h\uri\specific_factory
{
	public		function do_feed(h\string $scheme_specific_part)
	{
		$uri = new tested_uri;
		$uri->scheme = h\string('a');
		$uri->scheme_specific_part = $scheme_specific_part;
		return $uri;
	}
}

class test_suite_url_factory
	extends t\suite_object
{
	public		function __construct($message = 'URL Factory')
	{
		parent::__construct($message);

		$factory = new h\uri\factory;
		$factory->do_register_factory(h\string('a'), new tested_uri_factory($factory));

		$this->providers[] = function() use ($factory)
		{
			$url = $factory->create_from_string(h\string('a:b'));
			return $url;
		};
	}

	protected	function _test_create_uri()
	{

		$messages = array('URL');
		$expected_exception = null;

		$u = $this->target;
		$callback = function () use ($u)
		{
			return h\string('a')->is_equal($u->scheme)
				&& 'a:b' === (string) $u;
		};
		$this->add_test($callback, $messages, $expected_exception);
	}
}
