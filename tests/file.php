<?php

namespace horn;

use \horn\lib as h;

h\import('lib/file');
h\import('lib/test');

class test_unit_file
	extends test\unit_object
{
	public		$instance = null;

	public		function __construct($message = 'File')
	{
		parent::__construct($message);
	}

	public		function provides()
	{
		return file_factory::create('application/octet-stream');
	}

	public		function run()
	{
		$this->instance = $this->_test_instanciate();

		$expected_exception = null;

		try
		{
			$this->_test_virtual($this->instance);
			$this->_exception_not_thrown($expected_exception);
		}
		catch(\exception $e) { $this->_exception_thrown($e, $expected_exception) ; }
	}

	protected	function _test_virtual(object_base $o)
	{
		$this->_begin('Set a virtual file.');

		$expected_exception = null;

		try
		{
			$o->name = 'toto.txt';
			$this->_exception_not_thrown($expected_exception);
		}
		catch(\exception $e) { $this->_exception_thrown($e, $expected_exception) ; }

		$this->_end();
	}
}

/*
$pairs = array('target' => 'PHP redeemer');

$template = file_text::load('dummy.txt');
$translator = new translator($template);
$translator->add_tanslators($pairs);

$output = $translator->process();
$output->set_name('output.txt');
$output->write();

$template = file_odt::load('dummy.odt');
$translator->set_template($template);
$translator->add_tanslators($pairs);

$output = $translator->process();
$output->set_name('output.odt');
$output->write();
*/


