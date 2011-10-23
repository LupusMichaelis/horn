<?php
/** database class definition
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

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/collection.php' ;

class forge
	extends h\object_public
{
	public		function __construct(\mysqli $dbcon)
	{
		parent::__construct() ;
		$this->_handler = $dbcon ;

		if($this->_handler->connect_errno)
			$this->_throw($this->_handler->connect_error) ;
	}

	public		function select(/* $fields = array() */)
	{
		$fields = func_get_args() ;
		$fields = ! func_num_args()
			? h\collection()
			: h\collection($fields) ;
		return new select($fields) ;
	}

	public		function expression($content)
	{
		return new expression($content) ;
	}

	private		$_handler ;
}

class query
	extends h\object_public
{
	protected	$_table ;

	public		function __construct()
	{
		parent::__construct() ;
	}
}

require_once 'horn/lib/collection.php' ;

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
		$q = clone $this ;
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
		$q = clone $this ;
		$q->table = $table ;
		return $q ;
	}

	public		function where(/* $conditions */)
	{
		$q = clone $this ;
		if(func_num_args())
			$q->criteria[] = func_get_args() ;
		return $q ;
	}

	public		function to_literal()
	{
		$pattern = 'select %s from %s' ;
		$fields = count($this->fields)
			? $this->fields->implode(', ')
			: '*' ;
		$select = sprintf($pattern, $fields, $this->table) ;

		if(count($this->criteria))
		{
			$c = $this->criteria[0] ;
			$where = " {$c[0]}={$c[1]}" ;
		}
		else
			$where = '' ;

		return "$select$where" ;
	}
}

class where
	extends h\object_public
{
	public		function __construct(query $query)
	{
		$this->_query = $query ;
		parent::__construct() ;
	}

	public		function or_(expression $expression)
	{
	}

	public		function and_(expression $expression)
	{
	}

	protected	$_query ;
}

