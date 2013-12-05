<?php
/** HTML escaper in a string
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

namespace horn\lib\escaper\html;
use \horn\lib as h;

h\import('lib/object');
h\import('lib/escaper');

abstract
class base
	extends h\escaper\base
{
	/** \see http://php.net/manual/en/function.htmlentities.php */
	protected	function php_htmlentities(/* ... */)
	{
		$args = func_get_args();
		$this->filter_args($args);
		$result = call_user_func_array('htmlentities', $args);
		// If htmlentities encounter an invalid character, it returns an empty string
		$this->check_return($args, $result);

		return $result;
	}

	/** \see http://www.php.net/manual/en/function.html-entity-decode.php */
	protected	function php_html_entity_decode(/* ... */)
	{
		$args = func_get_args();
		$this->filter_args($args);
		$result = call_user_func_array('html_entity_decode', $args);
	}

	protected	function filter_args(& /* io */ $args)
	{
		$args[1] |= ENT_HTML401;
		//
		if('ASCII' === $args[2])
			$args[2] = 'ISO-8859-15';
	}

	protected	function check_return(& /* i */ $args, & /* i */ $result)
	{
		if('' !== $args[0] && '' === $result)
			throw $this->_exception('Encoding issue while escaping an HTML string');
	}

	protected	function copy_and_convert(h\string $from)
	{
		return $from->charset != $this->charset
			? $from->to_converted($this->charset)
			: clone $from;
	}
}

class text
	extends base
{
	public		function do_escape(h\string $text)
	{
		$result = $this->copy_and_convert($text);
		$result->scalar = $this->php_htmlentities($result->scalar, ENT_NOQUOTES, $result->charset);

		return $result;
	}

	public		function do_unescape(h\string $text)
	{
		$result = $this->copy_and_convert($text);
		$result->scalar = $this->php_html_entity_decode($result->scalar, ENT_NOQUOTES, $result->charset);

		return $result;
	}
}

class attribute
	extends base
{
	public		function do_escape(h\string $text)
	{
		$result = $this->copy_and_convert($text);
		$result->scalar = $this->php_htmlentities($result->scalar, ENT_QUOTES, $result->charset);
		return $result;
	}

	public		function do_unescape(h\string $text)
	{
		$result = $this->copy_and_convert($text);
		$result->scalar = $this->php_html_entity_decode($result->scalar, ENT_QUOTES, $result->charset);

		return $result;
	}
}

