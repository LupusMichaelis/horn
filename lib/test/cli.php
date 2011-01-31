<?php

namespace horn\lib\test ;
use horn\lib as h ;
use horn\lib\test as t ;

function cli_renderer(t\suite $suite)
{
	$howmany = count($suite->cases) ;
	$count = 0 ;
	foreach($suite->cases as $case)
	{
		++$count ;
		printf("[%d/%d] %s (%s)\n"
			, $count, $howmany
			, $case->message
			, $case->success ? $case->on_true : $case->on_false) ;
	}
}

/*
class cli_runner
	extends		h\object_public
{
	public		function __construct()
	{
		parent::__construct() ;
	}
}

class cli_renderer
	implements	renderer
	extends		h\object_public
{
	public		function message($fmt)
	{
		echo call_user_func_array('sprintf', func_get_args()), "\n" ;
	}

	public		function log(context $test)
	{
	}

}
*/
