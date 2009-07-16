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

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/collection.php' ;

class test
	extends \horn\object_public
{
	const		CAPTION = 'Begin test case.' ;

	public		function __construct(unit $relative)
	{
		parent::__construct() ;

		$this->unit = $relative ;
	}

	public		function __invoke()
	{
		$this->unit->begin(static::CAPTION) ;

		try { $this->run() ; $this->on_exception_not_thrown() ; }
		catch(\exception $e) { $this->on_exception_thrown($e) ; }

		$this->unit->end() ;
	}

	/** Actual test done in there */
	abstract
	public		function run() ;

	public		function on_exception_thrown()
	{
		if($this->exception_is_error())
			$this->success = false ;
	}

	public		function on_exception_not_thrown()
	{
		if($this->exception_is_error())
			$this->success = true ;
	}

	public		function speak()
	{
		if($this->success)
			$this->unit->message($message_if_true) ;
		else
			$this->unit->error($message_if_false) ;
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
		if($this->success)
			$this->unit->info('Case passed.') ;
		elseif(is_null($this->success))
			$this->unit->info('Case not processed ?') ;
		else
			$this->unit->info('Case failed !') ;
	}
}

/**
 *
 */
abstract
class unit
	extends \horn\object_public
{
	public		function __construct()
	{
#		$this->_messages = new \horn\collection ;
		$this->begin() ;
		$this->run() ;
		$this->end() ;

		unset($this) ;
	}

	public		function __destruct()
	{
		$this->info('Statistics success(%d/%d)'
				, $this->counter - $this->failures, $this->counter) ;
	}

	abstract
	protected	function run() ;

	protected	function begin($message = null)
	{
		if(is_null($message))
			$this->info('Begin unit test.') ;
		else
			$this->info('Begin unit test (%s).', $message) ;
	}

	protected	function end()
	{
		$this->info("End unit test.\n") ;
	}

	protected	function expected()
	{
		$this->message('Correct behaviour.') ;
	}

	protected	function exception_was_expected(\exception $exception)
	{
		$this->message('Exception \'%s\' thrown.', $exception->getMessage()) ;
	}

	protected	function exception_not_thrown()
	{
		$this->error('Expected exception was not thrown.') ;
	}

	protected	function unexpected()
	{
		$this->error('Unexpected behaviour doesn\'t happend.') ;
	}

	protected	function exception_unexpected(\exception $exception)
	{
		$this->error('Unexpected exception (%s) happends.', $exception->getMessage()) ;
	}

	protected	function info($fmt)
	{
		echo call_user_func_array('sprintf', func_get_args()), "\n" ;
	}

	protected	function message($fmt)
	{
		$this->success = true ;
		call_user_func_array(array($this, 'info'), func_get_args()) ;
	}

	protected	function error($fmt)
	{
		$this->success = false ;
		$this->failures++ ;
		call_user_func_array(array($this, 'info'), func_get_args()) ;
	}

	protected	function test($test_true, $message = 'Test case', $on_true = 'Ok', $on_false = 'Ko')
	{
		$test = new test($this, $message, $on_true, $on_false) ;
		$test() ;

	}

	protected	function test_instantiation()
	{
		$this->begin_case('Instantiate') ;

		try { $this->instance = $this->provides() ; }
		catch(\exception $e) { $this->exception_unexpected($e) ; }

		$this->end_case() ;
	}

	protected	function test_equal($left, $right)
	{
		$this->test($left == $right
			, 'Testing equality.'
			, 'Equality ok.'
			, sprintf('Not equal (%d != %d).', $left, $right)
		) ;
	}

	protected	function test_is_scalar($variable)
	{
		$this->test(is_scalar($variable), 'Testing is a scalar.') ;
	}

	protected	function test_is_resource($variable)
	{
		$this->test(is_resource($variable), 'Testing is a resource.') ;
	}

	protected	function test_is_integer($variable)
	{
		$this->test(is_integer($variable), 'Testing is an integer.') ;
	}

	protected	function test_is_float($variable)
	{
		$this->test(is_float($variable), 'Testing is a float.') ;
	}

	protected	function test_is_string($variable)
	{
		$this->test(is_string($variable), 'Testing is a string.') ;
	}

	protected	function test_is_object($variable)
	{
		$this->test(is_object($variable), 'Testing is an object.') ;
	}

	protected	function test_is_a($object, $class)
	{
		$this->test_class_exists($class) ;
		$this->test_is_object($object) ;
		$this->test(is_a($object, $class), sprintf('Testing is an instance of \'%s\'.', $class)) ; 
	}

	protected	function test_class_exists($class_name)
	{
		$this->test(class_exists($class_name)) ;
	}

	protected	$success = null ;
	protected	$failures = 0 ;
	protected	$counter = 0 ;
}

