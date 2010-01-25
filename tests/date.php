<?php

/** ?
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

require_once 'horn/lib/date.php' ;

/*
$birthday = new date(1980, 12, 22) ;
assert($birthday instanceof date) ;
assert($birthday->check()) ;
assert($birthday->day == 22) ;
assert($birthday->month == 12) ;
assert($birthday->year == 1980) ;
*/

$week = $birthday->week() ;

assert($week instanceof week) ;
assert($week->count() == 7) ;

assert($week[0] instanceof date) ;
assert($week[6] instanceof date) ;

assert($week->count() == 7) ;
assert(!isset($week[7])) ;
try { $day = $week[7] ; assert(false) ; } catch(exception $e) { }

assert($week['monday'] instanceof date) ;
try { $week['mon'] ; assert(false) ; } catch(exception $e) { }

assert($week['monday'] == $birthday) ;

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



