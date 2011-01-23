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

require_once 'horn/lib/regex.php' ;
require_once 'horn/lib/regex-defs.php' ;

abstract class inet
	extends		object_protected
{
	protected	$_literal ;
	protected	$_raw ;
	protected	$_netmask ;
	protected	$_words ;

	const		ERR_UNKNOWN_VERSION = "Unknown IP version [%d]." ;
	const		ERR_BAD_IP = "Bad address IP [%s]." ;

	protected	function __construct(string_ex $literal)
	{
		parent::__construct() ;

		$this->_literal = $literal ;
		$this->_words = new collection ;
		$this->parse() ;
	}

	static		function new_(string_ex $literal, $version=inet_4::VERSION)
	{
		if($version == inet_4::VERSION)
			$inet = new inet_4($literal) ;
		elseif($version == inet_6::VERSION)
			$inet = new inet_6($literal) ;
		else
			throw new exception(sprintf(self::ERR_UNKNOWN_VERSION, $version)) ;

		return $inet ;
	}

	public	function as_integer()
	{
		return (integer) $this->raw ;
	}

	abstract
		protected	function parse() ;
	abstract
		public	function __tostring() ;
}

class inet_4 extends inet
{
	const		VERSION = 4 ;
	const		CLASS_A = 0xa ;
	const		CLASS_B = 0xb ;
	const		CLASS_C = 0xc ;

	public		function __tostring()
	{
		return (string) $this->literal ;
	}

	public		function is_class($class)
	{
		return $this->class == $class ;
	}

	public		function get_class()
	{
		return $this->class ;
	}

	protected	function parse()
	{
		/*
		$exp_ip			= "(%1\$s)\\.(%1\$s)\\.(%1\$s)\\.(%1\$s)" ;
		$exp_0_199		= "(?:1?\d?\d)" ;
		$exp_200_255	= "(?:2(?:5[0-5]|[0-4]\d))" ;
		$exp_0_255		= sprintf("(?:%s|%s)", $exp_0_199, $exp_200_255) ;

		$re = new regex(string_ex::format($exp_ip, $exp_0_255)) ;
		*/

		$re = new regex(RE_INET4) ;
#		die($re->pattern) ;
		
		if(!$re->match($this->literal))
			throw new exception(sprintf(self::ERR_BAD_IP, $this->literal)) ;

		$refs = $re->get_pieces_by_match(0) ;
		$this->words[0] = $this->literal
			->slice($refs[4][0], $refs[4][1])
			->as_integer() ;
		$this->words[1] = $this->literal
			->slice($refs[3][0], $refs[3][1])
			->as_integer() ;
		$this->words[2] = $this->literal
			->slice($refs[2][0], $refs[2][1])
			->as_integer() ;
		$this->words[3] = $this->literal
			->slice($refs[1][0], $refs[1][1])
			->as_integer() ;

		$this->raw = $this->words[0]
			+ ($this->words[1] << 8)
			+ ($this->words[2] << 16)
			+ ($this->words[3] << 24) ;
		
		if($this->raw >> 31 == 0)
			$this->class = self::CLASS_A ;
		elseif(($this->raw >> 29) & ((1 << 2) + (1 << 1)))
			$this->class = self::CLASS_C ;
		elseif(($this->raw >> 30) & (1 << 1))
			$this->class = self::CLASS_B ;
	}

	protected	$_class ;
}

/*
class inet_6 extends inet
{
	const		VERSION = 6 ;
}
*/

?>
