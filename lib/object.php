<?php
/** \file
 *	Object coherent handling.
 *	\see object_base
 *
 *	object_public, object_protected and object_private are defined becasue you can't
 *	change the 
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

/* :%s/\$this->_throw_format(\(.*\), \(.*\)) ;$/throw new exception(sprintf(\1, \2)) ;/g
 */
namespace horn\lib ;

require_once 'horn/lib/horn.php' ;
require_once 'horn/lib/exception.php' ;

/** Ensure homogenic access to properties.
 *
 *  This is the base class for advanced object handling.
 *
 *	\warning	Don't inherit directly from it. Use classes object_public (for world
 *				instanciable object), and object_protected or object_private (for self
 *				instanciable object only). In fact, change function scope when inheriting
 *				isn't allowed. But I maybe need to do some stuff at construct time. So the
 *				flavored object need.
 *
 *	When defining a new class, you can declare public attributes. Accessors will not be
 *	called on it because of their accessibility to world. In fact, __set and __get are
 *	called only when the accessed attribute doesnt exist or isn't in scope
 *	(public/private awareness).
 *
 *	Actual name 
 *
 *	To ease daily use, magic methods are implemented and finalize, to avoid overloading
 *	and constraint the following behaviour. Get $name, the variable that contains
 *	attribute name.
 *
 *	Get the attribute $name :
 *	1. Check existence of static::$name
 *	2. If (1) is true, returns static::$name
 *	3. Build getting $method = "_get_$name"
 *	4. If (3) exists, call static::$method and returns.
 *	5. Returns null
 *	
 *	Set the attribute $name :
 *	
 *
 *	So magic methods will check for user defined protected assessor, with the following
 *	patterns and contracts.
 *  for setter : "_set_$name" 
 *  for getter : "_get_$name" 
 *	Your function must return a reference, a copied value or null.
 *  As an example, we want to add specific meaning to parent attribute. So we
 *  have to define _set_parent and _get_parent methods. In that methods, you can
 *  process some stuff and return the value of attribute. To notice errors, you
 *  must raise an exception.
 *
 *	void _set_parent(string $name, mixed $value) 
 *	void _get_parent(string $name)
 *
 *	If the attribute is readonly, consider to use _throw_readonly_attribute.
 *
 *	\todo	redonly attribute generic approach
 *	\todo	by-value vs by-ref semantic, find an acceptable way
 *
 */
abstract
class object_base
{
	/** Clone current object and returns it. If a _clone method
	 *	exists on the object, it will be used to create the new 
	 *	object. This _clone member function is mandatory if the
	 *	ctor takes arguments.
	 *	\return object_base	The new object cloned from $this
	 */
	final
	public		function __clone() 
	{ 
		if(method_exists($this, '_clone'))
			$new = $this->_clone() ;
		else
		{
			$new = new static ;
			$new->assign($this) ;
		}

		return $new ;
	}

	/** Getter on the $name attribute.
	 *	\param	$name	string
	 *	\return ref		Reference on attribute
	 */
	final
	public		function & __get($name) 
	{ 
#		var_dump(__FUNCTION__, $name, $value) ;
		$method = "_get_$name" ; 
		if(method_exists($this, $method))
			return $this->$method($name) ;
		else
			return $this->_get($name) ;
	} 

	/** Setter on the $name attribute.
	 *	\param $name	string	Access name of attribute.
	 *	\param $value	mixed	New value of attribute
	 *	\return ref		Reference on attribute
	 */
	final
	public		function & __set($name, $value) 
	{ 
#		var_dump(__FUNCTION__, $name, $value) ;
		$method = "_set_$name" ; 
		if(method_exists($this, $method))
			$this->$method($value) ;
		else
			$this->_set($name, $value) ;

		return $this->__get($name) ; 
	} 
 
	/** Unsetter on the $name attribute.
	 *	\param $name	string	Access name of attribute.
	 */
	final
	public		function __unset($name) 
	{ 
		$method = "_unset_$name" ; 
		if(method_exists($this, $method))
			$this->$method() ;
		else
			$this->_unset($name) ;

		return null ;
	}

	/** Check if $name attribute is set.
	 *	\param	$name	string	Access name of attribute.
	 *	\return	bool
	 */
	final
	public		function __isset($name) 
	{ 
		$method = "_isset_$name" ; 
		return method_exists($this, $method)
			? $this->$method()
			: $this->_isset($name)
			;
	}

