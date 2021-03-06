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

h\import('lib/object');
h\import('lib/regex/escaper');

class expression
	extends		h\object\public_
{
	const		default_delemeter = '`';
	protected	$_pattern;
	protected	$_delimiter;
	private		$escaper;

	public		function __construct(h\text $pattern, h\text $delimiter = null)
	{
		$this->_delimiter = is_null($delimiter)
			? h\text(static::default_delemeter)
			: $delimiter;
		$this->_pattern = new h\text();

		parent::__construct();

		$this->_pattern = $pattern;
		$this->escaper = new escaper(h\text($pattern->charset));
	}
	
	protected	function _clone()
	{
		$new = new static($this->pattern, $this->delimiter);
		return $new;
	}

	protected	function &_get_pattern()
	{
		$pattern = h\text::format("%1\$s%2\$s%1\$s", $this->delimiter, $this->_pattern);
		return $pattern;
	}

	protected	function _set_pattern(h\text $pattern)
	{
		$pattern = $this->escaper->do_escape($pattern, $this->delimiter);
		$this->_pattern->assign($pattern);
	}

	protected	function _set_delimiter(h\text $delimiter)
	{
		$pattern = $this->escaper->do_unescape($pattern, $this->delimiter);
		$this->delimiter = $delimiter;
		$this->_set_pattern($this->_pattern);
	}

	public		function is_matching(h\text $subject)
	{
		$result = $this->do_execute($subject);
		return $result->is_match();
	}


	public		function do_execute(h\text $subject)
	{
		$result = new result($this, $subject);
		return $result;
	}
}


