<?php
/** collection class definition
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
namespace horn\lib;

import('lib/object');
import('lib/string');
import('lib/future');

function is_collection($variable)
{
	return is_array($variable) || $variable instanceof \iterator;
}

function collection(/* ... */)
{
	$collection = new collection;
	$args = func_get_args() ; 
	foreach($args as $arg)
		$collection->push($arg);
	return $collection;
}

function c($a)
{
	if(!is_array($a))
		throw new \exception('Argument isn\'t an array.');

	$collection = new collection;
	foreach($a as $k => $v)
		$collection[$k] = $v;

	return $collection;
}

/**	An object that implements array behaviour in a consistent way.
  * \todo		make a compatible collection range class
  *				that allows laziness
  * 
  */
class collection
	extends		object_public
	implements	\Iterator
		, \ArrayAccess
		, \Countable
		, \JsonSerializable
{
	/**
	 * \see		self::join
	 */
	public		function __construct(/* ... */)
	{
		parent::__construct();
		$this->join(func_get_args());
	}

	/** Get a copy of the stack as a scalar.
	 *	\return array
	 */
	public		function get_stack()
	{
		return (array) $this->ref_stack();
	}

	/** Merge the argument list in a new collection.
	 *  Native PHP arrays and Iterable objects are join to the newly created
	 *  collection. Other variable types are pushed on the new collection.
	 *
	 *  \return		collection
	 */
	static		function merge(/* ... */)
	{
		$new = new static;

		foreach(func_get_args() as $arg)
			if(is_array($arg) || $arg instanceof Iterator)
				$new->join($arg);
			else
				$new->push($arg);

		return $new;
	}

	/** Iterate the collection and sticks string representation of elements each
	 *  to others with the given glue parameter. 
	 *	\bug	Assume we have a collection of strings
	 *  \param	$glue		mixed	A castable in string.
	 *  \return	string			The imploded representation of the current
	 *  							collection
	 */
	public		function implode($glue)
	{
		if(!($glue instanceof string))
			$glue = string($glue);

		$crunch = string('');
		$length = $this->count();
		$i = 0;
		foreach($this as $bit)
		{
			$bit = string($bit);
			$crunch->append($bit);
			// No glue at back of string
			if($i < $length - 1)
				$crunch->append($glue);

			++$i;
		}

		return $crunch;
	}

	/**
	 *  \param	$needle	mixed		The thing we are searching.
	 *  \param	$strict	bool		Ensure strict comparaision.
	 *  \return	(null|int|string)	Corresponding index to the value.
	 */
	public		function search_first($needle, $strict=false)
	{
		$keys = $this->search($needle, $strict);

		return isset($keys[0]) ? $keys[0] : null;
	}

	/**
	 *  \param	$needle	mixed		The thing we are searching as a value.
	 *  \param	$strict	bool		Ensure strict comparaision
	 *  \return	(null|array)		Corresponding index set to the value.
	 */
	public		function search($needle, $strict=false)
	{
		$keys = array_keys($this->ref_stack(), $needle, $strict);

		return $keys;
	}

	/** Find if the $value is in the collection.
	 *	\param	$value mixed
	 *	\return bool
	 */
	public		function has_value($value)
	{
		return in_array($value, $this->ref_stack());
	}

	/** Find if the current collection has at least one value from the $compared
	 *	\param	$compared collection
	 *	\return bool
	 */
	public		function has_intersection_with(collection $compared)
	{
		return 0 < count(\array_intersect($this->ref_stack(), $compared->ref_stack()));
	}

	/** Find if current collection has keys \param keys
	 *	\param	$keys collection
	 *	\return bool
	 */
	public		function has_keys(collection $keys)
	{
		return \count($keys->ref_stack()) === count(\array_intersect(\array_keys($this->ref_stack()), $keys->ref_stack()));
	}


	/** Remove element that match $value.
	 *	\return		bool	Success of removing.
	 */
	public		function remove($needle, $all = false)
	{
		$removed = false;
		$matches = $this->search($needle);

		if($matches === false)
			return false;

		if($all)
			foreach($matches as $match)
				$removed |= count(array_splice($this->ref_stack(), $match, 1));
		else
			$removed |= count(array_splice($this->ref_stack(), $matches[0], 1));

		return $removed;
	}

	/** Push the given on the stack.
	 *	\return		collection	reference on $this
	 */
	public		function push($element)
	{
		array_push($this->ref_stack(), $element);
		return $this;
	}

	/** Pop the last inserted element.
	 *	\return		mixed		The poped element.
	 */
	public		function pop()
	{
		return array_pop($this->ref_stack());
	}

	/** Pop the first inserted element.
	 *	\return		mixed		The poped element.
	 */
	public		function shift()
	{
		return array_shift($this->ref_stack());
	}

	/** Reference the front element of the collection.
	 *	\return		reference on $this
	 */
	public		function & front()
	{
		reset($this->ref_stack());

		return $this->ref_stack()[key($this->ref_stack())];
	}

	/** Reference the back element of the collection.
	 *	\return		reference on $this
	 */
	public		function & back()
	{
		end($this->ref_stack());

		return $this->ref_stack()[key($this->ref_stack())];
	}

	/** Spin off the collection.
	 *	\return		collection	reference on $this
	 */
	public		function reverse()
	{
		$s = $this->ref_stack();
		$s = array_reverse($s);
		$this->set_stack($s);
		return $this;
	}

	public		function get_column($key, $index_key=null)
	{
		$key = $this->filter_key($key);
		is_null($index_key) or $index_key = $this->filter_key($index_key);
		$column = \array_column($this->ref_stack(), $key, $index_key);
		$column = static::merge($column);
		return $column;
	}

	/** Check for existence of a key that reference an element of the collection.
	 *	\return		bool	The key exists.
	 */
	public		function has_key($needle)
	{
		return isset($this->ref_stack()[$this->filter_key($needle)]);
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
				$this[] = $value;
			else
				$this[$key] = $value;
		}

		return $this;
	}

	/* interface Iterator */
	public		function current()	{ return current($this->ref_stack()) ; }
	public		function next()		{ return next($this->ref_stack()) ; }
	public		function key()		{ return key($this->ref_stack()) ; }
	public		function rewind()	{ return reset($this->ref_stack()) ; }
	public		function valid()	{ return array_key_exists(key($this->ref_stack()), $this->ref_stack()) ; }

	/* interface Countable */
	public		function count()	{ return count($this->ref_stack()) ; }

	protected	function filter_key($candidate)
	{
		if(\is_integer($candidate) || \is_string($candidate))
			$filtered = $candidate;
		elseif(is_null($candidate))
			$filtered = null;
		else
			$filtered = (string) $candidate;
		return $filtered;
	}

	/* interface ArrayAccess */
	public		function offsetExists($key)
	{
		return array_key_exists($this->filter_key($key), $this->ref_stack());
	}
	
	public		function &offsetGet($key)
	{
		if(!$this->offsetExists($key))
			throw $this->_exception_offset_does_not_exists($key);

		return $this->ref_stack()[$this->filter_key($key)];
	}

	public		function offsetSet($key, $value)
	{
		if(is_null($key))
			$this->ref_stack()[] = $value;
		else
			$this->ref_stack()[$this->filter_key($key)] = $value;

		return $this->current();
	}

	public		function offsetUnset($key)
	{
		unset($this->ref_stack()[$this->filter_key($key)]);
	}

	/* interface JsonSerializable */
	public		function jsonSerialize()
	{
		return $this->get_stack();
	}

	/** Clean current elements of the collection, then copy each elements of the
	 *  copied collection. If it meets an object, the method clone it.
	 *
	 *  \param	$rhs	collection
	 *  \return	null
	 */
	protected		function _set_stack($rhs)
	{
		$a = array();
		$this->set_stack($a);
		unset($a);

		if(is_array($rhs) || $rhs instanceof iterator)
			foreach($rhs as $k => $v)
				if(is_scalar($v))
					$this[$k] = $v;
				elseif(is_object($v))
					$this[$k] = clone $v;
#				$this[$k] = $v;
				else
					throw $this->_exception('Don\'t know how to copy current value');
		elseif(!is_null($rhs))
			throw $this->_exception('Bad type.');

		return $this;
	}

	protected	function _get_stack()
	{
		// \warning	use $this->get_stack() to get a copy
		throw $this->_exception_readonly_attribute('stack');
	}

	protected	function set_stack(& $stack)
	{
		$this->_stack = $stack;
	}

	protected	function &ref_stack()
	{
		return $this->_stack;
	}

	protected	function _exception_offset_does_not_exists($key)
	{
		return $this->_exception_format('Key \'%s\' doesn\'t exist', $key);
	}

	/** array	Actual data that is accessed through collection
	 */
	protected	$_stack = array();
}

class collection_mutltivalue
	extends collection
{
	public		function offsetSet($offset, $value)
	{
		if($this->offsetExists($offset))
		{
			$current = $this->offsetGet($offset);
			if(!$current instanceof collection)
				$current = collection($current);

			$current[] = $value;
			parent::offsetSet($offset, $current);
		}
	}
}
