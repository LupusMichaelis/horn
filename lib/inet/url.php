<?php 
/** 
 *
 *  \project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  \copyright	2009, Lupus Michaelis
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

h\import('lib/uri');
h\import('lib/uri/host');
h\import('lib/uri/hierarchical_part');
h\import('lib/uri/path');
h\import('lib/text');
h\import('lib/collection');
h\import('lib/regex');
h\import('lib/regex-defs');

abstract
class url
	extends h\uri\absolute
{
	public		function __construct()
	{
		$this->_hierarchical_part = new h\uri\hierarchical_part;

		parent::__construct();
	}

	protected	$_port;
	protected	$_hierarchical_part;
}

class path
	extends h\uri\path
{
}

class search_part
	extends h\collection_mutltivalue
{
	public		function _to_string()
	{
		return '';
	}
}

/*
	protected	function &_get_scheme_specific_part()
	{
		$specific_part = h\text('//');

		if(0 < $this->user->length())
		{
			$auth = h\text(\rawurlencode($this->user));
			if(0 < $this->password->length())
			{
				$auth->append(':');
				$auth->append(\rawurlencode($this->password));
			}

			$specific_part->append($auth);
			$specific_part->append('@');
		}

		$specific_part->append(h\text($this->host));

		if(80 !== $this->port && h\text('http')->is_equal($this->scheme)
				|| 443 !== $this->port && h\text('https')->is_equal($this->scheme))
		{
			$specific_part->append(h\text(':'));
			$specific_part->append(h\text($this->port));
		}

		$path = h\text($this->path);
		if(0 < $path->length())
			$specific_part->append($path);

		$search_part = h\text($this->search);
		if(0 < $search_part->length())
			$specific_part->append($search_part);

		return $specific_part;
	}

*/
