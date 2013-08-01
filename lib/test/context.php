<?php


namespace horn\lib\test;
use horn\lib as h;

h\import('lib/object');
h\import('lib/string');
h\import('lib/collection');
h\import('lib/callback');

/** Test management.
 *	This class provides a way to handle test running. The test is actually done in a
 *  case object.
 */
class context
	extends h\object_public
{
	const		CAPTION = 'Unamed test case.';

	public		$success = null;
	public		$message = self::CAPTION;
	public		$on_true = 'Ok';
	public		$on_false = 'Ko';
	public		$expected_exception = array();

	protected	$_callback;
	protected	$_caught_exception = null;

	public		function __construct(h\callback $callback, $expected_exception = false)
	{
		parent::__construct();

		$this->callback = $callback;
		$expected_exception and $this->expected_exception = $expected_exception;
	}

	public		function __invoke()
	{
		$callback = $this->callback;
		try { $this->success = $callback() ; $this->on_not_caught_exception() ; }
		catch(\exception $e) { $this->on_caught_exception($e) ; }

		return $this;
	}

	public		function on_caught_exception(\exception $e)
	{
		$this->_caught_exception = $e;
		// XXX for now, we just check if the exception was expected. Have to test if the
		// thrown exception is ok
		// $this->success = in_array(get_class($e), $this->expected_exception);
		$this->success = (bool) $this->expected_exception;
	}

	public		function on_not_caught_exception()
	{
		if($this->expected_exception)
			$this->success = false;
	}
}


