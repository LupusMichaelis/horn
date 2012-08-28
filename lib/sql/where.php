<?php
/** where class definition
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

h\import('lib/object') ;
h\import('lib/collection') ;

class where
	extends h\object_public
{
	protected $_stack ;

	public		function __construct($operand)
	{
		$this->_stack = h\collection() ;
		parent::__construct() ;
		$this->stack[] = $operand ;
	}

	public		function equals($operand)
	{
		$this->stack[] = '=' ;
		$this->stack[] = $operand ;

		return $this ;
	}

	public		function in(h\collection $list)
	{
		$this->stack[] = ' in ' ;
		$this->stack[] = '('.$list->implode(', ').')' ;

		return $this ;
	}

	protected	function _to_string()
	{
		return $this->stack->implode('') ;
	}

	/*
		if(count($this->criteria) > 0)
		{
			$where = ' where ' ;
			foreach($this->criteria as $c)
			{
				$c = $this->criteria[0] ;
				$where = " where {$c[0]}={$c[1]}" ;
			}
	*/
}

