<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/uri');
h\import('lib/test');

class tested_uri
    extends h\uri_absolute
{
	protected function is_scheme_supported(h\string $candidate)
    {
        return h\string('a')->is_equal($candidate);
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
			$uri = new tested_uri;
			$uri->scheme = h\string('a');
			$uri->scheme_specific_part = h\string('b');
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
			return h\string($u->scheme)->is_equal(h\string('a'))
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
			$factory->create(h\string('toto:plop'));
		};
		$this->add_test($callback, $messages, $expected_exception);
	}
}