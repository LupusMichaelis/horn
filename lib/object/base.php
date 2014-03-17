<?php
/**
 *	Object coherent handling.
 *	\see object\base
 *
 *	object_public, object_protected and object_private are defined becasue you can't
 *	change the constructor accessibility while inheriting
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

namespace horn\lib\object;
use horn\lib as h;

h\import('lib/exception');

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
 *	If the attribute is readonly, consider to use _exception_readonly_attribute.
 *
 *	\todo	readonly attribute generic approach (!= const)
 *	\todo	by-value vs by-ref semantic, find an acceptable way
 *
 */
abstract
class base
{
	const		exception_class = '\horn\lib\exception';
	protected	$exception_class = self::exception_class;

	/** Default behaviour on setting an attribute.
	 *	\see self::__set
	 */
	abstract
	protected	function _set($name, $value);

	/** Return a reference of the attribute.
	 *  \param	$name	string	The access attribute name.
	 *  \return			mixed	The assign or reference of attribute
	 */
	abstract
	protected	function & _get($name);

	/** Default method for checking if an attribute is set.
	 */
	abstract
	protected	function _isset($name);

	/** Default method to unset a property.
	 */
	abstract
	protected	function _unset($name);

	/** Clone current object and returns it. If a _clone method
	 *	exists on the object, it will be used to create the new
	 *	object. This _clone member function is mandatory if the
	 *	ctor takes arguments.
	 *	\return object\base	The new object cloned from $this
	 */
	final
	public		function __clone()
	{
		if(method_exists($this, '_clone'))
			$new = $this->_clone();
		else
		{
			$new = new static;
			$new->assign($this);
		}

		return $new;
	}

	/** Getter on the $name attribute.
	 *	\param	$name	string
	 *	\return ref		Reference on attribute
	 */
	final
	public		function & __get($name)
	{
#		var_dump(__FUNCTION__, $name, $value);
		$method = "_get_$name" ;
		if(method_exists($this, $method))
			return $this->$method();
		else
			return $this->_get($name);
	}

