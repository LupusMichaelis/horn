<?php


namespace horn\lib\test ;
use horn\lib as h ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/string.php' ;
require_once 'horn/lib/collection.php' ;
require_once 'horn/lib/callback.php' ;

/**
 *
 */
abstract
class suite
	extends h\object_public
{
	public		$failures = 0 ;
	public		$counter = 0 ;

	protected	$_providers = array() ;

	public		function __construct($name)
	{
		$this->_begin($name) ;
		$this->run() ;
	}

	public		function __destruct()
	{
		$this->_end() ;
	}

	abstract
	public		function run() ;

	protected	function _log($case)
	{
		$this->info($case->message) ;
		if(!$case->success) ++$this->failures ;

		if(is_null($case->success))
			$this->error('Unit test not ran') ;
		elseif($case->success)
			$this->message($case->on_true) ;
		else
			$this->error($case->on_false) ;
	}

	protected	function _begin($message)
	{
		if(!h\is_string($message))
			$this->_throw('Non-string parameter given.') ;

		$this->info('Begin test suite (%s)', $message) ;
	}

	protected	function _end()
	{
		$this->info('End test suite (%d/%d)'
				, $this->counter - $this->failures, $this->counter) ;
	}

	public		function info($fmt)
	{
		echo call_user_func_array('sprintf', func_get_args()), "\n" ;
	}

	public		function message($fmt)
	{
		call_user_func_array(array($this, 'info'), func_get_args()) ;
	}

	public		function error($fmt)
	{
		call_user_func_array(array($this, 'info'), func_get_args()) ;
	}

	protected	function _test($test_true, $messages = array())
	{
		$callback = function () use ($test_true) { return $test_true ; } ;
		$this->_do_test($callback, $messages) ;
	}

	protected	function _do_test($callback, $messages = array())
	{
		if(!h\is_collection($messages))
			$this->_throw('variable \'messages\' is not a collection.') ;

		if(empty($messages[0]))		$messages[0] = 'Test case' ;
		if(empty($messages[true]))	$messages[true] = 'Ok' ;
		if(empty($messages[false]))	$messages[false] = 'Ko' ;
		
		$test = new context(h\callback($callback)) ;
		$test->message = $messages[0] ;
		$test->on_true = $messages[true] ;
		$test->on_false = $messages[false] ;

		++$this->counter ;
		$test() ;
		$this->_log($test) ;
	}

	protected	function _test_equal($left, $right)
	{
		$messages = array
			( 'Testing equality.'
			, true => 'Equality ok.'
			, false => sprintf('Not equal (%d != %d).', $left, $right)
			) ;
		$this->_test($left == $right, $messages) ;
	}

	protected	function _test_is_set($variable)
	{
		$messages = array('Testing is set value.') ;
		$this->_test(isset($variable), $messages) ;
	}

	protected	function _test_is_empty($variable)
	{
		$messages = array('Testing is empty variable.') ;
		$this->_test(is_empty($variable), $messages) ;
	}

	protected	function _test_is_null($variable)
	{
		$messages = array('Testing is null value.') ;
		$this->_test(is_null($variable), $messages) ;
	}

	protected	function _test_is_scalar($variable)
	{
		$messages = array
			( 'Testing is a scalar.'
			#, true => 'Tested variable is a scalar'
			) ;
		$this->_test(is_scalar($variable), $messages) ;
	}

	protected	function _test_is_resource($variable)
	{
		$messages = array('Testing is a resource.') ;
		$this->_test(is_resource($variable), $messages) ;
	}

	protected	function _test_is_integer($variable)
	{
		$messages = array('Testing is an integer.') ;
		$this->_test(is_integer($variable), $messages) ;
	}

	protected	function _test_is_float($variable)
	{
		$messages = array('Testing is a float.') ;
		$this->_test(is_float($variable), $messages) ;
	}

	protected	function _test_is_string($variable)
	{
		$messages = array('Testing is a string.') ;
		$this->_test(is_string($variable), $messages) ;
	}

	protected	function _test_is_object($variable)
	{
		$messages = array('Testing is an object.') ;
		$this->_test(is_object($variable), $messages) ;
	}

	protected	function _test_is_a($object, $class)
	{
		$this->_test_class_exists($class) ;
		$this->_test_is_object($object) ;
		$messages = array
			( sprintf('Testing is an instance of \'%s\'.', $class)
			); 
		$this->_test(is_a($object, $class), $messages) ;
	}

	protected	function _test_class_exists($class_name)
	{
		$this->_test(class_exists($class_name)) ;
	}
}

/**
 *
 */
abstract
class suite_object
	extends suite
{
	public		function run()
	{
		$this->_test_instanciate() ;

		foreach($this->providers as $provider)
			$this->_test_is_a($provider(), 'h\object_base') ;
	}

	protected	function _test_instanciate()
	{
		foreach($this->providers as $provider)
		{
			$instance = $provider() ;

			$this->_begin('Instantiate') ;
			$this->_test_is_object($instance) ;
			$this->_end() ;
		}
	}

}
