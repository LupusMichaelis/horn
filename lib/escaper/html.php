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

class base
	extends h\escaper
{
	/** \see http://php.net/manual/en/function.htmlentities.php */
	private		function php_htmlentities(/* ... */)
	{
		$args = func_get_args();

		$args[1] |= ENT_HTML401;
		//
		if('ASCII' === $args[2])
			$args[2] = 'ISO-8859-15';

		$result = call_user_func_array('htmlentities', $args);
		// If htmlentities encounter an invalid character, it returns an empty string
		if('' !== $args[0] && '' === $result)
			throw $this->_exception('Encoding issue while escaping an HTML string');

		return $result;
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
		throw $this->_exception_not_implemented(__CLASS__, __FUNCTION__);
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
		throw $this->_exception_not_implemented(__CLASS__, __FUNCTION__);
	}
}

