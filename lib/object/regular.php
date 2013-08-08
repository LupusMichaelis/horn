<?php
/**
 *
 *	\see object\base
 *
 *	object_public, object_protected and object_private are defined becasue you can't
 *	change the constructor accessibility while inheriting
 *
 *  \project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  \copyright	2013, Lupus Michaelis
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

namespace horn\lib\object;
use horn\lib as h;

class regular
	extends base
{
	final
	protected	function _set($name, $value)
	{
		$actual_name = $this->_actual_name($name);

		if($this->$actual_name instanceof self)
			$this->$actual_name->assign($value);
		if(!is_null($this->$actual_name) && is_object($value))
			$this->$actual_name = clone $value;
		else
			$this->$actual_name = $value;
	}

	final
	protected	function & _get($name)
	{
		$actual_name = $this->_actual_name($name);
		return $this->$actual_name;
	}

	final
	protected	function _isset($name)
	{
		try { $actual_name = $this->_actual_name($name); }
		catch(h\exception $e) { return false; }
		return isset($this->$actual_name);
	}

	final
	protected	function _unset($name)
	{
		$actual_name = $this->_actual_name($name);
		$this->$actual_name = null;
	}

}
