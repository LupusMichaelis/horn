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
import('lib/graph_geometry');

class coordinate
	extends object_public
{
	public		$title;
	public		$unit;
}

/**
 *	\todo	generalize to have dimension independent systems
 */
class system
	extends object_public
{
	protected	$_abscisse;
	protected	$_ordinate;

	public		function __construct()
	{
		$this->_abscisse = new coordinate;
		$this->_ordinate = new coordinate;
	}
}

/** 
 */
class graph
	extends object_public
{
	protected	$_title;
	protected	$_datas = array();
	protected	$_system;

	protected	$_max_x = 0;
	protected	$_min_x = 0;
	protected	$_max_y = 0;
	protected	$_min_y = 0;

	public		function __construct($title)
	{
		$this->title = $title;
		$this->_system = new system;
	}

	protected	function _set_datas($datas)
	{
		assert(is_array($datas));

		$stored_datas = array();

		foreach($datas as $x => $y)
		{
			$this->_max_x = max($this->_max_x, $x);
			$this->_min_x = min($this->_min_x, $x);
			$this->_max_y = max($this->_max_y, $y);
			$this->_min_y = min($this->_min_y, $y);

			$point = new point($x, $y);
			$stored_datas[] = $point;
		}

		$this->_set('datas', $stored_datas);
	}

	protected	function _set_max_x($value)
	{
		throw $this->_exception_readonly_attribute('max_x');
	}

	protected	function _set_min_x($value)
	{
		throw $this->_exception_readonly_attribute('max_x');
	}

	protected	function _set_max_y($value)
	{
		throw $this->_exception_readonly_attribute('max_y');
	}

	protected	function _set_min_y($value)
	{
		throw $this->_exception_readonly_attribute('max_y');
	}

}

