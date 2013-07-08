<?php

namespace tests ;
use horn\lib as h ;
use horn\lib\test as t ;

h\import('lib/string') ;
h\import('lib/test') ;

class test_suite_string
	extends t\suite_object
{
	public		function __construct($message = 'String')
	{
		parent::__construct($message) ;

		$this->providers[] = function() { return new h\string ; } ;
	}

	protected	function _test_empty()
	{
		$messages = array('Tests on an empty string.') ;
		$o = $this->target ;
		$callback = function () use ($o) { return $o->length() === 0 ; } ;
		$this->add_test($callback, $messages) ;
	}

	protected	function _test_append()
	{
		$messages = array('Tests appending on string.') ;
		$o = $this->target ;
		$callback = function () use ($o) 
			{
				$subject = 'Some string that\'s fine.' ;
				$size = $o->length() ;
				$o->append(h\string($subject)) ;
				return $o->length() === ($size + strlen($subject)) ;
			} ;
		$this->add_test($callback, $messages) ;
	}

	protected	function _test_prepend()
	{
		$messages = array('Tests prepending on string.') ;
		$o = $this->target ;
		$callback = function () use ($o) 
			{
				$subject = 'Some string that\'s fine.' ;
				$size = $o->length() ;
				$o->prepend(h\string($subject)) ;
				return $o->length() === ($size + strlen($subject)) ;
			} ;
		$this->add_test($callback, $messages) ;
	}

	protected	function _test_search()
	{
		$messages = array('Tests find value in a string.') ;
		$o = $this->target ;
		$callback = function () use ($o)
			{
				$subject = 'Some string that\'s fine.' ;
				$offset = $o->search('i') ;
				return $offset === 8 ;
			} ;
		$this->add_test($callback, $messages) ;
	}

	protected	function _test_encoding()
	{
		$messages = array('Tests encoding.') ;
		$callback = function ()
			{
				$s = h\string("UTF-8 string \xC9");
				return $s->charset === 'UTF-8';
			} ;
		$this->add_test($callback, $messages) ;
	}

	protected	function _test_slice()
	{
		$messages = array('Tests slice.') ;
		$callback = function ()
			{
				$s = h\string("This is some ham to slice");
				$h = $s->slice($s->search('ham'), $s->search('ham') + strlen('ham'));
				return $h->is_equal(h\string('ham'));
			} ;
		$this->add_test($callback, $messages) ;
	}
}

