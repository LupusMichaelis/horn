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

namespace horn\apps ;
use \horn\lib as h ;

h\import('lib/collection') ;
h\import('lib/string') ;

class story_source
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

	public		function insert(story $story)
	{
		$sql = h\string::format(
				'insert into stories (caption, description, created, modified)'
				.'	values (\'%s\', \'%s\', \'%s\', \'%s\')'
				, $this->source->escape($story->title)
				, $this->source->escape($story->description)
				, $this->source->escape(h\string($story->created))
				, $this->source->escape(h\string($story->modified))
				) ;
		$this->source->query($sql) ;
	}

	public		function update(story $story)
	{
		$id = $this->cache->search_first($story) ;
		$sql = h\string::format(
				'update stories set caption = \'%s\''
				.', description = \'%s\''
				.', created = \'%s\''
				.', modified = \'%s\''
				.' where id = %d'
				, $this->source->escape($story->title)
				, $this->source->escape($story->description)
				, $this->source->escape(h\string($story->created))
				, $this->source->escape(h\string($story->modified))
				, $id
				) ;
		$this->source->query($sql) ;
	}

	public		function delete(story $story)
	{
		$id = $this->cache->search_first($story) ;
		$sql = h\string::format('delete from stories where id=%d', $id) ;
		$this->source->query($sql) ;
	}

	public		function get_all()
	{
		$rows = $this->source->query(h\string('select * from stories')) ;
		return $this->stories_from_select($rows) ;
	}

	public		function get_by_title(h\string $title)
	{
		$sql = h\string::format('select * from stories where caption = \'%s\''
				, $this->source->escape($title)) ;
		$rows = $this->source->query($sql) ;
		$stories = $this->stories_from_select($rows) ;

		return isset($stories[0])
			? $stories[0]
			: null ;
	}

	private		function stories_from_select($rows)
	{
		$stories = new stories ;

		foreach($rows as $row)
		{
			if(isset($this->cache[$row['id']]))
				$new = $this->cache[$row['id']] ;
			else
			{
				$new = story::create
					( $row['caption']
					, $row['description']
					, $row['created']
					, $row['modified']
					) ;
				$this->cache[$row['id']] = $new ;
			}

			$stories->push($new) ;
		}

		return $stories ;
	}
}


class story
	extends h\object_public
{
	protected	$_title ;
	protected	$_description ;
	protected	$_created ;
	protected	$_modified ;

	// public	$owner ;
	public		function __construct()
	{
		$this->_title = new h\string ;
		$this->_description = new h\string ;
		$this->_created = h\now() ;
		$this->_modified = h\now() ;

		parent::__construct() ;
	}

	static
	public		function create($title, $description, $created, $modified)
	{
		$new = new static ;
		$new->title = h\string($title) ;
		$new->description = h\string($description) ;
		$new->created = h\date_time::from_date(h\date::new_from_sql($created)) ;
		$new->modified = h\date_time::from_date(h\date::new_from_sql($modified)) ;

		return $new ;
	}
}

class stories
	extends h\collection
{
}

