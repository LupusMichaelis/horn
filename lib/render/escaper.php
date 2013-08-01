<?php
/** Rendering strategy
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2013, Lupus Michaelis
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

namespace horn\lib\render;
use \horn\lib as h;

h\import('lib/object');

class html_escaper
	extends h\object_public
{
	protected	$_charset;

	public		function __construct(h\string $charset)
	{
		$this->_charset = clone $charset;
		parent::__construct();
	}

	private		function copy_and_convert(h\string $text)
	{
		return $text->to_converted($this->charset);
	}

	/** \see http://php.net/manual/en/function.htmlentities.php */
	private		function php_htmlentities(/* ... */)
	{
		$args = func_get_args();

		$args[1] |= ENT_XML1;
		//
		if('ASCII' === $args[2])
			$args[2] = 'ISO-8859-15';

		$result = call_user_func_array('htmlentities', $args);
		// If htmlentities encounter an invalid character, it returns an empty string
		if('' !== $args[0] && '' === $result)
			throw $this->_exception('Encoding issue while escaping an HTML string');

		return $result;
	}

	public		function do_escape_text(h\string $text)
	{
		$result = $this->copy_and_convert($text);
		$result->scalar = $this->php_htmlentities($result->scalar, ENT_NOQUOTES, $result->charset);

		return $result;
	}

	public		function do_escape_attribute(h\string $text)
	{
		$result = $this->copy_and_convert($text);
		$result->scalar = $this->php_htmlentities($result->scalar, ENT_QUOTES, $result->charset);
		return $result;
	}

	// Helpers ////////////////////////////////
	public		function t($text)
	{
		if(! $text instanceof h\string)
			$text = h\string($text);

		return $this->do_escape_text($text);
	}

	public		function a($text)
	{
		if(! $text instanceof h\string)
			$text = h\string($text);

		return $this->do_escape_attribute($text);
	}
}

class template_engine
	extends h\object_public
{
}
