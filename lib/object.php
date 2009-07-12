<?php
/** \file
 *	Object coherent handling.
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

/** \package horn
 */
namespace horn ;

/**
 *
 */
class exception
	extends \exception
{
}

/** Ensure homogenic access to properties.
 *
 *  This is the base class for advanced object handling. Don't inherit directly from
 *  it. Use classes object_public (for world instanciable object), and object_protected
 *  (for self instanciable object only).
 *
 *  __set and __get will check for user defined protected assessor, with the
 *  following patterns and contracts.
 *  for setter : "_set_$name" 
 *  for getter : "_get_$name" 
 *	Accessors must return 
 *  As an example, we want to add specific meaning to parent attribute. So we
 *  have to define _set_parent and _get_parent methods. In that methods, you can
 *  process some stuff and return the value of attribute. To notice errors, you
 *  must raise an exception.
 *
 * void _set_parent(string $name, mixed $value) 
 * void _get_parent(string $name)
 *
 * \todo 		document value returning and readonly access
 * When extending object_base, you can specify magic behaviour on attribute access
 * and type safe attributes. To do so, you just have to populate static class
 * variable.
 * \see self::$_attrs_readonly
 * \see self::$_attrs_type
 * \see self::$_attrs_by_value
 *
 * \todo	Manage private attributes (idea : {$"p_$name"})
 */
class object_base
{
	/** List of attributes that must be returned by value instead of by
	 * 	reference
	static 
	protected	$_attrs_by_value = array() ;
	 */

	/** List of attributes that must be referenced and not copied when
	 *	copying en object.
	static 
	protected	$_attrs_referenced_on_copy = array() ;
	 */

	/** List of attributes that must not be set by foreigners
	static 
	protected	$_attrs_readonly = array() ;
	 */

	/** List of types indexed by the corresponding attribute name.
	static 
	protected	$_attrs_type = array(/* $name => $type * /) ;
	 */

	/** 
	 *	\return object_base	The new object cloned from $this
	 */
	final
	public		function __clone() 
	{ 
		$new = new static ;
		$new->copy($this) ;

		return $new ;
	}

	/** Getter on the $name attribute.
	 *	\return mixed	Reference on attribute
	 */
	final
	public		function & __get($name) 
	{ 
#		var_dump(__FUNCTION__, $name, $value) ;
		if($this->_is_attribute_readonly($name))
			$this->_throw_readonly_attribute($name) ;

		$method = "_get_$name" ; 
		if(method_exists($this, $method))
			return $this->$method($name) ;
		else
			return $this->_get($name) ;
	} 

	/** Setter on the $name attribute.
	 *	\param $name	string	Access name of attribute.
	 *	\param $value	mixed	New value of attribute
	 *	\return mixed	Reference on attribute
	 */
	final
	public		function & __set($name, $value) 
	{ 
#		var_dump(__FUNCTION__, $name, $value) ;
		if($this->_is_attribute_readonly($name))
			$this->_throw_readonly_attribute($name) ;

		$method = "_set_$name" ; 
		if(method_exists($this, $method))
			$this->$method($value) ;
		else
			$this->_set($name, $value) ;

		return $this->_return($name) ; 
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
			: isset($value) ;
	}

	/**	Assign the $object_source to $this with inheritance care. The behaviour can be custom, just override protected
	 *	functions _copy_object, _copy_descendant and _copy_ancestor.
	 *	\param	$object_source	object_base	
	 *	\return	object_base	Return a reference on $this.
	 */
	final
	public		function copy(object_base $object_source)
	{
		// optimization : we won't to copy object in it-self.
		if($this->is_same($object_source))
			return $this ;

		if($object_source instanceof static)
			$this->_copy_object($object_source) ;
		elseif(is_a($object_source, get_class($this)))
			$this->_copy_descendant($object_source) ;
		elseif(is_a($this, get_class($object_source)))
			$this->_copy_ancestor($object_source) ;
		else
			$this->_throw_cant_copy_object($object_source) ;

		return $this ;
	}

	/** Returns true if the compared object is stricly equal to $this.
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
		foreach($this->get_attributes_class() as $name => $default_value)
			// Assign public name of attribute instead of protected name permits us to custom the behaviour of
			// attributes. For exemple, if the attribute must contain an object, if the original value is null, we want
			// assign null to the attribute, but just say to the attribute to nullify it-self.
			if($name[0] == '_')
			{
				$apparent_name = substr($name, 1) ;
				$this->$apparent_name = $default_value ;
			}

		return $this ;
	}

	/** Check for forbiden attributes
	 *  \throw	exception	When at least one attribute don't begin with
	 *  					an underscore
	 */
	final
	protected	function _check_attributes()
	{
		$attrs = $this->get_attributes_object() ;
		foreach($attrs as $attr)
			if($attr[0] != '_')
				throw new exception("Attribute '$attr' is not allowed.") ;
	}

	/** Determines actual attribute name and returns it.
	 *  \throw	exception	When actual attribute doesn't exists.
	 *
	 *  \param	$name	string	The access attribute name.
	 *  \return			string	The actual attribute name.
	 */
	final
	protected   function _actual_name($name) 
	{ 
		$actual_name = "_$name" ; 
 
		if(!array_key_exists($actual_name, get_object_vars($this)))
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
			$this->$actual_name->copy($value) ;
		if(!is_null($this->$actual_name) && is_object($value))
			$this->$actual_name = clone $value ;
		else
			$this->$actual_name = $value ;
	}

