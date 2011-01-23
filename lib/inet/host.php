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

namespace horn ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/inet.php' ;
require_once 'horn/lib/regex.php' ;
require_once 'horn/lib/regex-defs.php' ;

/**
 */
class host
	extends		object_public
{
	const		ERR_IP_BAD	= 'Host IP address\'s not valid.' ;

	protected	$_name ;
	protected	$_ip ;

	protected	$_reverse ;
	/** \bug PHP don't handle unsigned integer /!\ */

	public		function __construct(string_ex $literal)
	{
		parent::__construct() ;

		$this->_name = new string_ex ;

		/** \todo RE_INET6_S
		$re = new regex(string_ex::format
				("(?<inet4>%s)|(?<inet6>%s)|(?<host>%s)"
				, RE_INET4_S, RE_INET6_S, RE_HOST)) ;
		*/
		$re = new regex(string_ex::format
				("(?<inet4>%s)|(?<host>%s)"
				, RE_INET4_S, RE_HOST)) ;
		if($re->match($literal))
		{
			$result = $re->get_pieces_by_match(0) ;
			if(!is_null($r = $result['inet4']))
				$this->_ip = inet::new_($literal->slice($r[0], $r[1]), inet_4::VERSION) ;
			elseif(!is_null($r = $result['inet6']))
				$this->_ip = inet::new_($literal->slice($r[0], $r[1]), inet_6::VERSION) ;
			elseif(!is_null($r = $result['host']))
				$this->_name = $literal->slice($r[0], $r[1]) ;
		}
		else
			throw new exception(self::ERR_IP_BAD) ;
	}

	public		function __tostring()
	{
		if($this->name instanceof string_ex)
			$string = (string) $this->name ;
		elseif($this->ip instanceof inet)
			$string = (string) $this->ip ;
		else
			throw new exception('') ;

		return $string ;
	}

	/** \brief		Fetch an IP for the host
	 *
	 *  \bug		If lookup fails, its timeout too long. Maybe i'll
	 *  			implement a socket based lookup to work arround
	 */
	public		function resolve()
	{
		if($this->name->length() > 0)
		{
			$ret = gethostbyname($this->name) ;
			if($ret != $this->name)
			{
				$ip = new string_ex($ret) ;
				$this->ip = inet::new_($ip, inet_4::VERSION) ;
				return true ;
			}
		}

		return false ;
	}

	public		function reverse()
	{
		if($this->ip instanceof inet)
		{
			$name = gethostbyaddr($this->ip) ;

			if($name == $this->ip)
				return false ;

			$this->reverse = new host(new string_ex($name)) ;

			return true ;
		}

		return false ;
	}

	protected	function _get_reverse()
	{
		if(!($this->_reverse instanceof host))
			$this->reverse() ;
	}

	protected	function _get_ip()
	{
		if(!($this->_ip instanceof inet))
			$this->resolve() ;
	}

}


