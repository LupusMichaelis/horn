<?php
/** 
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

namespace horn\lib ;

final
class module
{
	// Stack loaded files. Default values are set to avoid conflicts.
	static
	private		$_loaded = array('horn/lib/horn.php', 'horn/lib/module.php') ;

	static
	private		$_include_path = 'horn/' ;

	static
	public		function load_file($file_name)
	{
		if(in_array($file_name, self::$_loaded))
			return ;

		self::$_loaded[] = $file_name ;
		require $file_name ;
	}

	static
	public		function load($module_name)
	{
		//$module_name = implode('/', explode('.', $module_name)) ;
		self::load_file(self::$_include_path . $module_name . '.php') ;
	}
}

function import($module)
{
	return module::load($module) ;
}


