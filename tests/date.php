<?php

/** ?
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

namespace tests ;

use horn\lib as h ;
use horn\lib\test as t ;

require_once 'horn/lib/time/date.php' ;

clASS test_suite_date
	extends t\suite_object
{
	public		function __construct()
	{
		parent::__construct('Date') ;

		$this->providers[] = function () { return new h\collection ; } ;
	}

	protected	function test_birthday()
	{
		$birthday = new h\date(1980, 12, 22) ;
		$this->_assert_is_a($birthday, '\horn\lib\date') ;
		$this->_assert($birthday->check()) ;
		$this->_assert_equals(22, $birthday->day) ;
		$this->_assert_equals(12, $birthday->month) ;
		$this->_assert_equals(1980, $birthday->year) ;

		$week = $birthday->week() ;

		$this->_assert_is_a($week, '\horn\lib\week') ;
		$this->_assert_equals(7, $week->count()) ;

		$this->_assert_is_a($week[0], '\horn\lib\date') ;
		$this->_assert_is_a($week[6], '\horn\lib\date') ;

		$this->_assert_equals(7, $week->count()) ;
		$this->_assert(!isset($week[7])) ;

		$messages = array('Week overflow.') ;
		$expected_exception = '\horn\lib\exception' ;
		$callback = function () use ($week) { return $week[7] ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;

		$messages = array('Undefined offset') ;
		$expected_exception = '\horn\lib\exception' ;
		$callback = function () use ($week) { return $week['mon'] ; } ;
		$this->add_test($callback, $messages, $expected_exception) ;

		$this->_assert($week['monday'], 'horn\lib\date') ;

		$this->_assert_equals($week['monday'], $birthday) ;
	}
}


