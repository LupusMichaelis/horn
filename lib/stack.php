<?php
/** stack class definition
 *	A stack is an array without hashmap capabilities.
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
namespace horn\lib;

import('lib/object');
import('lib/collection');

class stack
	extends collection
{
	/** Get a copy of the stack as a stack.
	 *	\return array
	 */
	public		function get_stack()
	{
		return array_values($this->_stack);
	}

	public		function offsetUnset($key)
	{
		parent::offsetUnset($key);
		$this->_stack = array_values($this->_stack);
	}

	public		function offsetSet($key, $value)
	{
		$key = $this->filter_key($key);

		if($key > count($this))
			$this->_stack = array_pad($this->_stack, $key, null);

		return parent::offsetSet($key, $value);
	}
}

