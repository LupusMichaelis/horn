<?php
/** Duration handling
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

require_once 'horn/lib/collection.php' ;

// \todo		refactor to make it lazy
class day_array
	extends		collection
{
	static		function range(date $first, date $last)
	{
		$week = new self ;
		$day = $first ;
		$week->push($day) ;

		do
		{
			$day = $day->tomorrow() ;
			$week->push($day) ;
		}
		while(!$day->is_equal($last)) ;

		return $week ;
	}

	/*
	static		function new_week()
	{
		$week = new self ;
		return $week ;
	}
	*/
}

class week
	extends		day_array
{
	protected	$_week ;
	protected	$_year ;

	static		function of(date $day)
	{
		$year = (int) strftime('%Y', $day->timestamp) ;
		$week = (int) strftime('%V', $day->timestamp) ;

		return new self($year, $week) ;
	}

	public		function __construct($year, $weekno)
	{
		$this->week = $weekno ;
		$this->year = $year ;

		$day = date::new_from_format("$year-W$weekno") ;
		$this->push($day) ;
		while($day->get_day_of_week() != 7)
		{
			$day = $day->tomorrow() ;
			$this->push($day) ;
		}

		// The day can be any day in a week. So we start to push
		// the asked day, then we push comming days, then we revert the day's storage, then we push
		// the passed days until the first, and revert the storage.
		/*
		$aday = $day ;
		$this->push($aday) ;

		while($aday->get_day_of_week() != 7)
		{
			$aday = $aday->tomorrow() ;
			$this->push($aday) ;
		}

		$this->reverse() ;
		$aday = $day ;

		while($aday->get_day_of_week() != 1)
		{
			$aday = $aday->yesterday() ;
			$this->push($aday) ;
		}

		$this->reverse() ;
		*/
	}

	public		function offsetSet($offset, $value)
	{
		throw new exception('Read-only collection') ;
	}

	public		function offsetGet($offset)
	{
		if(!is_integer($offset))
		{
			/// \bug find howto fetch day from locale aware function
			$days = array
				( 'monday' => 0
				, 'tuesday' => 1
				, 'wenesday' => 2
				, 'tuesday' => 3
				, 'friday' => 4
				, 'saturday' => 5
				, 'sunday' => 6
				) ;

			$offset = isset($days[$offset]) ? $days[$offset] : -1 ;
		}

		if(!is_integer($offset) || $offset < 0 || $offset > 6)
			throw new exception('Unknown day') ;

#		$date = date::new_from_format("{$this->year}-W{$this->week} $offset") ;

		return parent::offsetGet($offset) ;
	}

	public		function offsetExits($offset)
	{
		return !($offset < 0 && 7 < $offset) ;
	}
}

