<?php

namespace tests;
use horn\lib as h;
use horn\lib\test as t;

h\import('lib/text');
h\import('lib/test');

class test_suite_text
	extends t\suite_object
{
	public		function __construct($message = 'String')
	{
		parent::__construct($message);

		$this->providers[] = function() { return new h\text ; };
	}

	protected	function _test_empty()
	{
		$messages = ['Tests on an empty text.'];
		$o = $this->target;
		$callback = function () use ($o) { return $o->length() === 0 ; };
		$this->add_test($callback, $messages);
	}

	protected	function _test_append()
	{
		$messages = ['Tests appending on text.'];
		$o = $this->target;
		$callback = function () use ($o) 
			{
				$subject = 'Some text that\'s fine.';
				$size = $o->length();
				$o->append(h\text($subject));
				return $o->length() === ($size + strlen($subject));
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_prepend()
	{
		$messages = ['Tests prepending on text.'];
		$o = $this->target;
		$callback = function () use ($o) 
			{
				$subject = 'Some text that\'s fine.';
				$size = $o->length();
				$o->prepend(h\text($subject));
				return $o->length() === ($size + strlen($subject));
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_search()
	{
		$messages = ['Tests find value in a text.'];
		$o = $this->target;
		$callback = function () use ($o)
			{
				$subject = 'Some string that\'s fine.';
				$o->append(h\text($subject));
				$offset = $o->search('i');
				return $offset === 8;
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_encoding()
	{
		$messages = ['Tests encoding.'];
		$callback = function ()
			{
				$s = h\text("UTF-8 string \xC9");
				return $s->charset === 'UTF-8';
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_slice()
	{
		$messages = ['Tests slice.'];
		$callback = function ()
			{
				$s = h\text('This is some ham to slice');
				$h = $s->slice($s->search('ham'), $s->search('ham') + strlen('ham'));
				return $h->is_equal(h\text('ham'));
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_head()
	{
		$messages = ['Tests head'];
		$callback = function ()
			{
				$s = h\text('HeadTail');
				$h = $s->head(4);
				return h\text('Head')->is_equal($h)
					&& h\text('HeadTail')->is_equal($s);
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_behead()
	{
		$messages = ['Tests behead'];
		$callback = function ()
			{
				$s = h\text('HeadTail');
				$h = $s->behead(4);
				return h\text('Tail')->is_equal($s)
					&& h\text('Head')->is_equal($h);
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_tail()
	{
		$messages = ['Tests tail'];
		$callback = function ()
			{
				$s = h\text('HeadTail');
				$t = $s->tail(4);
				return h\text('Tail')->is_equal($t)
					&& h\text('HeadTail')->is_equal($s);
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_betail()
	{
		$messages = ['Tests betail'];
		$callback = function ()
			{
				$s = h\text('HeadTail');
				$t = $s->betail(4);
				return h\text('Tail')->is_equal($t)
					&& h\text('Head')->is_equal($s);
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_betail_origin()
	{
		$messages = ['Tests betail from 0'];
		$callback = function ()
			{
				$s = h\text('HeadTail');
				$t = $s->betail(0);
				return h\text('HeadTail')->is_equal($t)
					&& h\text('')->is_equal($s);
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_behead_origin()
	{
		$messages = ['Tests behead from 0'];
		$callback = function ()
			{
				$s = h\text('HeadTail');
				$h = $s->behead(0);
				return h\text('')->is_equal($h)
					&& h\text('HeadTail')->is_equal($s);
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_betail_end()
	{
		$messages = ['Tests betail from end'];
		$callback = function ()
			{
				$s = h\text('HeadTail');
				$t = $s->betail($s->length());
				return h\text('')->is_equal($t)
					&& h\text('HeadTail')->is_equal($s);
			};
		$this->add_test($callback, $messages);
	}

	protected	function _test_behead_end()
	{
		$messages = ['Tests behead from end'];
		$callback = function ()
			{
				$s = h\text('HeadTail');
				$h = $s->behead($s->length());
				return h\text('HeadTail')->is_equal($h)
					&& h\text('')->is_equal($s);
			};
		$this->add_test($callback, $messages);
	}
}

