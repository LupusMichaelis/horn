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

namespace horn\apps\account;
use \horn\lib as h;

h\import('lib/collection');
h\import('lib/text');

class source
	extends h\object_public
{
	protected	$_source;

	private		$cache;

	public		function __construct(h\db\database $db)
	{
		$this->_source = $db;
		$this->cache = h\collection();
		parent::__construct();
	}

	public		function insert(account $account)
	{
		$sql = h\text::format(
				'insert into accounts (name, email, created, modified)'
				.'	values (%s, %s, %s, %s)'
				, $this->source->escape($account->name)
				, $this->source->escape($account->email)
				, $this->source->escape($account->created->format(h\date::FMT_YYYY_MM_DD))
				, $this->source->escape($account->modified->format(h\date::FMT_YYYY_MM_DD))
				);
		$this->source->query($sql);
	}

	public		function update(account $account)
	{
		$id = $this->cache->search_first($account);
		$sql = h\text::format(
				'update accounts set name = %s'
				.', email = %s'
				.', created = %s'
				.', modified = %s'
				.' where id = %d'
				, $this->source->escape($account->name)
				, $this->source->escape($account->email)
				, $this->source->escape($account->created->format(h\date::FMT_YYYY_MM_DD))
				, $this->source->escape($account->modified->format(h\date::FMT_YYYY_MM_DD))
				, $id
				);
		$this->source->query($sql);
	}

	public		function delete(account $account)
	{
		$id = $this->cache->search_first($account);
		$sql = h\text::format('delete from accounts where id=%d', $id);
		$this->source->query($sql);
	}

	public		function get_all()
	{
		$rows = $this->source->query(h\text('select * from accounts'));

		return $this->accounts_from_select($rows);
	}

	public		function get_by_name(h\text $name)
	{
		$sql = h\text::format('select * from accounts where name = %s'
				, $this->source->escape($name));
		$rows = $this->source->query($sql);
		$accounts = $this->accounts_from_select($rows);

		return isset($accounts[0])
			? $accounts[0]
			: null;
	}

	public		function get_by_email(h\text $email)
	{
		$sql = h\text::format('select * from accounts where email = %s'
				, $this->source->escape($email));
		$rows = $this->source->query($sql);
		$accounts = $this->accounts_from_select($rows);

		return isset($accounts[0])
			? $accounts[0]
			: null;
	}

	private		function accounts_from_select($rows)
	{
		$accounts = new accounts;

		foreach($rows as $row)
		{
			if(isset($this->cache[$row['id']]))
				$new = $this->cache[$row['id']];
			else
			{
				$new = account::create
					( $row['name']
					, $row['email']
					, $row['created']
					, $row['modified']
					);
				$this->cache[$row['id']] = $new;
			}

			$accounts->push($new);
		}

		return $accounts;
	}
}


class account
	extends h\object_public
{
	protected	$_name;
	protected	$_email;
	protected	$_created;
	protected	$_modified;

	// public	$owner;
	public		function __construct()
	{
		$this->_name = new h\text;
		$this->_email = new h\text;
		$this->_created = h\today();
		$this->_modified = h\today();

		parent::__construct();
	}

	static
	public		function create($name, $email, $created, $modified)
	{
		$new = new static;
		$new->name = h\text($name);
		$new->email = h\text($email);
		$new->created = h\date::new_from_sql($created);
		$new->modified = h\date::new_from_sql($modified);

		return $new;
	}
}

class accounts
	extends h\collection
{
}

