<?php
/** Datetime
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2010, Lupus Michaelis
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

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/time/date.php' ;
require_once 'horn/lib/time/time.php' ;

function now()
{
	$aday = getdate() ;
	$now = new date_time ;
	$now->date = new date($aday['year'], $aday['mon'], $aday['mday']) ;
	$now->time = new time($aday['hours'], $aday['minutes'], $aday['seconds']) ;

	return $now ;
}


class date_time
	extends		object_public
{
	public		function __construct()
	{
		$this->_date = new date ;
		$this->_time = new time ;

		parent::__construct() ;
	}

	static		function from_date(date $day)
	{
		$new = self ;
		$new->_date->copy($day) ;

		return $new ;
	}

	static		function from_time(time $time)
	{
		$new = self ;
		$new->time = $time ;

		return $new ;
	}

	static		function from_date_time(date $day, time $time)
	{
		$new = self ;
		$new->_day->copy($day) ;
		$new->_time->copy($time) ;

		return $new ;
	}

	protected	$_date ;
	protected	$_time ;
}

