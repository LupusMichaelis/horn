<?php
/** String extended
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

namespace horn\lib;
use \horn\lib as h;

import('lib/object');
import('lib/collection');

/** Check if the provided variable is a string or not
 *  \param	any		$variable
 *	\return	bool
 */
function is_string($variable)
{
	return $variable instanceof text || \is_string($variable);
}

/** Cast $converted in a text
 */
function text($converted)
{
	return new text($converted);
}

/** Concatenate two strings
 */
function concatenate($lhs, $rhs)
{
	return text($lhs)->append(text($rhs));
}

/** 	Implementation of a text class that handle encoding issues. The API try
 *				to reach a consistency.
 *
 *  \todo		refactor prepend, append and append_list method to check encoding issues
 *  \todo		write specific exception classes (stringception for offset and charset
 *  			issues)
 */
class text
	extends		object_public
	implements	\arrayaccess, \countable, \jsonserializable
{
	const		ERR_OVERRUN	= 'Specified offset overrun [begin:%d,end:%d].';
	const		ERR_INVERT	= 'Offsets seems invert [begin:%d,end:%d].';

	protected	$_scalar ;	///< PHP native string
	protected	$_charset ;	///< PHP native string that contains the name of the charset

	/** If no $charset parameter provided, the ctor try to determines it from the $copied parameter. When a $copied
	 *	parameter is provided, the ctor checks the type and decide what to do with. If the parameter can't be cast, an
	 *	exception can be raised.
	 *	\param	$copied		(any|null)		copied thing
	 *	\param	$charset	(string|null)	\see _auto_charset()
	 */
	public		function __construct($copied=null, $charset=null)
	{
		parent::__construct();

		if(!is_null($copied))
			$copied = (string) $copied;

		if(!is_null($charset) && !is_string($charset))
			throw $this->_exception('charset');

		$this->scalar = $copied;
		$this->charset = $charset;

		$this->_auto_charset();
	}

	/** Autodetect the charset/encoding of the encapsulate string
	 *	\see	http://php.net/mb_detect_encoding
	 */
	protected	function _auto_charset()
	{
		$this->_charset = mb_detect_encoding($this->_scalar);
		if(false === $this->_charset)
			throw $this->_exception('Charset detection failed');
	}

	/** Factory method to forge text from a format string
	 *
	 *	\see	http://php.net/sprintf
	 *	\todo	Catch sprintf error
	 *	\param	$format		(string|text)	format string
	 *	\param	$arg1..n	any arguments
	 */
	static
	public function format(/* $format, $arg1, $arg2, ... , $argn */)
	{
		$s = new static;
		$args = func_get_args();
		$s->_scalar = call_user_func_array('sprintf', $args);

		return $s;
	}

	/**
	 */
	public		function _to_string()
	{
		return (string) $this->_scalar;
	}

	/** Cast scalar string to integer.
	 */
	public		function as_integer()
	{
		return (int) $this->_scalar;
	}

	/** \todo	length relative to encoding
	 */
	public		function length()
	{
		return strlen($this->_scalar);
	}

	/**	Stick $sticker at begin of string.
	 *	\param	$sticker	text	The text to be pretend to $this.
	 *	\return	$this
	 */
	public		function prepend(text $sticker)
	{
		$this->_scalar = $sticker->_scalar . $this->_scalar;
		$this->_auto_charset();
		return $this;
	}

	/**	Concatenate $sticker at back of text.
	 *	\param	$sticker	text	The text to be pretend to $this.
	 *	\return	$this
	 */
	public		function append(text $sticker)
	{
		$this->_scalar .= $sticker->_scalar;
		$this->_auto_charset();
		return $this;
	}

	/**	Factory method that gets caracters from 0 to $offset position from $this text.
	 *	\warning	It is the position, not the size that is requested !
	 *	\param		$offset	int		Position of the end of fetched text.
	 *	\return		text
	 *	\throw		exception	On overrun.
	 */
	public		function head($offset)
	{
		$new = clone $this;
		$new->betail($offset);
		return $new;
	}

	private		function php_substr($offset, $length=null)
	{
		if(is_null($length))
			$scalar = substr($this->_scalar, $offset);
		else
			$scalar = substr($this->_scalar, $offset, $length);

		if(false === $scalar)
			throw $this->_exception('Unexpected error');

		return $scalar;
	}

	public		function cut($offset)
	{
		if($offset === strlen($this->_scalar))
		{
			$tail = new static('', $this->charset);
			$head = clone $this;
		}
		else
		{
			$head = new static($this->php_substr(0, $offset), $this->charset);
			$tail = new static($this->php_substr($offset), $this->charset);
		}

		return h\collection($head, $tail);
	}

	/** Remove part of the text before $offset
	 *	\param		$offset	int		Position of the end of fetched text.
	 *	\return		text		The head of the text
	 *	\throw		exception	On overrun or bug
	 */
	public		function behead($offset)
	{
		if($offset > $this->length())
			throw $this->_exception_format(self::ERR_OVERRUN, null, $offset, $this->length());

		list($head, $tail) = $this->cut($offset);

		$this->_scalar = $tail->_scalar;
		return $head;
	}

	/**	Factory method that gets caracters from $offset to last position from $this text.
	 *	\param		$offset	int		Position of the start of fetched text.
	 *	\return		text
	 *	\throw		exception	On overrun.
	 */
	public		function tail($offset)
	{
		$new = clone $this;
		$new->behead($offset);
		return $new;
	}

	/**	Cut off the tail of that text from $offset
	 *	\param		$offset	int		Position of the first caracter removed
	 *	\return		text			The tail of the text
	 *	\throw		exception	On overrun.
	 */
	public		function betail($offset)
	{
		if($offset > $this->length())
			throw $this->_exception_format(self::ERR_OVERRUN, $offset, null, $this->length());

		list($head, $tail) = $this->cut($offset);

		$this->_scalar = $head->_scalar;
		return $tail;
	}

	/** Factory method that creates a new text [$begin,$end[
	 *	\code
	 *			$s = new text("My pretty text.");
	 *			$s->slice(0,2) ; // "My"
	 *			$s->slice(3,9) ; // "pretty"
	 *			$s->slice($s->search("str"), $s->length() - 1) ; // "text"
	 *	\endcode
	 *
	 * \param	$begin		int		First position (included) of the fetched text.
	 * \param	$end		int		Last position (exluded) of the fetched text.
	 *
	 * \return	text	The text corresponding to the interval
	 * \throw	exception	On bad index
	 */
	public		function slice($begin, $end)
	{
		$len = strlen($this->_scalar);
		if($begin > $end)
			throw $this->_exception_format(self::ERR_INVERT, $begin, $end);

		if($begin > $len || $end > $len)
			throw $this->_exception_format(self::ERR_OVERRUN, $begin, $end, $len);

		return new static(substr($this->_scalar, $begin, $end - $begin));
	}

	/** Removes spaces and tabs from edges of the text
	 */
	public		function trim()
	{
		$this->_scalar = trim($this->_scalar);
	}

	/** Clone the current text and return it trimmed
	 *  \return	The trimmed text
	 */
	public		function trimmed()
	{
		$copy = clone $this;
		$copy->trim();
		return $copy;
	}

	/** Search $needle from $offset to end until a match.
	 *
	 * \param	$needle		text
	 * \param	$offset		int
	 *
	 * \return	Found offset.
	 */
	public		function search($needle, $offset=0)
	{
		if($needle instanceof self)
			$needle = $needle->_scalar;
		elseif(!\is_string($needle))
			$needle = (string) $needle;

		$pos = strpos($this->_scalar, $needle, $offset);
		return $pos === false ? -1 : $pos;
	}
	
	/** 
	 *	\param	$rhs 	text
	 *	\return	int		0 on equal, -1 if $this is before $rhs, 1 else.
	 *	\see http://php.net/strcmp
	 */
	public		function compare(text $rhs)
	{
		return strcmp($this->_scalar, $rhs->_scalar);
	}

	/** Explode.
	  *
	  * \param	$c4		text	Pattern that must be user to cut $this
	  * \return	collection of text
	  */
	public		function explode($c4)
	{
		$result = new collection;

		$pieces = explode($c4, $this->_scalar);
		if($pieces !== false)
			foreach($pieces as $piece)
				$result[] = new static($piece);

		return $result;
	}

	/** Set every alphabetic characters to lowercase in $this.
	  * \return $this
	  */
	public		function lowcase()
	{
		$this->_scalar = strtolower($this->_scalar);
		return $this;
	}

	/** Factory that creates a new text from $this, and set every alphabetic characters to lower case.
	  *
	  * \return text	The lowred case clone of $this.
	  */
	public		function to_lower()
	{
		$new = clone $this;
		return $new->lowcase();
	}

	/** Set every alphabetic caracters of this to upcase.
	 *  \return $this
	 */
	public		function upcase()
	{
		$this->_scalar = strtoupper($this->_scalar);
		return $this;
	}

	/** Factory that creates a new text from $this, and set every alphabetic characters of the new
	  *	text to upper case.
	  *
	  * \return The upped case text
	  */
	public		function to_upper()
	{
		$new = clone $this;
		return $new->upcase();
	}

	/** Convert scalar text to the requiered encoding and set $this to the charset.
	 *	\todo	Ensure charset.
	 *	\param	$charset	text	The requiered charset to imbue in $this text.
	 *	\return	$this
	 */
	public		function convert($charset)
	{
		$this->_scalar = mb_convert_encoding($this->_scalar, $charset, $this->charset);
		$this->charset = $charset;
		return $this;
	}

	/**
	 */
	public		function to_converted($charset)
	{
		$converted = mb_convert_encoding($this->_scalar, $charset, $this->charset);
		return new static($converted, $charset);
	}

	// arrayaccess ///////////////////////////////////////////////////////
	public		function offsetUnset($offset)
	{
		if($this->offsetExists($offset))
		{
			$begin = $this->head($offset - 1)->_scalar;
			$end = $this->tail($offset + 1)->_scalar;
			$this->_scalar = $begin . $end;
		}
		else 
			throw $this->_exception_unexpected();

		return null;
	}

	public		function offsetSet($offset, $value)
	{
		$this->_scalar{$offset} = $value;
		$this->_auto_charset();
	}

	public		function offsetExists($offset)
	{
		return -1 < $offset && $offset < strlen($this->_scalar);
	}

	public		function offsetGet($offset)
	{
		return $this->slice($offset, $offset+1);
	}

	// countable /////////////////////////////////////////////////////////
	public		function count()
	{
		return $this->length();
	}

	// jsonserializable //////////////////////////////////////////////////
	public		function jsonserialize()
	{
		return $this->scalar;
	}
}

