<?php
/** 
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

namespace horn\lib\regex;
use \horn\lib as h;

class expression
	extends		h\object\public_
{
	const		default_delemeter = '`';
	protected	$_pattern;
	protected	$_delimiter;

	public		function __construct(h\string $pattern, h\string $delimiter = null)
	{
		$this->_delimiter = is_null($delimiter)
			? h\string(static::default_delemeter)
			: $delimiter;
		$this->_pattern = new h\string();

		parent::__construct();

		$this->pattern = $pattern;
	}
	
	protected	function _clone()
	{
		$new = new static($this->pattern, $this->delimiter);
		return $new;
	}

	protected	function &_get_pattern()
	{
		$pattern = h\string::format("%1\$s%2\$s%1\$s", $this->delimiter, $this->_pattern);
		return $pattern;
	}

	protected	function _set_pattern(h\string $pattern)
	{
		$pattern->escape($this->delimiter);
		$this->_pattern->assign($pattern);
	}

	protected	function _set_delimiter(h\string $delimiter)
	{
		$this->_pattern->unescape($this->delimiter);
		$this->delimiter = $delimiter;
		$this->_set_pattern($this->_pattern);
	}

	public		function is_matching(h\string $subject)
	{
		$this->do_execute($subject);
		return $result->is_match();
	}


	public		function do_execute(h\string $subject)
	{
		$result = new result($this, $subject);
		return $result;
	}
}


