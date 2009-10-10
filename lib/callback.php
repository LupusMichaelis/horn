<?php
/** callback
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

require_once 'horn/lib/horn.php' ;
require_once 'horn/lib/object.php' ;
require_once 'horn/lib/collection.php' ;

/** Can be call with two or one parameter
 *
 */
function callback()
{
	$args = func_get_args() ;
	switch(count($args))
	{
		case 1: return new callback($args[0]) ;
		case 2: return new callback(array($args[0], $args[1])) ;
		default: throw new \exception('No callback supplied.') ;
	}
}

/**
 */
class callback
	extends object_public
{
	/**
	 */
	public		function __construct($native)
	{
		parent::__construct() ;
		$this->native = $native ;
	}

	/**
	 */
	public		function __invoke()
	{
		return call_user_func_array($this->native, func_get_args()) ;
	}

	protected	function _set_native($native)
	{
		if(!is_callable($native))
			$this->_throw_bad_callback($native) ;

		$this->_native = $native ;
	}

	protected	function _throw_bad_callback($native)
	{
		$desc = dump($native) ;
		$this->_throw_format('Bad callback \'%s\' supplied.', $desc) ;
	}

	/**
	 */
	protected	$_native ;
}

