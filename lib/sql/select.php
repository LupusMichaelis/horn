<?php
/** select class definition
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2011, Lupus Michaelis
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
namespace horn\lib\sql ;
use \horn\lib as h ;

h\import('lib/object') ;
h\import('lib/collection') ;

h\import('lib/sql/query') ;

class select
	extends query
{
	protected	$_fields ;
	protected	$_criteria ;

	public		function __construct(h\collection $fields = null)
	{
		$this->_fields = h\collection() ;
		$this->_criteria = h\collection() ;
		parent::__construct() ;
		$this->fields = $fields ;
	}

	public		function values($fields)
	{
		$q = $this->q(); ;
		$q->values[] = $fields ;
		return $q ;
	}

	public		function insert($fields)
	{
		$q = new insert($this) ;
		$q->fields = $fields ;
		return $q ;
	}

	public		function from($table)
	{
		$q = $this->q(); ;
		$q->table = $table ;
		return $q ;
	}

	protected	function _to_string()
	{
		$pattern = 'select %s from %s' ;
		$fields = count($this->fields)
			? $this->fields->implode(', ')
			: '*' ;
		$select = sprintf($pattern, $fields, $this->table) ;

		$where = (string) $this->_where;

		return strlen($where) ? "$select where $where" : $select ;
	}
}

