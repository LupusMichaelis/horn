<?php

/** \file
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

namespace horn;

import('lib/object');

/** Position in screen
 */
class point
	extends object_public
{
	public		$height;
	public		$width;

	static
	public		function create_origin()
	{
		return new point(0, 0);
	}

	public		function __construct($width, $height)
	{
		$this->width = $width;
		$this->height = $height;
	}

	protected	function _clone()
	{
		return new static($this->width, $this->height);
	}
}

class box
	extends object_public
{
	protected	$_top_left;
	protected	$_bottom_right;

	public		function __construct(point $top_left = null, point $bottom_right = null)
	{
		$this->top_left = is_null($top_left) ? new point(0, 0) : $top_left;
		$this->bottom_right = is_null($bottom_right) ? new point(0, 0) : $bottom_right;
	}
}

class line
	extends object_public
{
	protected	$_first;
	protected	$_second;

	public		function __construct(point $first = null, point $second = null)
	{
		$this->first = is_null($first) ? new point(0, 0) : $first;
		$this->second = is_null($second) ? new point(0, 0) : $second;
	}

}

