<?php


namespace horn\lib\test;
use horn\lib as h;

h\import('lib/object');
h\import('lib/text');
h\import('lib/collection');
h\import('lib/callback');

/**
 *
 */
abstract
class suite
	extends h\object_public
{
	protected	$_cases;
	protected	$_providers = [null];
	protected	$_name ;		
	protected	$_target;

	public		function __construct($name)
	{
		$this->_providers = new h\collection();
		$this->_cases = new h\collection();
		$this->_name = h\text($name);
		parent::__construct();
	}

	final
	public		function run()
	{
		foreach($this->providers as $provider)
			foreach(get_class_methods($this) as $fn)
			{
				$this->target = $provider();
				if(0 === strpos($fn, '_test_'))
					$this->$fn();
			}

		foreach($this->cases as $case)
			$case();
	}

	/*
	protected	function _log($case)
	{
		if(!$case->success) ++$this->failures;

		$this->info($case->message);

		if(is_null($case->success))
			$this->error('Unit test not ran');
		elseif($case->success)
			$this->message($case->on_true);
		else
			$this->error($case->on_false);
	}
	*/

	protected	function assert($test_true, $messages = [])
	{
		$callback = function () use ($test_true) { return $test_true ; };
		$this->add_test($callback, $messages);
	}

	protected	function add_test($callback, $messages = [], $expected_exception = [])
	{
		if(!h\is_collection($messages))
			throw $this->_exception('variable \'messages\' is not a collection.');

		if(empty($messages[0]))			$messages[0] = 'Test case';
		if(empty($messages['true']))	$messages['true'] = 'Ok';
		if(empty($messages['false']))	$messages['false'] = 'Ko';
		
		$test = new context(h\callback($callback));
		$test->message = $messages[0];
		$test->on_true = $messages['true'];
		$test->on_false = $messages['false'];
		$test->expected_exception = $expected_exception;

		$this->cases->push($test);
	}

	protected	function _assert_equals($expected, $actual)
	{
		$messages =
			[ 'Testing equality.'
			, 'true' => 'Equality ok'
			, 'false' => sprintf
				( 'Not equal (expected \'%s\' != actual \'%s\')'
				, var_export($expected, true)
				, var_export($actual, true)
				)
			];
		$this->assert($expected == $actual, $messages);
	}

	protected	function _assert_not_equals($unexpected, $actual)
	{
		$messages =
			[ 'Testing inequality.'
			, 'true' => 'Inequality ok'
			, 'false' => sprintf('Not equal (unexpected \'%s\' == actual \'%s\')'
				, var_export($unexpected, true)
				, var_export($actual, true)
				)
			];
		$this->assert($unexpected != $actual, $messages);
	}

	protected	function _assert_is_set($variable)
	{
		$messages = ['Testing is set variable'];
		$this->assert(isset($variable), $messages);
	}

	protected	function _assert_is_empty($variable)
	{
		$messages = ['Testing is empty variable'];
		$this->assert(empty($variable), $messages);
	}

	protected	function _assert_is_null($variable)
	{
		$messages = ['Testing is null value'];
		$this->assert(is_null($variable), $messages);
	}

	protected	function _assert_is_scalar($variable)
	{
		$messages = ['Testing is a scalar'];
		$this->assert(is_scalar($variable), $messages);
	}

	protected	function _assert_is_resource($variable)
	{
		$messages = ['Testing is a resource'];
		$this->assert(is_resource($variable), $messages);
	}

	protected	function _assert_is_integer($variable)
	{
		$messages = ['Testing is an integer'];
		$this->assert(is_integer($variable), $messages);
	}

	protected	function _assert_is_float($variable)
	{
		$messages = ['Testing is a float'];
		$this->assert(is_float($variable), $messages);
	}

	protected	function _assert_is_string($variable)
	{
		$messages = ['Testing is a string'];
		$this->assert(is_string($variable), $messages);
	}

	protected	function _assert_is_object($variable)
	{
		$messages = ['Testing is an object'];
		$this->assert(is_object($variable), $messages);
	}

	protected	function _assert_is_a($object, $class)
	{
		$this->_assert_class_exists($class);
		$this->_assert_is_object($object);
		$messages = [sprintf('Testing is an instance of \'%s\'', $class)];
		$this->assert(is_a($object, $class), $messages);
	}

	protected	function _assert_class_exists($class_name)
	{
		$this->assert(class_exists($class_name));
	}
}

/**
 *
 */
abstract
class suite_object
	extends suite
{
	protected	function _test_instanciate()
	{
		$this->_assert_is_object($this->target);
	}
}
