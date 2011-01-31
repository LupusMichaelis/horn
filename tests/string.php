<?php

namespace tests ;
use horn\lib as h ;
use horn\lib\test as t ;

require_once 'horn/lib/string.php' ;
require_once 'horn/lib/test.php' ;

class test_suite_string
	extends t\suite_object
{
	public		function __construct($message = 'String')
	{
		parent::__construct($message) ;

		$this->providers[] = function() { return new h\string ; } ;
	}

	public		function run()
	{
		parent::run() ;

		foreach($this->providers as $provider)
		{
			$instance = $provider() ;
			$this->_test_is_a($instance, '\horn\lib\string') ;

			$this->_test_empty($instance) ;

			$this->_test_append($instance) ;
			$this->_test_prepend($instance) ;

#			$this->_test_head($instance) ;
		}
	}

	protected	function _test_empty(h\string $o)
	{
		$messages = array('Tests on an empty string.') ;
		$callback = function () use ($o) { return $o->length() === 0 ; } ;
		$this->add_test($callback, $messages) ;
	}

	protected	function _test_append(h\string $o)
	{
		$messages = array('Tests appending on string.') ;
		$callback = function () use ($o) 
			{
				$subject = 'Some string that\'s fine.' ;
				$size = $o->length() ;
				$o->append(h\string($subject)) ;
				return $o->length() === ($size + strlen($subject)) ;
			} ;
		$this->add_test($callback, $messages) ;
	}

	protected	function _test_prepend(h\string $o)
	{
		$messages = array('Tests prepending on string.') ;
		$callback = function () use ($o) 
			{
				$subject = 'Some string that\'s fine.' ;
				$size = $o->length() ;
				$o->prepend(h\string($subject)) ;
				return $o->length() === ($size + strlen($subject)) ;
			} ;
		$this->add_test($callback, $messages) ;
	}
}

