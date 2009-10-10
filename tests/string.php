<?php

namespace horn ;

require_once 'horn/lib/string.php' ;
require_once 'horn/lib/test.php' ;

require_once 'tests/object.php' ;

class test_unit_string
	extends test_unit_object
{
	public		$instance = null ;

	public		function __construct($message = 'String')
	{
		parent::__construct($message) ;
	}

	public		function run()
	{
		parent::run() ;

		$this->_test_is_a($this->provides(), 'horn\string') ;

		$this->_test_empty($this->provides()) ;

		$this->_test_append($this->provides()) ;
		$this->_test_prepend($this->provides()) ;

#		$this->_test_head($this->provides()) ;
	}

	public		function provides()
	{
		return new string ; 
	}

	protected	function _test_empty(string $o)
	{
		$this->_begin('Tests on an empty string.') ;
		$this->_test($o->length() == 0) ;
		$this->_end() ;
	}

	protected	function _test_append(string $o)
	{
		$this->_begin('Tests appending on string.') ;
		$this->_test($o->length() == 0) ;
		
		$subject = 'Some string that\'s fine.' ;

		try { $o->append(string($subject)) ; $this->_expected() ; }
		catch(\exception $e) { $this->_exception_unexpected($e) ; }

		$this->_test($o->length() == strlen($subject)) ;

		$this->_end() ;
	}

	protected	function _test_prepend(string $o)
	{
		$this->_begin('Tests prepending on string.') ;
		$this->_test($o->length() == 0) ;
		
		$subject = 'Some string that\'s fine.' ;

		try { $o->prepend(string($subject)) ; $this->_expected() ; }
		catch(\exception $e) { $this->_exception_unexpected($e) ; }

		$this->_test($o->length() == strlen($subject)) ;

		$this->_end() ;
	}
}
