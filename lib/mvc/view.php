<?php

/** View base component for a MVC like implementation.
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


namespace horn ;

class view
	extends object_public
{
	protected	$_childs ;
	protected	$_template ;
	protected	$_model ;

	public		function __construct($template)
	{
		$this->_childs = new collection ;
		$this->_template = $template ;
	}

	public		function __tostring()
	{
		try {
			return $this->_draw() ;
		} catch (\exception $e) {
			// \todo find a smarter way to handle excptions in this fucking __tostring
			trigger_error($e) ;
		}
	}

	/*
	abstract
	protected	function _set_model(object_base $model) ;
	*/

	protected	function _draw()
	{
		$path = $this->template ;
		if(!is_file($path))
			// XXX Note that throwing in __tostring isn't allowed
			$this->_throw_format('Can\'t open template file \'%s\'.', $this->template) ;

		ob_start() ;
		include $path ;
		return ob_get_clean() ;
	}
}

class html_fragment
	extends view
{

	public		function __construct($template)
	{
		$this->template = $template ;
	}

	protected	function _set_model(collection $model)
	{
		return $this->_model = $model ;
	}
}

