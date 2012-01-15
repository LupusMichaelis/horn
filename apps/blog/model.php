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

	public		function __construct(h\db\database $db)
	{
		$this->_source = $db ;
		parent::__construct() ;
	}

	public		function get_all()
	{
		$rows = $this->source->query(h\string('select * from stories')) ;

		$stories = new stories ;

		foreach($rows as $row)
			$stories->push(story::create
				( $row['caption']
				, $row['description']
				, $row['created']
				, $row['modified']
				)
			) ;

		return $stories ;
	}

	public		function get_by_title(h\string $title)
	{
		$sql = h\string::format('select * from stories where caption = \'%s\'', $title) ;
		$rows = $this->source->query($sql) ;

		$stories = new stories ;

		foreach($rows as $row)
			$stories->push(story::create
				( $row['caption']
				, $row['description']
				, $row['created']
				, $row['modified']
				)
			) ;

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
		$this->title = new h\string ;
		$this->description = new h\string ;
		$this->created = new h\date_time ;
		$this->modified = new h\date_time ;

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

