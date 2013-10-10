<?php
/** Base escaper
 *
 *  \project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  \copyright	2013, Lupus Michaelis
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

namespace horn\lib\escaper;
use \horn\lib as h;

h\import('lib/object');
h\import('lib/collection');
h\import('lib/string');

abstract
class base
	extends h\object_public
	implements h\escaper
{
	protected	$_charset;

	public		function __construct(h\string $charset)
	{
		$this->_charset = clone $charset;
		parent::__construct();
	}

}

class generic
	extends base
{
	protected	$_map;

	public		function __construct(h\string $charset)
	{
		$this->_map = h\collection();
		parent::__construct($charset);
	}

	public		function &_set_map(h\collection $pairs)
	{
		if($pairs->keys()->unique()->count() !== $pairs->values->count())
			throw $this->_exception_invalid_map();

		$this->_map = $pairs;
	}

	public		function do_escape(h\string $subject)
	{
		$escaped = strtr($subject, $this->_map);

		if(false === $escaped)
			throw $this->_exception_invalid_map();

		$escaped = h\string($escaped);
		return $escaped;
	}

	public		function do_unescape(h\string $subject)
	{
	}

	protected	function _exception_invalid_map()
	{
		return $this->_exception('Invalid translation map provided');
	}
}
