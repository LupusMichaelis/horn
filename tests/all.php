<?php
/** \file
 *	Run all tests !
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

ini_set('include_path', $_SERVER['HOME'].'/php-includes/:.');

use horn\lib as h;
use horn\lib\test as t;

require'horn/lib/horn.php';

h\import('lib/test');
h\import('lib/test/cli');

define('DEBUG', true);

$available_units = array
	( 'test'
	, 'object'
	, 'wrapper'
	, 'collection'
	, 'stack'
	, 'string'

	, 'time'
	, 'date'

#	, 'file'
	, 'sql'
	, 'mustache'

	, 'path'
	, 'uri'
	, 'uri_factory'
	, 'url_factory_http'

	, 'regex'
	, 'escaper'
	);

// We throw some exception and catch them, to test behaviour. Xdebug will trace exception
// (even caught) and eventually prints it on stdout. So we ask it to stfu.
ini_set('xdebug.show_exception_trace', 0);

if($argc == 1)
{
	$units = $available_units;
}
else
{
	array_shift($argv);
	$units = array_intersect($available_units, $argv);
	$unknown_units = array_diff($argv, $available_units);

	if(count($unknown_units))
		die(sprintf("Unknown units [%s]\n", implode('|', $unknown_units)));
}

foreach($units as $unit)
{
	require_once "tests/{$unit}.php";

	try
	{
		$unit_class = "\\tests\\test_suite_$unit";
		$uc = new $unit_class;
		$uc->run();
		t\cli_renderer($uc);
	}
	catch(\exception $e)
	{
		echo $e->getMessage(), "\n" ; 
		echo $e->getTraceAsString(), "\n" ; 

		if(DEBUG) throw $e;
	}
}


