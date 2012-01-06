<?php
/** query class definition
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2011, Lupus Michaelis
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
namespace horn\lib\sql ;
use \horn\lib as h ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/collection.php' ;

require_once 'horn/lib/sql/where.php' ;

class query
	extends h\object_public
{
	protected	$_table ;
	protected	$_where ;

	private		$_inplace = false;

	public		function __construct($inplace = null)
	{
		parent::__construct() ;
		$this->_inplace = $inplace ;
	}

	protected	function q()
	{
		return $this->_inplace ? $this : clone $this ;
	}

	public		function where($operand)
	{
		$q = $this->q() ;
		$q->where = new where($operand) ;
		return $q ;
	}

	public		function equals($operand)
	{
		$q = $this->q() ;
		$q->where->equals($operand) ;
		return $q ;
	}

	public		function in($list)
	{
		$q = $this->q() ;
		$q->where->in($list) ;
		return $q ;
	}
}

