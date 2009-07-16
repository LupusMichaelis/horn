<?php

namespace horn ;

require_once 'horn/lib/string.php' ;
require_once 'horn/lib/test.php' ;


class test_unit_string
	extends test\unit
{
	public		$instance = null ;

	protected	function run()
	{
		$this->test_instantiation() ;
		$this->test_is_a($this->instance, 'horn\object_base') ;
		$this->test_is_a($this->instance, 'horn\object_public') ;
		$this->test_is_a($this->instance, 'horn\string') ;

		$this->test_empty($this->instance) ;

		$this->test_append($this->instance) ;
		$this->test_prepend($this->instance) ;

		$this->test_head($this->instance) ;
	}

	protected	function provides()
	{
		return new string ; 
	}

	protected	function test_empty(string $o)
	{
		$this->begin_case('Tests on an empty string.') ;
		$this->test($o->length() == 0) ;
		$this->end_case() ;
	}

	protected	function test_append(string $o)
	{
		$this->begin_case('Tests appending on string.') ;
		$this->test($o->length() == 0) ;
		
		$subject = 'Some string that\'s fine.' ;

		try { $o->append(string($subject)) ; $this->expected() ; }
		catch(\exception $e) { $this->exception_unexpected($e) ; }

		$this->test($o->length() == strlen($subject)) ;

		$this->end_case() ;
	}

	protected	function test_prepend(string $o)
	{
		$this->begin_case('Tests prepending on string.') ;
		$this->test($o->length() == 0) ;
		
		$subject = 'Some string that\'s fine.' ;

		try { $o->prepend(string($subject)) ; $this->expected() ; }
		catch(\exception $e) { $this->exception_unexpected($e) ; }

		$this->test($o->length() == strlen($subject)) ;

		$this->end_case() ;
	}
}
