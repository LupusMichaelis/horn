<?php
/** \file
 *	Unit test classes
 *	This module provide a naive implementation of unit test method.
 *
 *	\todo	Extract output generation.
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2009, Lupus Michaelis
 *  License	AGPL <http://www.fsf.org/licensing/licenses/agpl-3.0.html>
 */

/*
 *  This file is part of Horn Framework.
 *
 *  Horn Framework is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Horn Framework is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero Public License for more details.
 *
 *  You should have received a copy of the GNU Affero Public License
 *  along with Horn Framework.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/** \package horn\tests
 */
namespace horn\test ;
use horn as h ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/string.php' ;
require_once 'horn/lib/collection.php' ;
require_once 'horn/lib/callback.php' ;

/**
 *
 */
abstract
class unit
	extends h\object_public
{
	public		$success = null ;
	public		$failures = 0 ;
	public		$counter = 0 ;

	public		function __construct($name)
	{
		$this->_begin($name) ;
		$this->run() ;
		$this->_end() ;

		unset($this) ;
	}

	public		function __destruct()
	{
		$this->info('Statistics success(%d/%d)'
				, $this->counter - $this->failures, $this->counter) ;
	}

	abstract
	public		function run() ;

	abstract
	public		function provides() ;

	protected	function _begin($message)
	{
		if(! $message instanceof h\string && !is_string($message))
			$this->_throw('Non-string parameter given.') ;

		$this->info('Begin unit test (%s).', $message) ;
	}

	protected	function _end()
	{
		$this->info("End unit test.\n") ;
	}

	protected	function _expected()
	{
		$this->message('Correct behaviour.') ;
	}

	protected	function _exception_was_expected(\exception $exception)
	{
		$this->message('Exception \'%s\' thrown.', $exception->getMessage()) ;
	}

	protected	function _exception_not_thrown()
	{
		$this->error('Expected exception was not thrown.') ;
	}

	protected	function _exception_unexpected(\exception $exception)
	{
		$this->error('Unexpected exception (%s) happends.', $exception->getMessage()) ;
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
		$this->failures++ ;
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

		if(empty($messages[0])) $messages[0] = 'Test case' ;
		if(empty($messages[true])) $messages[true] = 'Ok' ;
		if(empty($messages[false])) $messages[false] = 'Ko' ;
		
		$test = new test($this, h\callback($callback)) ;
		$test->message = $messages[0] ;
		$test->on_true = $messages[true] ;
		$test->on_false = $messages[false] ;

		$test() ;
	}

	protected	function _test_instanciate()
	{
		/* todo
		$callback = h\callback($this, 'provides') ;
		$this->_do_test($callback) ;
		*/
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
		$messages = array('Testing is a scalar.') ;
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

/** Test management.
 *	This class provides a way to handle test running. The test is actually done in a test_unit object.
 */
class test
	extends h\object_public
{
	const		CAPTION = 'Unamed test case.' ;

	public		$success = null ;
	public		$message = self::CAPTION ;
	public		$on_true = 'Ok' ;
	public		$on_false = 'Ko' ;
	public		$exception_expected ;

	protected	$_unit ;
	protected	$_callback ;

	private		$_catched_exception = null ;

	public		function __construct(unit $relative, h\callback $callback)
	{
		parent::__construct() ;

		$this->unit = $relative ;
		$this->callback = $callback ;
		$this->exception_expected = false ;
	}

	public		function __invoke()
	{
		$this->begin($this->message) ;

		$callback = $this->callback ;
		try { $this->success = $callback() ; $this->on_exception_not_thrown() ; }
		catch(\exception $e) { $this->on_exception_thrown($e) ; }

		$this->end() ;
	}

	/** Actual test done in there */
	public		function run()
	{
		$this->callback($this) ;
	}

	public		function on_exception_thrown(\exception $e)
	{
		$this->_catched_exception = $e ;
		$this->success = $this->exception_expected ;
	}

	public		function on_exception_not_thrown()
	{
		if($this->exception_expected)
			$this->success = false ;
	}

	public		function speak()
	{
		if($this->success)
			$this->unit->message($this->on_true) ;
		else
			$this->unit->error($this->on_false) ;
	}

	protected	function _set_unit(unit $relative)
	{
		$this->_unit = $relative ;
	}

	protected	function begin($message)
	{
		$this->success = null ;
		call_user_func_array(array($this->unit, 'info'), func_get_args()) ;
		$this->unit->counter++ ;
	}

	protected	function end()
	{
		if($this->success === true)
			$this->unit->info('Case passed.') ;
		elseif($this->success === false)
			$this->unit->error('Case failed !') ;
		elseif($this->success === null)
			$this->_throw('Case not processed ?') ;
		else
			$this->_throw('That\'s heavy ! Check this test case.') ;
	}
}


