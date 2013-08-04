<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/url');
h\import('lib/test');

class url
    extends h\url
{
    protected   function is_scheme_supported()
    {
        return $this->scheme === h\string('a');
    }
}

class test_suite_url
	extends t\suite_object
{
	public		function __construct($message = 'URL')
	{
		parent::__construct($message);

		$this->providers[] = function() { return new url(h\string('a:b')) ; };
	}

	protected	function _test_getter_protected()
	{

		$messages = array('Mock URL');
		$expected_exception = null;

		$u = $this->target;
		$callback = function () use ($u)
		{
			return $u->scheme->is_equal(h\string('a'))
				&& 'a:b' === (string) $u;
		};
		$this->add_test($callback, $messages, $expected_exception);
	}
}


