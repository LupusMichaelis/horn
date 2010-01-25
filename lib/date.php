<?php
/** Time handling classes.
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

require_once 'horn/lib/collection.php' ;

// WTF ! Why must I set the timzone, why set the tz is safer than using
// system setting ? Must I set the clock at every scrip invokation ?
date_default_timezone_set('Europe/Paris') ;

class date
	extends		object_public
{
	protected	$_year ;
	protected	$_month ;
	protected	$_day ;

	protected	$_timestamp ;

	public		function __construct($year, $month, $day)
	{
		$this->_day = (int) $day ;
		$this->_month = (int) $month ;
		$this->_year = (int) $year ;

		parent::__construct() ;

		$this->compute_timestamp() ;
	}

	static
	protected	function _clone_object(object_public $copied)
	{
		return new static($copied->year, $copied->month, $copied->day) ;
	}

	/*
	protected	function _check_attributes()
	{
		parent::_check_attributes() ;

		if(!$this->check())
			throw new exception('Date is inconsistent.') ;
	}
	*/

	public		function __tostring()
	{
		return $this->format('%A %d %B %Y') ;
	}

	public		function to_sql()
	{
		return $this->format('%Y-%m-%d') ;
	}

	public		function format($fmt)
	{
		return strftime($fmt
				, mktime(0, 0, 0, $this->month, $this->day, $this->year)) ;
	}

	static
	protected	$today = null ;
	static		function new_today()
	{
		if(is_null(self::$today))
		{
			$aday = getdate() ;
			self::$today = new self($aday['year'], $aday['mon'], $aday['mday']) ;
		}

		return self::$today ;
	}

	static		function new_tomorrow()
	{
		self::new_today() ;
		return self::$today->tomorrow() ;
	}

	static		function new_yesterday()
	{
		self::new_today() ;
		return self::$today->yesterday() ;
	}

	/** \todo think about l10n
	  */
	static		function new_from_format($data)
	{
		$aday = getdate(strtotime($data)) ;
		$new = new self($aday['year'], $aday['mon'], $aday['mday']) ;
		return $new ;
	}

	static		function new_from_sql($sql_date)
	{
		$parsed = date_parse($sql_date) ;

		$new = new self($parsed['year'], $parsed['month'], $parsed['day']) ;
		return $new ;
	}

	public		function is_equal(date $right)
	{
		return $this->day == $right->day
			&& $this->month == $right->month
			&& $this->year == $right->year
			;
	}

	public		function yesterday()
	{
		$aday = getdate(strtotime('yesterday', $this->timestamp)) ;
		$yesterday = new self($aday['year'], $aday['mon'], $aday['mday']) ;
		return $yesterday ;
	}

	public		function tomorrow()
	{
		$aday = getdate(strtotime('tomorrow', $this->timestamp)) ;
		$tomorrow = new self($aday['year'], $aday['mon'], $aday['mday']) ;

		return $tomorrow ;
	}

	public		function christmas()
	{
		$christmas = clone $this ;
		$christmas->day = 24 ;
		$christmas->month = 12 ;

		return $christmas ;
	}

	public		function easterday()
	{
		$aday = getdate(strtotime('easterday', $this->timestamp)) ;
		$easterday = new self($aday['year'], $aday['mon'], $aday['mday']) ;

		return $easterday ;
	}

	public		function week()
	{
		return week::of($this) ;
	}

	public		function check()
	{
		return checkdate($this->month, $this->day, $this->year) ;
	}

	public		function get_day_literal()
	{
		return strftime('%A'
				, mktime(0, 0, 0, $this->month, $this->day, $this->year)) ;
	}

	public		function get_month_literal()
	{
		return strftime('%B'
				, mktime(0, 0, 0, $this->month, $this->day, $this->year)) ;
	}

	public		function get_day_of_week()
	{
		 return (int) strftime('%u'
				, mktime(0, 0, 0, $this->month, $this->day, $this->year)) ;
	}

	protected	function compute_timestamp()
	{
		$this->timestamp = mktime(0, 0, 0, $this->month, $this->day, $this->year) ;
	}
}

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
}

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

class date_time
	extends		object_public
{
	public		function __construct()
	{
		$this->_date = new date ;
		$this->_time = new time_ex ;
	}

	static		function from_date(date $day)
	{
		$new = self ;
		$new->_date->copy($day) ;

		return $new ;
	}

	static		function from_time(time_ex $time)
	{
		$new = self ;
		$new->_time->copy($time) ;

		return $new ;
	}

	static		function from_date_time(date $day, time_ex $time)
	{
		$new = self ;
		$new->_day->copy($day) ;
		$new->_time->copy($time) ;

		return $new ;
	}

	protected	$_date ;
	protected	$_time ;
}


class duration
	extends object_public
{
}

