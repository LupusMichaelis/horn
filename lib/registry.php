<?php
/** Simple registry with PHP array backend
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

namespace horn ;

require_once 'horn/lib/object.php' ;

class registry
	extends		object_public
	implements	\ArrayAccess
{
	protected	$_tree = array(0 => null) ;
	protected	$_current ;

	static
	public		function load($array)
	{
		$registry = new self ;
		$registry->_tree = $array ;
		$registry->cd('/') ;

		return $registry ;
	}

	public		function __construct()
	{
		$ref = & $this->_tree ;
		$this->_current = (object) array('ref' => &$ref) ;
	}

	public		function cd($path)
	{
		$current = & $this->_seek_node($path) ;
		unset($this->_current->ref) ;
		$this->_current->ref = & $current ;
		return $this ;
	}

	public		function offsetExists($path)
	{
		try
		{
			$node = & $this->_seek_node($path) ;
			return is_array($node) ;
		}
		catch(exception $e)
		{
			return false ;
		}
	}

	public		function offsetGet($path)
	{
		$node = & $this->_seek_node($path) ;
		return isset($node[0]) ? $node[0] : null ;
	}

	public		function offsetUnset($path)
	{
		$node = & $this->_seek_node($path) ;
		$node = null ;
	}

	public		function offsetSet($path, $value)
	{
		$keys = $this->_explode_path($path) ;

		// If it is absolute path
		if(strpos($path, '/') === 0)
		{
			$node = & $this->_tree ;
			array_shift($keys) ;
		}
		else
			$node = & $this->_current->ref ;

		// go in tree as deeper as we can
		while(is_string($key = array_shift($keys)))
		{
			if(!empty($key) && isset($node[$key]) && is_array($node[$key]))
			{
				// reference swaping
				unset($n) ;		$n = & $node[$key] ;
				unset($node) ;	$node = & $n ;
			}
			else
				break ;
		}

		// create branch if it doesn't exist
		do
		{
			if(!empty($key))
			{
				$node[$key] = array(0 => null) ;
				// reference swaping
				unset($n) ;		$n = & $node[$key] ;
				unset($node) ;	$node = & $n ;
			}
		}
		while(is_string($key = array_shift($keys))) ;

		$node[0] = $value ;
	}

	public		function childs($path = '')
	{
		$node = & $this->_seek_node($path) ;
		return array_filter(array_keys($node), 'is_string') ;
	}

	protected	function & _seek_node($path)
	{
		$keys = $this->_explode_path($path) ;

		if(strpos($path, '/') === 0)
		{
			$node = & $this->_tree ;
			array_shift($keys) ;
		}
		else
			$node = & $this->_current->ref ;

		while(is_string($key = array_shift($keys)))
		{
			if(!empty($key))
				if(isset($node[$key]) && is_array($node[$key]))
				{
					// reference swaping
					unset($n) ;		$n = & $node[$key] ;
					unset($node) ;	$node = & $n ;
				}
				else
					$this->_throw_format('Couldn\'t path to (%s).', $path) ;
		}

		return $node ;
	}

	protected	function _explode_path($path)
	{
		$keys = explode('/', $path) ;

		if(count($keys) < 1)
			$this->_throw_format('Invalid path "%s".', $path) ;

		return $keys ;
	}
}

