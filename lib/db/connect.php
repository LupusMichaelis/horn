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

namespace horn\lib\db;
use horn\lib as h;

h\import('lib/object');

const MYSQL = 'mysql';
const COMPOSITE = 'composite';
const MEMCACHE = 'memcache';

function open($specification)
{
	$factory = new database_factory($specification);
	$con = $factory->create();

	$con->open();
	return $con;
}

class database_factory
	extends h\object_public
{
	protected	$_specification;

	public		function __construct($specification)
	{
		$this->_specification = $specification;
		parent::__construct();
	}

	public		function create()
	{
		$db = $this->build();
		return $db;
	}

	private		function build()
	{
		$type = $this->specification['type'];
		if($type === COMPOSITE)
			$db = $this->build_composite($this->specification);
		else if($type === MYSQL)
			$db = new database_mysql($this->specification);
		else
			throw $this->_exception_format('Unknown database type \'%s\'', $type);

		return $db;
	}
}

abstract
class database
	extends h\object_public
{
	protected	$_specification;

	public		function __construct($specification)
	{
		$this->_specification = $specification;
		parent::__construct();
	}

	abstract
	public		function open();

	abstract
	public		function close();
}



class database_mysql
	extends database
{
	private		$_con;
	protected	$_charset;

	public		function open()
	{
		if(null !== $this->_con)
			throw $this->_exception('Database already connected');

		@ /* XXX XXX XXX XXX */
		$this->_con = new \mysqli
			( $this->specification['host']
			, $this->specification['user']
			, $this->specification['password']
			, $this->specification['base']
			);

		if($this->_con->connect_errno)
			throw $this->_exception($this->_con->connect_error);

		if(array_key_exists('charset', $this->specification))
			$this->charset = $this->specification['charset'];
	}

	protected	function &_get_affected_rows()
	{
		$affected_rows = $this->_con->affected_rows;
		return $affected_rows;
	}

	protected	function &_get_insert_id()
	{
		$insert_id = $this->_con->insert_id;
		return $insert_id;
	}

	protected	function _exception_query_error()
	{
		return $this->_exception($this->_con->error);
	}

	protected	function _set_charset($charset)
	{
		/// XXX check charset
		$this->_charset = $charset;
		$this->_con->set_charset($charset);
	}

	protected	function &_get_charset()
	{
		if(is_null($this->_charset))
			$this->_charset = $this->_con->get_charset();

		return $this->_charset;
	}

	public		function close()
	{
		$this->_con->close();
		$this->_con = null;
	}

	public		function query(h\text $sql)
	{
		$result = $this->_con->query($sql);
		if($result === false)
			throw $this->_exception_query_error();

		if(is_array($result))
		{
			$return = h\collection();

			while($row = $result->fetch_assoc())
				$return->push($row);
		}
		else
			$return = $result;

		return $return;
	}

	public		function escape(h\text $sql, $is_nullable=false)
	{
		if(h\text('null')->is_equal($sql))
			if(false === $is_nullable)
				throw $this->_exception('Value is not nullable');
			else
				$escaped = h\text('null');
		else
			$escaped = h\text::format('\'%s\'', $this->_con->real_escape_string($sql->scalar));

		return $escaped;
	}

	public		function escape_json(h\text $sql, $is_nullable=false)
	{
		if(h\text('null')->is_equal($sql))
			if(false === $is_nullable)
				throw $this->_exception('Value is not nullable');
			else
				$escaped = h\text('null');
		else
			$escaped = h\text::format('\'%s\'', $this->_con->real_escape_string(json_encode($sql->scalar)));

		return $escaped;
	}
}


