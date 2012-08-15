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

namespace horn\lib ;

import('lib/object') ;
import('lib/collection') ;

/** Check if the provided variable is a string or not
 *  \param	any		$variable
 *	\return	bool
 */
function is_string($variable)
{
	return $variable instanceof string || \is_string($variable) ;
}

/** Cast $converted in a string
 */
function string($converted)
{
	return new string($converted) ;
}

/** Concatenate two strings
 */
function concatenate($lhs, $rhs)
{
	return string($lhs)->append(string($rhs)) ;
}

/** 	Implementation of a string class that handle encoding issues. The API try
 *				to reach a consistency.
 *
 *  \todo		refactor prepend, append and append_list method to check encoding issues
 *  \todo		write specific exception classes (stringception for offset and charset
 *  			issues)
 */
class string
	extends		object_public
	implements	\arrayaccess, \countable
{
	const		ERR_OVERRUN	= 'Specified offset overrun [begin:%d,end:%d].' ;
	const		ERR_INVERT	= 'Offsets seems invert [begin:%d,end:%d].' ;

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
		parent::__construct() ;

		if(!is_null($copied))
			$copied = (string) $copied ;

		if(!is_null($charset) && !is_string($charset))
			$this->_throw('charset') ;

		$this->scalar = $copied ;
		$this->charset = $charset ;

		$this->_auto_charset() ;
	}

	/** Autodetect the charset/encoding of the encapsulate string
	 *	\see	http://php.net/mb_detect_encoding
	 */
	protected	function _auto_charset()
	{
		 return $this->charset = mb_detect_encoding($this->_scalar) ;
	}

	/** Factory method to forge string from a format string
	 *
	 *	\see	http://php.net/sprintf
	 *	\todo	Catch sprintf error
	 *	\param	$format		(string|string)	format string
	 *	\param	$arg1..n	any arguments
	 */
	static
	public function format(/* $format, $arg1, $arg2, ... , $argn */)
	{
		$s = new static ;
		$args = func_get_args() ;
		$s->_scalar = call_user_func_array('sprintf', $args) ;
		$s->_auto_charset() ;

		return $s ;
	}

	/**
	 */
	public		function _to_string()
	{
		return (string) $this->_scalar ;
	}

	/** Cast scalar string to integer.
	 */
	public		function as_integer()
	{
		return (int) $this->_scalar ;
	}

	/** \todo	length relative to encoding
	 */
	public		function length()
	{
		return strlen($this->_scalar) ;
	}

	/**	Stick $sticker at begin of string.
	 *	\param	$sticker	string	The string to be pretend to $this.
	 *	\return	$this
	 */
	public		function prepend(string $sticker)
	{
		$this->_scalar = $sticker->_scalar . $this->_scalar ;
		$this->_auto_charset() ;
		return $this ;
	}

	/**	Concatenate $sticker at back of string.
	 *	\param	$sticker	string	The string to be pretend to $this.
	 *	\return	$this
	 */
	public		function append(string $sticker)
	{
		$this->_scalar .= $sticker->_scalar ;
		$this->_auto_charset() ;
		return $this ;
	}

	/**	Factory method that gets caracters from 0 to $offset position from $this string.
	 *	\warning	It is the position, not the size that is requested !
	 *	\param		$offset	int		Position of the end of fetched string.
	 *	\return		$this
	 *	\throw		exception	On overrun.
	 */
	public		function head($offset)
	{
		if($offset > $this->length())
			$this->_throw_format(self::ERR_OVERRUN, null, $offset, $this->length()) ;

		return new static(substr($this->_scalar, 0, $offset+1)) ;
	}

	/**	Factory method that gets caracters from $offset to last position from $this string.
	 *	\warning	It is the position, not the size that is requested !
	 *	\param		$offset	int		Position of the start of fetched string.
	 *	\return	$this
	 *	\throw		exception	On overrun.
	 */
	public		function tail($offset)
	{
		if($offset > $this->length()-1)
			$this->_throw_format(self::ERR_OVERRUN, $offset, null, $this->length()) ;

		return new static(substr($this->_scalar, $offset)) ;
	}

	/** Factory method that creates a new string [$begin,$end[
	 *	\code
	 *			$s = new string("My pretty string.") ;
	 *			$s->slice(0,2) ; // "My"
	 *			$s->slice(3,9) ; // "pretty"
	 *			$s->slice($s->search("str"), $s->length() - 1) ; // "string"
	 *	\endcode
	 *
	 * \param	$begin		int		First position (included) of the fetched string.
	 * \param	$end		int		Last position (exluded) of the fetched string.
	 *
	 * \return	string	The string corresponding to the interval
	 * \throw	exception	On bad index
	 */
	public		function slice($begin, $end)
	{
		$len = strlen($this->_scalar) ;
		if($begin > $end)
			$this->_throw_format(self::ERR_INVERT, $begin, $end) ;

		if($begin > $len or $end > $len)
			$this->_throw_format(self::ERR_OVERRUN, $begin, $end, $len) ;

		return new static(substr($this->_scalar, $begin, $end - $begin)) ;
	}

	/** Search $needle from $offset to end until a match.
	 *
	 * \param	$needle		string
	 * \param	$offset		int
	 *
	 * \return	Found offset.
	 */
	public		function search($needle, $offset=0)
	{
		if($needle instanceof self)
			$needle = $needle->_scalar ;
		elseif(!\is_string($needle))
			$needle = (string) $needle ;

		$pos = strpos($this->_scalar, $needle, $offset) ;
		return $pos === false ? -1 : $pos ;
	}
	
	/** 
	 *	\param	$rhs 	string
	 *	\return	int		0 on equal, -1 if $this is before $rhs, 1 else.
	 *	\see http://php.net/strcmp
	 */
	public		function compare(string $rhs)
	{
		return strcmp($this->_scalar, $rhs->_scalar) ;
	}

	/** Explode.
	  *
	  * \param	$c4		string	Pattern that must be user to cut $this
	  * \return	collection of string
	  */
	public		function explode($c4)
	{
		$result = new collection ;

		$pieces = explode($c4, $this->_scalar) ;
		if($pieces !== false)
			foreach($pieces as $piece)
				$result[] = new static($piece) ;

		return $result ;
	}

	/** Set every alphabetic characters to lowercase in $this.
	  * \return $this
	  */
	public		function lowcase()
	{
		$this->_scalar = strtolower($this->_scalar) ;
		return $this ;
	}

	/** Factory that creates a new string from $this, and set every alphabetic characters to lower case.
	  *
	  * \return string	The lowred case clone of $this.
	  */
	public		function to_lower()
	{
		$new = clone $this ;
		return $new->lowcase() ;
	}

	/** Set every alphabetic caracters of this to upcase.
	 *  \return $this
	 */
	public		function upcase()
	{
		$this->_scalar = strtoupper($this->_scalar) ;
		return $this ;
	}

	/** Factory that creates a new string from $this, and set every alphabetic characters of the new
	  *	string to upper case.
	  *
	  * \return The upped case string
	  */
	public		function to_upper()
	{
		$new = clone $this ;
		return $new->upcase() ;
	}

	/** Convert scalar string to the requiered encoding and set $this to the charset.
	 *	\todo	Ensure charset.
	 *	\param	$charset	string	The requiered charset to imbue in $this string.
	 *	\return	$this
	 */
	public		function convert($charset)
	{
		$this->_scalar = mb_convert_encoding($this->_scalar, $charset, $this->charset) ;
		$this->charset = $charset ;
		return $this ;
	}

	/**
	 */
	public		function to_converted($charset)
	{
		$converted = mb_convert_encoding($this->_scalar, $charset, $this->charset) ;
		return new static($converted, $charset) ;
	}


	// arrayaccess ///////////////////////////////////////////////////////
	public		function offsetUnset($offset)
	{
		if($this->offsetExists($offset))
		{
			$begin = $this->head($offset - 1)->_scalar ;
			$end = $this->tail($offset + 1)->_scalar ;
			$this->_scalar = $begin . $end ;
		}
		else 
			$this->_throw_unexpected() ;

		return null ;
	}

	public		function offsetSet($offset, $value)
	{
		$this->_scalar{$offset} = $value ;
		$this->_auto_charset() ;
	}

	public		function offsetExists($offset)
	{
		return -1 < $offset && $offset < strlen($this->_scalar) ;
	}

	public		function offsetGet($offset)
	{
		return $this->slice($offset, $offset+1) ;
	}

	// countable /////////////////////////////////////////////////////////
	public		function count()
	{
		return $this->length() ;
	}
}