	/** Default behaviour on setting an attribute.
	 *	\see self::__get
	 *	\todo	i have a disign issue here. I can't have easy custom getter with read only and by-value semantic.
	 */
	protected	function & _get($name)
	{
		/*
		$actual_name = $this->_actual_name($name) ; 

		if(!is_null($this->$actual_name) && is_object($value))
			$this->$actual_name = clone $value ;
		*/

		return $this->_return($name) ; 
	}

	/** \todo	a correct thing
	 */
	final
	protected	function _unset($name) 
	{
		/*
		if($this->$name instanceof object_base)
			$this->$name->reset() ;
		elseif(is_scalar($this->$name))
		*/

		$actual_name = $this->_actual_name($name) ; 
		unset($this->$actual_name) ;
	}

	/** Generic copier for object_base
	 */
	final
	protected	function _copy_object(object_base $object_source)
	{
		$attrs = $this->get_attributes_object() ;
		$this->_copy_attributes_from($attrs, $object_source) ;
	}

	/** Copy attributes listed in $attrs.
	 */
	final
	protected	function _copy_attributes_from($attrs, object_base $object_source)
	{
		foreach($attrs as $attr_name)
			if($attr_name[0] == '_')
			{
				$apparent_name = substr($attr_name, 1) ;
				$this->$apparent_name = $object_source->$apparent_name ;
			}
	}

	/** Generic descendant copier 
	 *	\param	$object_source object_base
	 */
	final
	protected	function _copy_descendant(object_base $object_source)
	{
		assert('$object_source instanceof static') ;

		$this->reset() ;
		$attrs = $this->get_attributes_class($this) ;
		$this->_copy_attributes_from($attrs, $object_source) ;
	}

	/** Generic ancestor copier 
	 */
	final
	protected	function _copy_ancestor(object_base $object_source)
	{
		$this->reset() ;
		$attrs = $object_source->get_attributes_object() ;
		$this->_copy_attributes_from($attrs, $object_source) ;
	}

	/** Return a copy or a reference of the attribute.
	 *  \param	$name	string	The access attribute name.
	 *  \return			mixed	The copy or reference of attribute
	 */
	final
	protected	function & _return($name)
	{
		$actual_name = $this->_actual_name($name) ; 

		if(is_null($this->$actual_name))
			return null ;

		/*
		return $this->_is_attribute_by_value($name)
			? $this->_return_value($actual_name)
			: $this->$actual_name ;
		*/
		return $this->$actual_name ;
	}

	/** Return a copy of value attribute instead of a reference on it
	  * \param	$actual_name	string	The actual name of native attribute.
	  * \return	mixed	The copy of attribute value.
	  */
	final
	protected	function _return_value($actual_name)
	{
		return is_object($this->$actual_name)
			? clone $this->$actual_name
			: $this->$actual_name ;
	}

	/** \warning	To call like instance method.
	 */
	final
	static
	protected	function _is_attribute_by_value($name)
	{
		assert('!isset($this)') ;
		return in_array($name, static::$_attrs_by_value) ;
	}

	/**
	 */
	final
	protected	function _is_attribute_readonly($name)
	{
		return in_array($name, static::$_attrs_readonly) ;
	}

	/** \todo
	 */
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

	/** \throw	exception
	 */
	protected	function _throw_attribute_missing($name)
	{
		$msg = sprintf('Attribute \'%s\' doesn\'t exist in \'%s\'.'
				, $name, get_class($this)) ; 
		throw new exception($msg) ;
	}

	/** \throw exception
	 */
	protected	function _throw_cant_copy_object($object_source)
	{
		$msg = sprintf('Supplied object of class \'%s\' can\'t be copied in this class \'%s\'.'
				, get_class($object_source)
				, get_class($this)
				) ;
		throw new exception($msg) ;
	}

	/** \throw exception
	 */
	protected	function _throw_cant_set_attribute($object_source, $attr_name)
	{
		$msg = sprintf('Supplied object of class \'%s\' can\'t be assign to attribute \'%s\'.'
			 , get_class($object_source), $attr_name) ;
		throw new exception($msg) ;
	}

	/** \throw exception
	 */
	protected	function _throw_unexpected()
	{
		throw new exception('Unexpected happend.') ;
	}

	/** \throw exception
	 */
	protected	function _throw_readonly_attribute($name)
	{
		throw new exception("Attribute '$name' is readonly.") ;
	}

	/** \throw exception
	 */
	protected	function _throw_missing_method($name)
	{
		throw new exception("Instance method '$name' is missing.") ;
	}

}  
 

/** Generic object class with public constructor.
 */
class object_public
	extends		object_base
{
	/** 
	 */
	public		function __construct()
	{
		$this->_check_attributes() ;
	}
}

/** Generic object class with protected constructor.
 */
class object_protected
	extends		object_base
{
	/**
	 */
	protected	function __construct()
	{
		$this->_check_attributes() ;
	}
}

/** Generic object class with private constructor.
 */
class object_private
	extends		object_base
{
	/** 
	 */
	private		function __construct()
	{
		$this->_check_attributes() ;
	}
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

