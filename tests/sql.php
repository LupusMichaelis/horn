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

require_once 'horn/lib/sql/database.php' ;

class test_suite_sql
	extends t\suite_object
{
	public		function __construct()
	{
		parent::__construct('Database') ;
		$dbcon = new \mysqli('localhost', 'test', 'test', 'test') ;
		$this->providers[] = function () use ($dbcon) {
			// I know, this is unsecure
			$forge = new h\sql\forge($dbcon) ;

			return $forge ;
		} ;
	}

	protected	function _test_select_from()
	{
		$db = $this->target ;

		$query = $db->select()
			->from('pictures') ;
		$this->_assert_equal((string) $query, 'select * from pictures') ;

		return $query ;
	}

	protected	function _test_select_from_where()
	{
		$select = $this->_test_select_from() ;

		$this->_assert_equal('select * from pictures', (string) $select) ;

		$where = $select
			->where('id');

		$this->_assert_equal('select * from pictures where id'
				, (string) $where) ;

		$where_equals = $where
			->equals(10) ;

		$this->_assert_equal('select * from pictures where id=10'
				, (string) $where_equals) ;

		$where_in = $select->where('id')->in(h\collection(1, 2)) ;
		$this->_assert_equal('select * from pictures where id in (1, 2)'
				, (string) $where_in) ;
	}
}