	/**	Assign the $object_source to $this with inheritance care. The behaviour can be custom, just override protected
	 *	functions _assign_object, _assign_descendant and _assign_ancestor.
	 *	\param	$object_source	object_base	
	 *	\return	object_base	Return a reference on $this.
	 */
	final
	public		function assign(object_base $object_source = null)
	{
		if(is_null($object_source))
			$this->reset() ;
		elseif($this->is_same($object_source))
			/* optimization : we won't to assign object in it-self. */ ;
		elseif($object_source instanceof static)
			$this->_assign_object($object_source) ;
		elseif(is_a($object_source, get_class($this)))
			$this->_assign_descendant($object_source) ;
		elseif(is_a($this, get_class($object_source)))
			$this->_assign_ancestor($object_source) ;
		else
			$this->_throw_cant_assign_object($object_source) ;

		return $this ;
	}

	/** Returns true if the compared object is strictly equal to $this.
	 *	\param	$compared
	 *	\return bool
	 */
	public		function is_same(object_base $compared)
	{
		return $this === $compared ;
	}

	/**
	 *	\param	$compared
	 *	\return bool
	 */
	public		function is_equal(object_base $compared)
	{
		return $this->is_same($compared) || $this == $compared ;
	}

	final
	public		function get_attributes_default_values()
	{
		return get_class_vars(get_class($this)) ;
	}

	final
	public		function get_attributes_class()
	{
		return array_keys(get_class_vars(get_class($this))) ;
	}

	final
	public		function get_attributes_object()
	{
		return array_keys(get_object_vars($this)) ;
	}

	/** \todo
	final
	public		function iterate_attributes()
	{
	}
	*/

	/** Reset every instance attributes to their class defined values.
	 *	\return $this
	 */
	final
	public		function reset()
	{
		foreach($this->get_attributes_default_values() as $name => $default_value)
			$this->__set($name, $default_value) ;

		return $this ;
	}

	/** Determines actual attribute name and returns it. Private and protected attributes
	 *	are prefixed by an underscore.
	 *  \throw	exception	When actual attribute doesn't exists.
	 *
	 *  \param	$name	string	The access attribute name.
	 *  \return			string	The actual attribute name.
	 */
	final
	protected   function _actual_name($name) 
	{ 
		$attrs = $this->get_attributes_object() ;
		$actual_name = in_array($name, $attrs) ? $name : "_$name" ;
 
		if(!in_array($actual_name, $this->get_attributes_object()))
			$this->_throw_attribute_missing($name) ;

		return $actual_name ;
	} 

	/** Default behaviour on setting an attribute.
	 *	\see self::__set
	 */
	protected	function & _set($name, $value)
	{
		$actual_name = $this->_actual_name($name) ; 

		if($this->$actual_name instanceof self)
			$this->$actual_name->assign($value) ;
		if(!is_null($this->$actual_name) && is_object($value))
			$this->$actual_name = clone $value ;
		else
			$this->$actual_name = $value ;

		return $this->$actual_name ;
	}

	/** Return a reference of the attribute.
	 *  \param	$name	string	The access attribute name.
	 *  \return			mixed	The assign or reference of attribute
	 */
	final
	protected	function & _get($name)
	{
		$actual_name = $this->_actual_name($name) ; 
		return $this->$actual_name ;
	}

	/** Default method for checking if an attribute is set.
	 */
	final
	protected	function _isset($name) 
	{
		try { $actual_name = $this->_actual_name($name) ; }
		catch(exception $e) { return false ; }
		return isset($this->$actual_name) ;
	}

	/** Default method to unset a property.
	 */
	final
	protected	function _unset($name) 
	{
		$actual_name = $this->_actual_name($name) ; 
		$this->$actual_name = null ;
#		unset($this->$actual_name) ;
	}

	/** Generic copier for object_base
	 */
	final
	protected	function _assign_object(object_base $object_source)
	{
		$attrs = $this->get_attributes_object() ;
		$this->_assign_attributes_from($attrs, $object_source) ;
	}

	/** Copy attributes listed in $attrs.
	 */
	final
	protected	function _assign_attributes_from($attrs, object_base $object_source)
	{
		foreach($attrs as $attr_name)
		{
			$apparent_name = $this->_actual_name($attr_name) ;
			$this->__set($attr_name, $object_source->$apparent_name) ;
		}
	}

