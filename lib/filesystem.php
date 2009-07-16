<?php
/** \file
 *	Filesystem management
 *
 *	object_public, object_protected and object_private are defined becasue you can't
 *	change the 
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

/** \package horn
 */
namespace horn ;

require_once 'horn/lib/object.php' ;

const KILOBI = 1024 ;
const MEGABI = 1048576 ;

class path
	extends		object_public
{
	public		function __construct(string_ex $path)
	{
		$this->_path = $path->explode('/') ;
	}

	protected	function _set_path(string $path)
	{
	}

	protected	$_path ;
}

class filesystem
{
	static
	public		function exists($filename)
	{
	}
}

