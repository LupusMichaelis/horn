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


require_once 'horn/lib/url.php' ;

/**
 *	\code
 *	http://<user>:<pass>@<host>:<port>/<path>?<searchpart>
 *	\endcode
 */
class url_http
	extends		url_inet
{
	public		function __construct(string_ex $literal)
	{
		parent::__construct($literal) ;
		$this->port = 80 ;
	}

	protected	function is_scheme_supported()
	{
		return $this->scheme->to_lower() == 'http' ;
	}

	public		function normalize()
	{
		if($this->host instanceof host)
		{
			if(!$this->host->normalize())
				return false ;
		}
		else
			return false ;

		if(!parent::normalize())
			return false ;

		return true ;
	}

	public		function sync_literal()
	{
		$this->location->reset() ;
		$this->location->append('//') ;

		if($this->username instanceof string_ex)
		{
			$this->location->append($this->username) ;
			if($this->password instanceof string_ex)
				$this->location->append_list(':', $this->password) ;

			$this->location->append('@') ;
		}

		if($this->hostname instanceof host)
			$this->location->append($this->hostname->as_string()) ;
		else
			throw new exception(self::ERR_NO_HOST) ;

		if(is_integer($this->port))
			$this->location->append_list(':', $this->port) ;

		if($this->path instanceof path)
			$this->location->append($this->path->as_string()) ;

		if($this->search instanceof string_ex)
			$this->location->append_list('?', $this->search) ;

		parent::sync_literal() ;
	}

}

class url_https
	extends		url_http
{
	protected function is_scheme_supported()
	{
		return $this->scheme == 'https' ;
	}
}

