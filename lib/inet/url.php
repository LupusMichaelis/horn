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

namespace horn\lib\inet;
use \horn\lib as h;

h\import('lib/url');
h\import('lib/inet/host');
h\import('lib/string');
h\import('lib/collection');
h\import('lib/regex');
h\import('lib/regex-defs');

abstract
class url
	extends h\url
{
	const		ERR_LOCATOR_SLASH	= 'Trailing slash missing.';
	const		ERR_NO_HOST			= 'Host missing.';

	protected	$_user;
	protected	$_password;
	protected	$_host;
	protected	$_port;
	protected	$_path;
	protected	$_search;
	protected	$_id;

	// Maybe I had to document that ? :-D
	protected	function parse()
	{
		parent::parse();

		$re = new regex('^'.RE_URL.'$');
		$re->match($this->literal);
		$pieces = $re->get_pieces_by_match(0);

		if(!is_null($pieces['user']))
		{
			$this->user = $this->literal->slice
			($pieces['user'][0], $pieces['user'][1]);
			if(!is_null($pieces['password']))
				$this->password = $this->literal->slice
					($pieces['password'][0], $pieces['password'][1]);
		}

		if(!is_null($pieces['host']))
			$this->host = new host($this->literal->slice
				($pieces['host'][0], $pieces['host'][1]));
		elseif(!is_null($pieces['inet4']))
			$this->host = host::new_inet4($this->literal->slice
				($pieces['host'][0], $pieces['host'][1]));
		elseif(!is_null($pieces['inet6']))
			$this->host = host::new_inet6($this->literal->slice
				($pieces['host'][0], $pieces['host'][1]));
		else
			throw $this->_exception(self::ERR_NO_HOST);

		if(!is_null($pieces['port']))
			$this->port = $this->literal->slice
				($pieces['port'][0], $pieces['port'][1])
				->as_integer();

		if(!is_null($pieces['path']))
			$this->path = new path($this->literal->slice
				($pieces['path'][0], $pieces['path'][1]));

		if(!is_null($pieces['search']))
			$this->search = $this->literal->slice
				($pieces['search'][0], $pieces['search'][1]);

		if(!is_null($pieces['id']))
			$this->id = $this->literal->slice
				($pieces['id'][0], $pieces['id'][1]);
	}
}

class path
	extends h\path
{
}

class search_part
	extends h\collection_mutltivalue
{
	static
	public		function from_string(string $s)
	{
		$new = new static;

		$parts = $s->explode('&');
		foreach($parts as $part)
		{
			$pos = $part->search('=');
			if($pos !== false)
			{
				$name = $part->head($pos);
				$value = $part->tail($pos + 1);
			}
			else
			{
				$name = $part;
				$value = null;
			}

			$pos = $name->search('[]');
			if($pos !== false)
				$name = $name->head(-2);

			$new[$name] = urldecode($value);
		}
	}
}

class request_uri
	extends h\object_public
{
	protected	$_path;
	protected	$_searchpart;

	public		function __construct(h\string $raw = null)
	{
		if($raw === null)
			$raw = h\string('');

		$qmark = $raw->search('?');
		if($qmark > -1)
		{
			$this->_path = $raw->head($qmark - 1);
			$this->_searchpart = $raw->tail($qmark);
		}
		else
		{
			$this->_path = $raw;
			$this->_searchpart = h\string('');
		}

		parent::__construct();
	}

	public		function _to_string()
	{
		return $this->_path;
	}
}

