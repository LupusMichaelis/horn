<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/collection');
h\import('lib/test');

class test_suite_collection
	extends t\suite_object
{
	public		$instance = null;

	public		function __construct()
	{
		parent::__construct('Object');

		$this->providers[] = function () { return new h\collection ; };
	}

	protected	function _test_array()
	{
		$this->_test_stack();
		$this->_assert_equals(0, $this->target->count());
		$this->_test_add();
	}

	protected	function _test_stack()
	{
		$this->_test_stack_getter();
		$this->_test_stack_setter();
	}

	protected	function _test_stack_getter()
	{
		$messages = array('Trying to get readonly stack.');
		$expected_exception = '\horn\lib\exception';
		// \warning	use $this->get_stack() to get a copy
		$o = $this->target;
		$callback = function () use ($o)
			{ $o->stack ; return true ; };
		$this->add_test($callback, $messages, $expected_exception);
	}

	protected	function _test_stack_setter()
	{
		$messages = array('Trying to set readonly stack.');
		$expected_exception = '\horn\lib\exception';
		$o = $this->target;
		$callback = function () use ($o)
			{ $o->stack = array() ; return false ; };
		$this->add_test($callback, $messages, $expected_exception);
	}

	protected	function _test_add()
	{
		$messages = ['Push an element to a h\collection.'];
		$o = $this->target;
		$callback = function () use ($o)
			{
				$o[] = 'toto';
				return 1 == $o->count();
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_add_element_with_index()
	{
		$messages = ['Add an element with a numeric index to a h\collection.'];
		$o = $this->target;
		$callback = function () use ($o)
			{
				$o[] = 'toto';
				$o[10] = 'toto';
				$o['key'] = 'toto';
				$o->push('toto');
				return 4 == $o->count();
			};

		$this->add_test($callback, $messages);
	}

	function _test_undefined_offset()
	{
		$messages = array('Trying to access undefined offset.');
		$expected_exception = '\horn\lib\exception';

		$o = $this->target;
		$callback = function () use ($o)
			{ $v = $o[1] ; };
		$this->add_test($callback, $messages, $expected_exception);
	}

	function _test_isset_offset()
	{
		$messages = array('Trying to use array_key_exists.');

		$o = $this->target;
		$callback = function () use ($o)
			{
				$o['key'] = 'value';
				return isset($o['key']);
			};
		$this->add_test($callback, $messages);
	}

	function _test_init()
	{
		$o = h\c(['key' => 'value', 'first']);

		$messages = ['Check key \'key\'.'];
		$callback = function () use ($o)
			{
				return isset($o['key']);
			};
		$this->add_test($callback, $messages);

		$messages = ['Check key \'0\'.'];
		$callback = function () use ($o)
			{
				return isset($o[0]) && $o->search('first') > -1 && $o[0] === 'first';
			};
		$this->add_test($callback, $messages);
	}

	public		function _test_match_keys()
	{
		$o = h\c(array('key' => 'value', 'first'));
		$k = h\c(array('key', 0));

		$messages = array('Check is key in collection.');
		$callback = function () use ($o, $k)
			{
				return $o->has_keys($k);
			};
		$this->add_test($callback, $messages);

		$k = h\c(array('key21', 0));

		$messages = array('Check are keys in collection.');
		$callback = function () use ($o, $k)
			{
				return !$o->has_keys($k);
			};
		$this->add_test($callback, $messages);

		$k = h\c(array('key21', 1));

		$messages = array('Check are keys in collection.');
		$callback = function () use ($o, $k)
			{
				return !$o->has_keys($k);
			};
		$this->add_test($callback, $messages);
	}

	public		function _test_jsonserialize()
	{
		$a = array('key' => 'value', 'first');
		$o = h\c($a);
		$messages = array('Try to Jsonify a collection');
		$callback = function () use ($a, $o)
			{
				return json_encode($a) === json_encode($o);
			};
		$this->add_test($callback, $messages);
	}

	public		function _test_column()
	{
		$a =
			[ [ 'key' => 'first', '1' ]
			, [ 'key' => 'second', '2' ]
			];
		$o = h\c($a);
		$c = $o->get_column('key');

		$messages = ['Try to get the column\'s values from collection'];
		$callback = function () use ($a, $o)
			{
				return $o->get_column(0)->get_stack() === array_column($a, 0);
			};
		$this->add_test($callback, $messages);
	}
}
