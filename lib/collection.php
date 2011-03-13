<?php
/** collection class definition
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

require_once 'horn/lib/object.php' ;

function is_collection($variable)
{
	return is_array($variable) || $variable instanceof \iterator ;
}

/**	An object that implements array behaviour in a consistent way.
  * \todo		make a compatible collection range class
  *				that allows laziness
  * 
  */
class collection
	extends		object_public
	implements	\Iterator, \ArrayAccess, \Countable
{
	/**
	 * \see		self::join
	 */
	public		function __construct(/* ... */)
	{
		parent::__construct() ;
		$this->_stack = array() ;
		$this->join(func_get_args()) ;
	}

	/** Get a copy of the stack as a scalar.
	 *	\return array
	 */
	public		function get_stack()
	{
		return (array) $this->_stack ;
	}

	/** Merge the argument list in a new collection.
	 *  Native PHP arrays and Iterable objects are join to the newly created
	 *  collection. Other variable types are pushed on the new collection.
	 *
	 *  \return		collection
	 */
	static		function merge(/* ... */)
	{
		$new = new self ;

		foreach(func_get_args() as $arg)
			if(is_array($arg) || $arg instanceof Iterator)
				$new->join($arg) ;
			else
				$new->push($arg) ;

		return $new ;
	}

	/** Iterate the collection and sticks string representation of elements each
	 *  to others with the given glue parameter. 
	 *
	 *  \param	$glue		mixed	A castable in string.
	 *  \return	string_ex			The imploded representation of the current
	 *  							collection
	 */
	public		function implode($glue)
	{
		if(!($glue instanceof string_ex))
			$glue = new string_ex($glue) ;

		$crunch = new string_ex ;
		$length = $this->count() ;
		$i = 0 ;
		foreach($this as $bit)
		{
			$crunch->append($bit) ;
			// No glue at back of string
			if($i < $length - 1)
				$crunch->append($glue) ;

			$i++ ;
		}

		return $crunch ;
	}

	/**
	 *  \param	$needle	mixed		The thing we are searching.
	 *  \param	$strict	bool		Ensure strict comparaision.
	 *  \return	(null|int|string)	Corresponding index to the value.
	 */
	public		function search_first($needle, $strict=false)
	{
		$keys = $this->search($needle, $strict) ;

		return isset($keys[0]) ? $keys[0] : null ;
	}

	/**
	 *  \param	$needle	mixed		The thing we are searching.
	 *  \param	$strict	bool		Ensure strict comparaision
	 *  \return	(null|array)		Corresponding index set to the value.
	 */
	public		function search($needle, $strict=false)
	{
		$keys = array_keys($this->_stack, $needle, $strict) ;

		return $keys ;
	}

	/** Remove element that match $value.
	 *	\return		bool	Success of removing.
	 */
	public		function remove($needle, $all = false)
	{
		$removed = false ;
		$matches = $this->search($needle) ;

		if($matches === false)
			return false ;

		if($all)
			foreach($matches as $match)
				$removed |= count(array_splice($this->_stack, $match, 1)) ;
		else
			$removed |= count(array_splice($this->_stack, $matches[0], 1)) ;

		return $removed ;
	}

	/** Push the given on the stack.
	 *	\return		collection	reference on $this
	 */
	public		function push($element)
	{
		array_push($this->_stack, $element) ;
		return $this ;
	}

	/** Pop the last inserted element.
	 *	\return		mixed		The poped element.
	 */
	public		function pop()
	{
		return array_pop($this->_stack) ;
	}

	/** Reference the front element of the collection.
	 *	\return		reference on $this
	 */
	public		function & front()
	{
		reset($this->_stack) ;

		return $this->_stack[key($this->_stack)] ;
	}

	/** Reference the back element of the collection.
	 *	\return		reference on $this
	 */
	public		function & back()
	{
		end($this->_stack) ;

		return $this->_stack[key($this->_stack)] ;
	}

	/** Spin off the collection.
	 *	\return		collection	reference on $this
	 */
	public		function reverse()
	{
		$this->_stack = array_reverse($this->_stack) ;
		return $this ;
	}

	/** Check for existence of a key that reference an element of the collection.
	 *	\return		bool	The key exists.
	 */
	public		function has_key($needle)
	{
		return array_key_exists($needle, $this->_stack) ;
	}

	/** Import every element of an iterable.
	 *  \param	$array	(iterable|array)
	 *						The set of values to append on this
	 *  					collection.
	 *	\return		collection		A reference on $this
	 */
	public		function join($array)
	{
		// \warning don't use array_merge, it doesn't use offsetSet
		foreach($array as $key => $value)
		{
			if(is_integer($key))
				$this[] = $value ;
			else
				$this[$key] = $value ;
		}

		return $this ;
	}

	/* interface Iterator */
	public		function current()	{ return current($this->_stack) ; }
	public		function next()		{ return next($this->_stack) ; }
	public		function key()		{ return key($this->_stack) ; }
	public		function rewind()	{ return reset($this->_stack) ; }
	public		function valid()	{ return array_key_exists(key($this->_stack), $this->_stack) ; }

	/* interface Countable */
	public		function count()	{ return count($this->_stack) ; }

	protected	function _filter_key($candidate)
	{
		if(\is_integer($candidate) || \is_string($candidate))
			$filtered = $candidate ;
		elseif(is_null($candidate))
			$filtered = null ;
		else
			$filtered = (string) $candidate ;
		return $filtered ;
	}

	/* interface ArrayAccess */
	public		function offsetExists($key)
	{
		return array_key_exists($this->_filter_key($key), $this->_stack) ;
	}
	
	public		function offsetGet($key)
	{
		if(!$this->offsetExists($key))
			$this->_throw_offset_does_not_exists($key) ;

		return $this->_stack[$key] ;
	}

	public		function offsetSet($key, $value)
	{
		if(is_null($key))
			$this->_stack[] = $value ;
		else
			$this->_stack[$this->_filter_key($key)] = $value ;

		return $this->current() ;
	}

	public		function offsetUnset($key)
	{
		unset($this->_stack[$this->_filter_key($key)]) ;
	}

	/** Clean current elements of the collection, then copy each elements of the
	 *  copied collection. If it meets an object, the method clone it.
	 *
	 *  \param	$rhs	collection
	 *  \return	null
	 */
	protected		function _set_stack($rhs)
	{
		$stack = array() ;
		$this->_stack = & $stack ;

		if(is_array($rhs) || $rhs instanceof iterator)
			foreach($rhs as $k => $v)
				if(is_scalar($v))
					$this[$k] = $v ;
				elseif(is_object($v))
					$this[$k] = clone $v ;
#				$this[$k] = $v ;
				else
					$this->_throw('Don\'t know how to copy current value') ;
		elseif(!is_null($rhs))
			$this->_throw('Bad type.') ;

		return $this ;
	}

	protected	function _get_stack()
	{
		$this->_throw_readonly_attribute('stack') ;
	}

	protected	function _throw_offset_does_not_exists($key)
	{
		$this->_throw_format('Key \'%s\' doesn\'t exist', $key) ;
	}

	/** array	Actual data that is accessed through collection
	 */
	protected	$_stack ; // = & array() ;
}


