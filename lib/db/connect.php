<?php
/** Provide generic facility to connect a data source
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

namespace horn\lib\db ;
use horn\lib as h ;

require_once 'horn/lib/object.php' ;

const MYSQL = 'mysql' ;
const COMPOSITE = 'composite' ;

function connect($specification)
{
	$factory = new database_factory($specification) ;
	$con = $factory->create() ;

	$con->connect() ;
	return $con ;
}

class database_factory
	extends h\object_public
{
	protected	$_specification ;

	public		function __construct($specification)
	{
		$this->_specification = $specification ;
		parent::__construct() ;
	}

	public		function create()
	{
		$db = $this->build() ;
		$db->connect() ;
		return $db ;
	}

	private		function build()
	{
		$type = $this->specification['type'] ;
		if($type === COMPOSITE)
			$db = $this->build_composite($this->specification) ;
		else if($type === MYSQL)
			$db = new database_mysql($this->specification) ;
		else
			$this->_throw_format('Unknown database type \'%s\'', $type) ;

		return $db ;
	}
}

abstract
class database
	extends h\object_public
{
	protected	$_specification ;

	public		function __construct($specification)
	{
		$this->_specification = $specification ;
		parent::__construct() ;
	}

	abstract
	public		function connect() ;
}



class database_mysql
	extends database
{
	private		$_con ;

	public		function connect()
	{
		$this->_con = new \mysqli
			( $this->specification['host']
			, $this->specification['user']
			, $this->specification['password']
			, $this->specification['base']
			) ;
	}
}

