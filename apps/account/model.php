<?php
/** Domain model for account
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

namespace horn\apps\account ;
use \horn\lib as h ;

h\import('lib/collection') ;
h\import('lib/string') ;

class source
	extends h\object_public
{
	protected	$_source ;

	private		$cache ;

	public		function __construct(h\db\database $db)
	{
		$this->_source = $db ;
		$this->cache = h\collection() ;
		parent::__construct() ;
	}

	public		function insert(account $account)
	{
		$sql = h\string::format(
				'insert into accounts (name, email, created, modified)'
				.'	values (\'%s\', \'%s\', \'%s\', \'%s\')'
				, $this->source->escape($account->name)
				, $this->source->escape($account->emain)
				, $this->source->escape(h\string($account->created))
				, $this->source->escape(h\string($account->modified))
				) ;
		$this->source->query($sql) ;
	}

	public		function update(account $account)
	{
		$id = $this->cache->search_first($account) ;
		$sql = h\string::format(
				'update accounts set name = \'%s\''
				.', email = \'%s\''
				.', created = \'%s\''
				.', modified = \'%s\''
				.' where id = %d'
				, $this->source->escape($account->name)
				, $this->source->escape($account->email)
				, $this->source->escape(h\string($account->created))
				, $this->source->escape(h\string($account->modified))
				, $id
				) ;
		$this->source->query($sql) ;
	}

	public		function delete(account $account)
	{
		$id = $this->cache->search_first($account) ;
		$sql = h\string::format('delete from accounts where id=%d', $id) ;
		$this->source->query($sql) ;
	}

	public		function get_all()
	{
		$rows = $this->source->query(h\string('select * from accounts')) ;
		return $this->accounts_from_select($rows) ;
	}

	public		function get_by_name(h\string $name)
	{
		$sql = h\string::format('select * from accounts where name = \'%s\''
				, $this->source->escape($name)) ;
		$rows = $this->source->query($sql) ;
		$accounts = $this->accounts_from_select($rows) ;

		return isset($accounts[0])
			? $accounts[0]
			: null ;
	}

	public		function get_by_email(h\string $email)
	{
		$sql = h\string::format('select * from accounts where email = \'%s\''
				, $this->source->escape($email)) ;
		$rows = $this->source->query($sql) ;
		$accounts = $this->accounts_from_select($rows) ;

		return isset($accounts[0])
			? $accounts[0]
			: null ;
	}

	private		function accounts_from_select($rows)
	{
		$accounts = new accounts ;

		foreach($rows as $row)
		{
			if(isset($this->cache[$row['id']]))
				$new = $this->cache[$row['id']] ;
			else
			{
				$new = account::create
					( $row['name']
					, $row['email']
					, $row['created']
					, $row['modified']
					) ;
				$this->cache[$row['id']] = $new ;
			}

			$accounts->push($new) ;
		}

		return $accounts ;
	}
}


class account
	extends h\object_public
{
	protected	$_name ;
	protected	$_email ;
	protected	$_created ;
	protected	$_modified ;

	// public	$owner ;
	public		function __construct()
	{
		$this->_name = new h\string ;
		$this->_email = new h\string ;
		$this->_created = h\now() ;
		$this->_modified = h\now() ;

		parent::__construct() ;
	}

	static
	public		function create($name, $email, $created, $modified)
	{
		$new = new static ;
		$new->name = h\string($name) ;
		$new->email = h\string($email) ;
		$new->created = h\date_time::from_date(h\date::new_from_sql($created)) ;
		$new->modified = h\date_time::from_date(h\date::new_from_sql($modified)) ;

		return $new ;
	}
}

class accounts
	extends h\collection
{
}

