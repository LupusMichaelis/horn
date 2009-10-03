<?php

namespace horn ;

require_once 'horn/lib/string.php' ;
require_once 'horn/lib/test.php' ;


class test_unit_string
	extends test\unit
{
	public		$instance = null ;

	public		function run()
	{
		$this->_test_instantiation() ;
		$this->_test_is_a($this->instance, 'horn\object_base') ;
		$this->_test_is_a($this->instance, 'horn\object_public') ;
		$this->_test_is_a($this->instance, 'horn\string') ;

		$this->_test_empty($this->instance) ;

		$this->_test_append($this->instance) ;
		$this->_test_prepend($this->instance) ;

		$this->_test_head($this->instance) ;
	}

	public		function provides()
	{
		return new string ; 
	}

	protected	function _test_empty(string $o)
	{
		$this->_begin_case('Tests on an empty string.') ;
		$this->_test($o->length() == 0) ;
		$this->_end_case() ;
	}

	protected	function _test_append(string $o)
	{
		$this->_begin_case('Tests appending on string.') ;
		$this->_test($o->length() == 0) ;
		
		$subject = 'Some string that\'s fine.' ;

		try { $o->append(string($subject)) ; $this->expected() ; }
		catch(\exception $e) { $this->exception_unexpected($e) ; }

		$this->_test($o->length() == strlen($subject)) ;

		$this->end_case() ;
	}

	protected	function _test_prepend(string $o)
	{
		$this->_begin_case('Tests prepending on string.') ;
		$this->_test($o->length() == 0) ;
		
		$subject = 'Some string that\'s fine.' ;

		try { $o->prepend(string($subject)) ; $this->_expected() ; }
		catch(\exception $e) { $this->_exception_unexpected($e) ; }

		$this->_test($o->length() == strlen($subject)) ;

		$this->end_case() ;
	}
}
