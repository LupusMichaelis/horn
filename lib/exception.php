<?php
/** \file
 *	Exception 
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
namespace horn\lib;

function throw_format($fmt /*, $args*/)
{
	$msg = call_user_func_array('sprintf', func_get_args());
	throw new exception($msg, null);
}

/**
 *
 */
class exception
	extends \exception
{
	private		$throwee;

	public		function __construct($msg, $that)
	{
		parent::__construct($msg);
		$this->throwee = $that;
	}
}

class exception_method_not_exists extends exception { }

