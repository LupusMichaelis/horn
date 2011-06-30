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

namespace horn\lib\inet ;
using \horn\lib as h ;

require_once 'horn/lib/url.php' ;
require_once 'horn/lib/inet/host.php' ;
require_once 'horn/lib/string.php' ;
require_once 'horn/lib/collection.php' ;
require_once 'horn/lib/regex.php' ;
require_once 'horn/lib/regex-defs.php' ;

/** \brief URL describes in RFC 1738
  * \code
  * <scheme> : <scheme-specific-part>
  * <scheme> := [a-z.+-]+
  * \endcode
  *
  */
class url
	extends \horn\lib\uri
{
	const ERR_MALFORMED				= 'URL\'s not valid.' ;
	const ERR_SCHEME_NO				= 'Scheme not found.' ;
	const ERR_SCHEME_BAD			= 'Malformed scheme.' ;
	const ERR_SCHEME_NOT_SUPPORTED	= 'Scheme is not supported.' ;

	protected		$_scheme ;
	protected		$_locator ;

	/** \brief		This method must implement a way to reduce the
	 *				processed literal to a canonical literal string. 
	 *				For example, if the literal contain HTTP, it must be
	 *				lowcased
	 *  \return	boolean		Normalization passed good
	 */
	public		function normalize()
	{
		if($this->scheme instanceof \horn\lib\string)
			$this->scheme->lowcase() ;
		else
			return false ;

		$this->sync_literal() ;
		return true ;
	}

	public		function sync_literal()
	{
		$this->literal->reset() ;
		$this->literal->glue($this->scheme, ':', $this->location) ;
	}

	protected	function parse()
	{
		$scheme_sep_pos = $this->literal->search(':') ;
		if($scheme_sep_pos < 0)
			$this->_throw(self::ERR_SCHEME_NO) ;

		$this->scheme = $this->literal->head($scheme_sep_pos - 1) ;
		$this->locator = $this->literal->tail($scheme_sep_pos + 1) ;

		$this->is_scheme_supported() ;
	}

	protected function is_scheme_supported()
	{
		return true ;
	}
}

class url_inet extends url
{
	const		ERR_LOCATOR_SLASH	= 'Trailing slash missing.' ;
	const		ERR_NO_HOST			= 'Host missing.' ;

	protected	$_user ;
	protected	$_password ;
	protected	$_host ;
	protected	$_port ;
	protected	$_path ;
	protected	$_search ;
	protected	$_id ;

	// Maybe I had to document that ? :-D
	protected	function parse()
	{
		parent::parse() ;

		$re = new regex('^'.RE_URL.'$') ;
		$re->match($this->literal) ;
		$pieces = $re->get_pieces_by_match(0) ;

		if(!is_null($pieces['user']))
		{
			$this->user = $this->literal->slice
			($pieces['user'][0], $pieces['user'][1]) ;
			if(!is_null($pieces['password']))
				$this->password = $this->literal->slice
				($pieces['password'][0], $pieces['password'][1]) ;
		}

		if(!is_null($pieces['host']))
			$this->host = new host($this->literal->slice
				($pieces['host'][0], $pieces['host'][1])) ;
		elseif(!is_null($pieces['inet4']))
			$this->host = host::new_inet4($this->literal->slice
				($pieces['host'][0], $pieces['host'][1])) ;
		elseif(!is_null($pieces['inet6']))
			$this->host = host::new_inet6($this->literal->slice
				($pieces['host'][0], $pieces['host'][1])) ;
		else
			$this->_throw(self::ERR_NO_HOST) ;

		if(!is_null($pieces['port']))
			$this->port = $this->literal->slice
				($pieces['port'][0], $pieces['port'][1])
				->as_integer() ;

		if(!is_null($pieces['path']))
			$this->path = new path($this->literal->slice
				($pieces['path'][0], $pieces['path'][1])) ;

#		var_dump($pieces['path'], $this->path) ;

		if(!is_null($pieces['search']))
			$this->search = $this->literal->slice
				($pieces['search'][0], $pieces['search'][1]) ;

		if(!is_null($pieces['id']))
			$this->id = $this->literal->slice
				($pieces['id'][0], $pieces['id'][1]) ;
	}

}

class path
{
	protected	$_literal ;
	protected	$_nodes ;

	public		function __construct(\horn\lib\string $source)
	{
		$this->literal = $source ;
		$this->nodes = new collection ;
	}

	public		function __tostring()
	{
		return (string) $this->literal ;
	}

	protected	function parse()
	{
		$re = new regex("^".RE_PATH."$") ;
		$re->match($this->literal) ;

		$this->nodes = $this->literal->explode('/') ;
	}
}

/*
class url_inet
	extends url_file
{
}

class url_db
	extends url_file
*/
class url_db
	extends url_inet
{
	protected	$_space ;
	protected	$_table ;

	protected	function parse()
	{
		parent::parse() ;

		$this->space = new \horn\lib\string($this->path) ;
		$this->space = $this->space->tail(1) ;
	}
}


