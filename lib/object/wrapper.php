<?php
/**
 *	Object coherent handling.
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

class wrapper
	extends		h\object\base
{
	private		$impl;

	public		function set_impl(h\object\base $impl)
	{
		if(!$this->is_supported($impl))
			throw $this->_exception_format('Implementation \'%s\' not supported'
					, get_class($impl));

		$this->impl = $impl;
	}

	private		function &get_impl()
	{
		if(is_null($this->impl))
			throw $this->_exception_impl_is_no_set();

		return $this->impl;
	}

	protected	function is_supported(h\object\base $impl)
	{
		return true;
	}

	final
	protected	function _set($name, $value)
	{
		$this->get_impl()->$name = $value;
	}

	final
	protected	function _unset($name)
	{
		unset($this->get_impl()->$name);
	}

	final
	protected	function _isset($name)
	{
		return isset($this->get_impl()->$name);
	}

	final
	protected	function &_get($name)
	{
		$buffer = $this->get_impl()->$name;
		return $buffer;
	}

	final
	protected	function &_call($fn_name, $fn_args)
	{
		$buffer = call_user_func_array(array($this->get_impl(), $fn_name), $fn_args);
		return $buffer;
	}

	public		function _exception_impl_is_no_set()
	{
		return $this->_exception('The implementation isn\'t set');
	}
}