	/** Setter on the $name attribute.
	 *	\param $name	string	Access name of attribute.
	 *	\param $value	mixed	New value of attribute
	 *	\return ref		Reference on attribute
	 */
	final
	public		function & __set($name, $value)
	{
#		var_dump(__FUNCTION__, $name, $value);
		$method = "_set_$name" ;
		if(method_exists($this, $method))
			$this->$method($value);
		else
			$this->_set($name, $value);

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
			$this->$method();
		else
			$this->_unset($name);

		return null;
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

	/** For a lot of types, it is not a good idea to cast in a string. Take care when you
	 *  make the decision.
	 */
	final
	public		function __tostring()
	{
		if(!method_exists($this, '_to_string'))
			trigger_error(sprintf('No %s::_to_string method provided', get_class($this)), E_USER_ERROR);

		try {
			$s = $this->_to_string();
		} catch(\exception $e) {
			/// @todo better format
			die('' . $e->xdebug_message . $this->getMessage());
		}

		return (string) $s;
	}

	/**	Assign the $object_source to $this with inheritance care. The behaviour can be custom, just override protected
	 *	functions _assign_object, _assign_descendant and _assign_ancestor.
	 *	\param	$object_source	object\base
	 *	\return	object\base	Return a reference on $this.
	 */
	final
	public		function assign(base $object_source = null)
	{
		if(is_null($object_source))
			$this->reset();
		elseif($this->is_same($object_source))
			/* optimization : we won't to assign object in it-self. */;
		elseif($object_source instanceof static)
			$this->_assign_object($object_source);
		elseif(is_a($object_source, get_class($this)))
			$this->_assign_descendant($object_source);
		elseif(is_a($this, get_class($object_source)))
			$this->_assign_ancestor($object_source);
		else
			throw $this->_exception_cant_assign_object($object_source);

		return $this;
	}

	/** Returns true if the compared object is strictly equal to $this.
	 *	\param	$compared
	 *	\return bool
	 */
	public		function is_same(base $compared)
	{
		return $this === $compared;
	}

	/**
	 *	\param	$compared
	 *	\return bool
	 */
	public		function is_equal(base $compared)
	{
		return $this->is_same($compared) || $this == $compared;
	}

	/**
	 */
	final
	public		function get_attributes_default_values()
	{
		$r = $this->_reflection_of_class_();
		$defaults = $r->getDefaultProperties();
		return $defaults;
	}

	/**
	 */
	final
	public		function get_attributes_class()
	{
		return $this->_reflection_of_class_()->getProperties
				(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
	}

	/**
	 */
	final
	public		function get_attributes_object()
	{
		$attributes = array();
		$properties = $this->_reflection_of_object_()->getProperties
				(\ReflectionProperty::IS_PUBLIC
				 | \ReflectionProperty::IS_PROTECTED
				 & ~\ReflectionProperty::IS_STATIC
				 );
		foreach($properties as $property)
			$attributes[] = $property->name;

		return $attributes;
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
			$this->__set($name, $default_value);

		return $this;
	}

	/** Determines actual attribute name and returns it. Private and protected attributes
	 *	are prefixed by an underscore.
	 *  \throw	h\exception	When actual attribute doesn't exists.
	 *
	 *  \param	$name	string	The access attribute name.
	 *  \return			string	The actual attribute name.
	 */
	final
	protected   function _actual_name($name)
	{
		$attrs = $this->get_attributes_object();
		$actual_name = in_array($name, $attrs) ? $name : "_$name";

		if(!in_array($actual_name, $this->get_attributes_object()))
			throw $this->_exception_attribute_missing($name);

		return $actual_name;
	}

	/** Generic copier for base
	 */
	final
	protected	function _assign_object(base $object_source)
	{
		$attrs = $this->get_attributes_object();
		$this->_assign_attributes_from($attrs, $object_source);
	}

	/** Copy attributes listed in $attrs.
	 */
	final
	protected	function _assign_attributes_from($attrs, base $object_source)
	{
		foreach($attrs as $attr_name)
		{
			$apparent_name = $this->_actual_name($attr_name);
			$this->__set($attr_name, $object_source->$apparent_name);
		}
	}

	/** Generic descendant copier
	 *	\param	$object_source base
	 */
	final
	protected	function _assign_descendant(base $object_source)
	{
		if(! $object_source instanceof static)
			throw $this->_exception_not_child($object_source);

		$this->reset();
		$attrs = $this->get_attributes_class($this);
		$this->_assign_attributes_from($attrs, $object_source);
	}

	/** Generic ancestor copier
	 */
	final
	protected	function _assign_ancestor(base $object_source)
	{
		$this->reset();
		$attrs = $object_source->get_attributes_object();
		$this->_assign_attributes_from($attrs, $object_source);
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
			: $this->$actual_name;
	}

	/** \todo
	final
	protected	function _check_type($name, $value)
	{
		if(!in_array($name, static::$_attrs_type))
			return;

		$good_type = false;
		if(isset(static::$_attrs_type[$name]))
			$type = strtolower(static::$_attrs_type[$name]);

		if(isset($type))
		{
			if(is_scalar($value))
			{
				switch($type)
				{
					case 'int' :
					case 'integer' :
						$good_type = is_integer($value);
						break;
					case 'float' :
						$good_type = is_float($value);
						break;
					case 'string' :
						$good_type = is_string($value);
						break;
					case 'boolean' :
						$good_type = is_bool($value);
						break;
					case 'array' :
						$good_type = is_array($value);
						break;
					default:
						throw new h\exception("Unkown scalar type '$type'.");
				}
			}
			elseif(is_object($value))
				$good_type = class_exists($type) && $value instanceof $type;
#				$good_type = class_exists($type) && is_a($value, $type);
			elseif(is_resource($value))
				$good_type = $type == 'resource';
			else
				throw new h\exception("Unexpected type '$type'");
		}

		if(!$good_type)
			throw new h\exception("Attribute '$name' has not the requierd type.");

		return $good_type;
	}
	 */

	/*
	*/
	final
	public		function __call($method_name, $arguments)
	{
		if(method_exists($this, $method_name))
			// This is an attempt to access the method from an unallowed scope
			throw $this->_exception_format('Function \'%s\' is protected or private in \'%s\'.'
					, $method_name, get_class($this));

		$result = null;
		$is_managed = false;
		foreach(get_class_methods($this) as $handler_method)
			if(0 === strpos($handler_method, '_call_'))
				if(true === ($is_managed = $this->$handler_method($method_name, $arguments, $result)))
					break;

		if($is_managed)
			return $result;

		if(method_exists($this, '_call'))
			return $this->_call($method_name, $arguments);

		throw $this->_exception_format('Function \'%s\'::\'%s\' can\'t be handled', get_class($this), $method_name);
	}

	/**
	 */
	protected	function _call_exception($method_name, $arguments, &/*out*/$result)
	{
		$pos = strpos($method_name, '_exception');
		if(0 !== $pos)
			return false;

		$actual_method_name = substr($method_name, $pos);

		$actual_method_name = $actual_method_name . '_ex';
		if(!method_exists($this, $actual_method_name))
			return false;

		$callback = array($this, $actual_method_name);
		$arguments = array_merge(array($this->get_exception_class()), $arguments);

		$result = call_user_func_array($callback, $arguments);

		return true;
	}

	/**
	 */
	private		function get_exception_class()
	{
		return $this->exception_class;
	}

	/**
	 */
	protected	function _exception_ex($exception_class, $msg)
	{
		return new $exception_class($msg, h\dump($this));
	}

	/**
	 */
	protected	function _exception_format_ex($exception_class, $fmt)
	{
		$args = func_get_args();
		array_shift($args);
		$msg = call_user_func_array('sprintf', $args);
		return $this->_exception_ex($exception_class, $msg);
	}

	/**
	 */
	protected	function _exception_attribute_missing($name)
	{
		return $this->_exception_format('Attribute \'%s\' doesn\'t exist in \'%s\'.', $name, get_class($this)) ;
	}

	/**
	 */
	protected	function _exception_cant_assign_object($object_source)
	{
		return $this->_exception_format('Supplied object of class \'%s\' can\'t be copied in this class \'%s\'.'
				, get_class($object_source)
				, get_class($this)
				);
	}

	/**
	 */
	protected	function _exception_cant_set_attribute($object_source, $attr_name)
	{
		return $this->_exception_format('Supplied object of class \'%s\' can\'t be assign to attribute \'%s\'.'
			 , get_class($object_source), $attr_name);
	}

	/**
	 */
	protected	function _exception_unexpected()
	{
		return $this->_exception('Unexpected happend.');
	}

	/**
	 */
	protected	function _exception_readonly_attribute($name)
	{
		return $this->_exception("Attribute '$name' is readonly.");
	}

	protected	function _exception_missing_method($name)
	{
		return $this->_exception("Instance method '$name' is missing.");
	}

	protected	function _exception_not_implemented($class_name, $function_name)
	{
		return $this->_exception_format('Method \'%s::%s\' isn\'t implemented.', $class_name, $function_name);
	}

	private		function _reflection_of_object_()
	{
		if(is_null($this->__reflection_object__))
			$this->__reflection_object__ = new \ReflectionClass($this);

		return $this->__reflection_object__;
	}

	private		function _reflection_of_class_()
	{
		if(is_null($this->__reflection_class__))
			$this->__reflection_class__ = new \ReflectionClass(get_class($this));

		return $this->__reflection_class__;
	}

	private		$__reflection_object__;
	private		$__reflection_class__;
}
