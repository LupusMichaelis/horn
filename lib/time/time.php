<?php
/** Time handling
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


class time
	extends		object_public
{
	protected	$_second ;
	protected	$_minute ;
	protected	$_hour ;

	public		function __construct($second = null, $minute = 0, $hour = 0)
	{
		if(is_null($second))
			$second = time() ;

		$this->second = $second ;
		$this->minute = $minute ;
		$this->hour = $hour ;
	}

	protected	function _to_string()
	{
		return sprintf('%02d:%02d:%02d', $this->_hour, $this->_minute, $this->_second) ;
	}
}

class duration
	extends object_public
{
}

