<?php

namespace horn\lib\test;
use horn\lib as h;
use horn\lib\test as t;

function cli_renderer(t\suite $suite)
{
	$howmany = count($suite->cases);
	$count = 0;
	$errors = 0;
	$out = [];
	foreach($suite->cases as $case)
	{
		++$count;
		$out[] = sprintf
			( '[%d/%d] %s (%s)'
			, $count, $howmany
			, $case->message
			, $case->success ? $case->on_true : $case->on_false
			);
		if(!$case->success)
		{
			++$errors;

			if($case->caught_exception instanceof \exception)
			$out[] = $case->caught_exception->xdebug_message;
		}
	}

	print "\n".implode($out, "\n")."\n";
	printf("[%s] (%d/%d)\n", $suite->name, $count - $errors, $count);
}

/*
class cli_runner
	extends		h\object_public
{
	public		function __construct()
	{
		parent::__construct();
	}
}

class cli_renderer
	implements	renderer
	extends		h\object_public
{
	public		function message($fmt)
	{
		echo call_user_func_array('sprintf', func_get_args()), "\n";
	}

	public		function log(context $test)
	{
	}

}
*/
