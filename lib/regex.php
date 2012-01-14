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

import('lib/string') ;

class regex
	extends		object_public
{
	protected	$_pattern ;
	protected	$_delimiter ;
	protected	$_results ;

	protected	$_matches ;

	public		function __construct($pattern, $delimiter='`')
	{
		$this->delimiter = $delimiter ;

		// \todo check for delimiters in the pattern, and escape them

		if($pattern instanceof string)
			$this->pattern = clone $pattern ;
		if(is_null($pattern))
			$this->pattern = new string ;
		else
			$this->pattern = new string((string) $pattern) ;
	}

	public		function match(string $subject)
	{
		$pattern = sprintf("%1\$s%2\$s%1\$s", $this->delimiter, $this->pattern) ;
		$result = preg_match_all
			( $pattern
			, $subject
			, $this->_matches
			, PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER) > 0 ;
#		var_dump($this->matches) ;

		return $result ;
	}

	public		function get_matches()
	{
#		var_dump($this->matches) ;
		return $this->get_result(0) ;
	}

	public		function get_result($offset)
	{
		$submatches = new collection ;
		if(array_key_exists($offset, $this->matches))
			foreach($this->matches[$offset] as $name => $match)
			{
				$begin = $match[1] ;
				if($begin < 0)
					$submatches[$name] = null ;
				else
				{
					$end = $begin + strlen($match[0]) ;
					$submatches[$name] = new collection($begin, $end) ;
				}
			}

		return $submatches ;
	}

	public		function get_pieces_by_match($offset)
	{
#		var_dump(__FUNCTION__, $offset) ; die('here') ;
		$pieces = new collection ;

#		var_dump($this->matches) ;

		foreach($this->matches as $name => $result)
		{
			$pieces[$name] = null ;

			// no matches for this set, so proceding
			if(!is_array($result))
				continue ;

			if(!array_key_exists($offset, $result))
				continue ;

			$match = $result[$offset] ;
			if(!is_array($match))
				continue ;

			$begin = $match[1] ;
			$end = $begin + strlen($match[0]) ;
			if($begin > -1 and $end > -1)
				$pieces[$name] = new collection($begin, $end) ;
			else
				$pieces[$name] = null ;
		}

		return $pieces ;
	}
}


