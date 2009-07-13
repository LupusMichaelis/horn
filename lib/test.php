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

/**
 *
 */
class unit
	extends \horn\object_public
{
	public		function __construct()
	{
#		$this->_messages = new \horn\collection ;
		$this() ;
		unset($this) ;
	}

	public		function __destruct()
	{
		$this->message('Statistics success(%d/%d)'
				, $this->counter - $this->failed, $this->counter) ;
	}

	protected	function __invoke()
	{
	}

	protected	function begin($message = null)
	{
		if(is_null($message))
			$this->message('Begin unit test.') ;
		else
			$this->message('Begin unit test (%s).', $message) ;
	}

	protected	function end()
	{
	}

	protected	function begin_case($message = null)
	{
		$this->counter++ ;
	}

	protected	function end_case()
	{
	}

	protected	function expected()
	{
		$this->message('') ;
	}

	protected	function expectedException(\exception $exception)
	{
		$this->message('') ;
	}

	protected	function notThrow()
	{
		$this->error('') ;
	}

	protected	function unexpected()
	{
		$this->error('') ;
	}

	protected	function unexpectedException(\exception $exception)
	{
		$this->error('') ;
	}

	protected	function message($fmt)
	{
		echo call_user_func_array('sprintf', func_get_args()), "\n" ;
	}

	protected	function error($message)
	{
		$this->failed++ ;
	}

	protected	$failed = 0 ;
	protected	$counter = 0 ;
}

