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

/** Wrapper on SQL database. Wrap only MySQL for now.
 */
class database
	extends h\object_public
{
	public		function __construct($host, $space, $user, $pass)
	{
		parent::__construct() ;
		$this->_handler = new \mysqli($host, $user, $pass, $space) ;

		if($this->_handler->connect_errno)
			$this->_throw($this->_handler->connect_error) ;
	}

	public		function select(/* $fields = array() */)
	{
		$fields = func_get_args() ;
		$fields = is_null($fields)
			? h\collection()
			: h\collection($fields) ;
		return new select($fields) ;
	}

	public		function expression($content)
	{
		return new expression($content) ;
	}

	public		function execute(query $query)
	{
		$sql = $query->to_literal($this) ;
		$this->_handler->query($sql) ;
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

	public		function __construct(h\collection $fields = null)
	{
		$this->_fields = h\collection() ;
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

	public		function where($condition, $and = true)
	{
		$q = clone $this ;
		if($and) $q->where->and($condition) ; else $q->where->or($condition) ;
		return $q ;
	}

	public		function to_literal(database $db)
	{
		$pattern = 'select %s from %s' ;
		$fields = count($this->fields)
			? $this->fields->implode(', ')
			: '*' ;
		$query = sprintf($pattern, $fields, $this->table) ;
		return $query ;
	}
}

class where
	extends h\object_public
{
	public		function or_(expression $expression)
	{
	}

	public		function and_(expression $expression)
	{
	}
}


$db = new database('localhost', 'test', 'test', 'test') ;

$query = $db->select()
	->from('pictures') ;
echo $query->to_literal($db) ;

