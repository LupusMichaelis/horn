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

namespace horn ;
require_once 'horn/lib/test.php' ;

define('DEBUG', true) ;

$available_units = array
	( 'object'
	, 'collection'
	, 'string'
	, 'file'
	, 'test'
	) ;

if($argc == 1)
{
	$units = $available_units ;
}
else
{
	array_shift($argv) ;
	$units = array_intersect($available_units, $argv) ;
}

foreach($units as $unit)
{
	require_once "horn/lib/{$unit}.php" ;
	require_once "tests/{$unit}.php" ;

	try
	{
		$unit_class = "\\horn\\test_unit_$unit" ;
		new $unit_class ;
	}
	catch(\exception $e)
	{
		echo $e->getMessage(), "\n" ; 
		echo $e->getTraceAsString(), "\n" ; 

		if(DEBUG) throw $e ;
	}
}