	/** Generic descendant copier 
	 *	\param	$object_source object_base
	 */
	final
	protected	function _assign_descendant(object_base $object_source)
	{
		if(! $object_source instanceof static)
			$this->_throw_not_child($object_source) ;

		$this->reset() ;
		$attrs = $this->get_attributes_class($this) ;
		$this->_assign_attributes_from($attrs, $object_source) ;
	}

	/** Generic ancestor copier 
	 */
	final
	protected	function _assign_ancestor(object_base $object_source)
	{
		$this->reset() ;
		$attrs = $object_source->get_attributes_object() ;
		$this->_assign_attributes_from($attrs, $object_source) ;
	}

	/** Return a assign of value attribute instead of a reference on it
	  * \param	$actual_name	string	The actual name of native attribute.
	  * \return	mixed	The assign of attribute value.
	  */
	final
	protected	function _return_value($actual_name)
	{
		return is_object($this->$actual_name)
			? clone $this->$actual_name
			: $this->$actual_name ;
	}

	/** \todo
	final
	protected	function _check_type($name, $value)
	{
		if(!in_array($name, static::$_attrs_type))
			return ;

		$good_type = false ;
		if(isset(static::$_attrs_type[$name]))
			$type = strtolower(static::$_attrs_type[$name]) ;

		if(isset($type))
		{
			if(is_scalar($value))
			{
				switch($type)
				{
					case 'int' :
					case 'integer' :
						$good_type = is_integer($value) ;
						break ;
					case 'float' :
						$good_type = is_float($value) ;
						break ;
					case 'string' :
						$good_type = is_string($value) ;
						break ;
					case 'boolean' :
						$good_type = is_bool($value) ;
						break ;
					case 'array' :
						$good_type = is_array($value) ;
						break ;
					default:
						throw new exception("Unkown scalar type '$type'.") ;
				}
			}
			elseif(is_object($value))
				$good_type = class_exists($type) && $value instanceof $type ;
#				$good_type = class_exists($type) && is_a($value, $type) ;
			elseif(is_resource($value))
				$good_type = $type == 'resource' ;
			else
				throw new exception("Unexpected type '$type'") ;
		}

		if(!$good_type)
			throw new exception("Attribute '$name' has not the requierd type.") ;

		return $good_type ;
	}
	 */

	/** \throw	exception
	 */
	protected	function _throw($msg)
	{
		throw new exception($msg, dump($this)) ;
	}

	/** \throw	exception
	 */
	protected	function _throw_format($fmt)
	{
		$msg = call_user_func_array('sprintf', func_get_args()) ;
		$this->_throw($msg) ;
	}

	/** \throw	exception
	 */
	protected	function _throw_attribute_missing($name)
	{
		$this->_throw_format('Attribute \'%s\' doesn\'t exist in \'%s\'.', $name, get_class($this)) ; 
	}

	/** \throw exception
	 */
	protected	function _throw_cant_assign_object($object_source)
	{
		$this->_throw_format('Supplied object of class \'%s\' can\'t be copied in this class \'%s\'.'
				, get_class($object_source)
				, get_class($this)
				) ;
	}

	/** \throw exception
	 */
	protected	function _throw_cant_set_attribute($object_source, $attr_name)
	{
		$this->_throw_format('Supplied object of class \'%s\' can\'t be assign to attribute \'%s\'.'
			 , get_class($object_source), $attr_name) ;
	}

	/** \throw exception
	 */
	protected	function _throw_unexpected()
	{
		$this->_throw('Unexpected happend.') ;
	}

	/** \throw exception
	 */
	protected	function _throw_readonly_attribute($name)
	{
		$this->_throw("Attribute '$name' is readonly.") ;
	}

	/** \throw exception
	 */
	protected	function _throw_missing_method($name)
	{
		$this->_throw("Instance method '$name' is missing.") ;
	}

}  
 

/** Generic object class with public constructor.
 */
class object_public
	extends		object_base
{
	/** 
	 */
	public		function __construct() { }
}

/** Generic object class with protected constructor.
 */
class object_protected
	extends		object_base
{
	/**
	 */
	protected	function __construct() { }
}

/** Generic object class with private constructor.
 */
class object_private
	extends		object_base
{
	/** 
	 */
	private		function __construct() { }
}


/** Generic object class with private constructor.
 */
class object_singleton
	extends		object_private
{
	static
	public		function get()
	{
		if(is_null(static::$instance))
			static::$instance = new self ;

		return static::$instance ;
	}
}

