<?php
/** Domain model for blog
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

namespace horn\apps\blog;
use \horn\lib as h;

h\import('lib/collection');
h\import('lib/text');
h\import('lib/model');

class account_data
	extends h\model_data
{
	const name = 'account';

	public		function get_all()
	{
		$db = $this->model->services->get('db');

		$rows = $db->query(h\text('select * from accounts'));
		return $this->create_accounts_from_select($rows);
	}

	public		function get_by_name(h\text $name)
	{
		$db = $this->model->services->get('db');

		$sql = h\text::format('select * from accounts where name = %s'
				, $db->escape($name));
		$rows = $db->query($sql);
		$accounts = $this->create_accounts_from_select($rows);

		return isset($accounts[0])
			? $accounts[0]
			: null;
	}

	public		function delete_by_name(h\text $name)
	{
		$db = $this->model->services->get('db');

		$sql = h\text::format('delete from accounts where name=%s'
				, $db->escape($name));
		return $db->query($sql);
	}

	public		function insert(account $account)
	{
		$db = $this->model->services->get('db');

		$sql = h\text::format(
				'insert into accounts (name, email, created, modified)'
				.'	values (%s, %s, %s, %s)'
				, $db->escape($account->name)
				, $db->escape($account->email)
				, $db->escape($account->created->format(h\date::FMT_YYYY_MM_DD))
				, $db->escape($account->modified->format(h\date::FMT_YYYY_MM_DD))
				);
		$db->query($sql);
	}

	public		function update(account $account)
	{
		$db = $this->model->services->get('db');

		$id = $this->cache->search_first($account);
		$sql = h\text::format(
				'update accounts set name = %s'
				.', email = %s'
				.', created = %s'
				.', modified = %s'
				.' where id = %d'
				, $db->escape($account->name)
				, $db->escape($account->email)
				, $db->escape($account->created->format(h\date::FMT_YYYY_MM_DD))
				, $db->escape($account->modified->format(h\date::FMT_YYYY_MM_DD))
				, $id
				);
		$db->query($sql);
	}

	public		function delete(account $account)
	{
		$db = $this->model->services->get('db');

		$id = $this->cache->search_first($account);
		$sql = h\text::format('delete from stories where id=%d', $id);
		$db->query($sql);
	}

	private		function create_accounts_from_select($rows)
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

class story_data
	extends h\model_data
{
	const name = 'story';

	private		function insert(story $story)
	{
		$db = $this->model->services->get('db');

		$sql = h\text::format(
				'insert into stories (caption, description, created, modified)'
				.'	values (%s, %s, %s, %s)'
				, $db->escape($story->title)
				, $db->escape($story->description)
				, $db->escape($story->created->format(h\date::FMT_YYYY_MM_DD))
				, $db->escape($story->modified->format(h\date::FMT_YYYY_MM_DD))
				);
		$db->query($sql);
	}

	public		function update(story $story)
	{
		$db = $this->model->services->get('db');

		$id = $this->cache->search_first($story);
		$sql = h\text::format(
				'update stories set caption = %s'
				.', description = %s'
				.', created = %s'
				.', modified = %s'
				.' where id = %d'
				, $db->escape($story->title)
				, $db->escape($story->description)
				, $db->escape($story->created->format(h\date::FMT_YYYY_MM_DD))
				, $db->escape($story->modified->format(h\date::FMT_YYYY_MM_DD))
				, $id
				);
		$db->query($sql);
	}

	public		function delete(story $story)
	{
		$db = $this->model->services->get('db');

		$id = $this->cache->search_first($story);
		$sql = h\text::format('delete from stories where id=%d', $id);
		$db->query($sql);
	}

	public		function delete_by_title(h\text $title)
	{
		$db = $this->model->services->get('db');

		$sql = h\text::format('delete from stories where caption=%s'
				, $db->escape($title));
		return $db->query($sql);
	}

	public		function get_all()
	{
		$db = $this->model->services->get('db');

		$rows = $db->query(h\text('select * from stories'));
		return $this->create_stories_from_select($rows);
	}

	public		function get_by_title(h\text $title)
	{
		$db = $this->model->services->get('db');

		$sql = h\text::format('select * from stories where caption = %s'
				, $db->escape($title));
		$rows = $db->query($sql);
		$stories = $this->create_stories_from_select($rows);

		return isset($stories[0])
			? $stories[0]
			: null;
	}

	public		function get_by_legacy_path(h\text $legacy_path)
	{
		$db = $this->model->services->get('db');

		$sql = h\text::format
			('select * from stories s right join legacy_stories ls'
				.'	on s.id = ls.story_id where path = %s'
				, $db->escape($legacy_path));
		$rows = $db->query($sql);
		$stories = $this->create_stories_from_select($rows);

		return isset($stories[0])
			? $stories[0]
			: null;
	}

	private		function create_stories_from_select($rows)
	{
		$stories = new stories;

		foreach($rows as $row)
		{
			if(isset($this->cache[$row['id']]))
				$new = $this->cache[$row['id']];
			else
			{
				$new = story::create
					( $row['caption']
					, $row['description']
					, $row['created']
					, $row['modified']
					);
				$this->cache[$row['id']] = $new;
			}

			$stories->push($new);
		}

		return $stories;
	}
}

class model
	extends h\model
{
	protected	$_source;

	public		function __construct(h\service_provider $service)
	{
		$this->_source = $service->get('db');

		parent::__construct($service);

		$this->add_data(new story_data($this));
		$this->add_data(new account_data($this));
	}

	public		function is_user_granted(story $story, h\http\user $user, h\acl $rights = null)
	{
		// XXX By default, deny access
		return false;
	}

	public		function delete_story_by_title(h\text $title)
	{
		return $this->data['story']->delete_by_title($title);
	}

	public		function get_stories_all()
	{
		return $this->data['story']->get_all();
	}

	public		function get_accounts_all()
	{
		return $this->data['account']->get_all();
	}

	public		function get_story_by_title(h\text $title)
	{
		return $this->data['story']->get_by_title($title);
	}

	public		function get_story_by_legacy_path(h\text $legacy_path)
	{
		return $this->data['story']->get_by_legacy_path($legacy_path);
	}
}

class story
	extends h\object_public
{
	protected	$_title;
	protected	$_description;
	protected	$_created;
	protected	$_modified;

	// public	$owner;
	public		function __construct()
	{
		$this->_title = new h\text;
		$this->_description = new h\text;
		$this->_created = h\today();
		$this->_modified = h\today();

		parent::__construct();
	}

	static
	public		function create($title, $description, $created, $modified)
	{
		$new = new static;
		$new->title = h\text($title);
		$new->description = h\text($description);
		$new->created = h\date::new_from_sql($created);
		$new->modified = h\date::new_from_sql($modified);

		return $new;
	}
}

class stories
	extends h\collection
{
}


class account
	extends h\object_public
{
	protected	$_name;
	protected	$_email;
	protected	$_created;
	protected	$_modified;

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
		$new->created = h\date::new_from_sql($created); // XXX or null
		$new->modified = h\date::new_from_sql($modified); // XXX or null

		return $new;
	}
}

class accounts
	extends h\collection
{
}

