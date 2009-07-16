<?php

namespace horn ;

require_once 'horn/lib/file.php' ;
require_once 'horn/lib/test.php' ;

class test_unit_file
	extends test\unit
{
	public		$instance = null ;

	protected	function provides()
	{
		return file_factory::create('application/octet-stream') ;
	}

	protected	function run()
	{
		$this->test_instantiation() ;
		$this->test_virtual($this->instance) ;
	}

	protected	function test_virtual(object_base $o)
	{
		$this->begin_case('Set a virtual file.') ;

		try { $o->name = 'toto.txt' ; $this->expected() ; }
		catch(\exception $e) { $this->exception_unexpected($e) ; }

		$this->end_case() ;
	}
}

/*
$pairs = array('target' => 'PHP redeemer') ;

$template = file_text::load('dummy.txt') ;
$translator = new translator($template) ;
$translator->add_tanslators($pairs) ;

$output = $translator->process() ;
$output->set_name('output.txt') ;
$output->write() ;

$template = file_odt::load('dummy.odt') ;
$translator->set_template($template) ;
$translator->add_tanslators($pairs) ;

$output = $translator->process() ;
$output->set_name('output.odt') ;
$output->write() ;
*/


