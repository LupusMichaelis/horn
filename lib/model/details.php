<?php
/** Model holder
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2014, Lupus Michaelis
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

namespace horn\lib\model;
use \horn\lib as h;

h\import('lib/object');

class exception extends h\exception { }

abstract
class data
	extends h\object_public
{
	const		exception_class = '\horn\lib\model\exception';
	protected	$exception_class = self::exception_class;

	public		function __construct(h\model $parent)
	{
		$this->_model = $parent;
		$this->_cache = h\collection();
		parent::__construct();
	}

	/*
	abstract
	public		function get_by_id($id);
	*/

	protected	$_name;
	protected	$_model;
	protected	$_cache;
}

class cache
	extends data
{
	public		function __construct(h\model $parent)
	{
		$this->_cache = h\collection();
		parent::__construct($parent);
	}

	public		function get_by_id($thing_id)
	{
		$thing = null;
		
		if(isset($this->cache[$thing_id]))
			$thing = $this->cache[$thing_id];

		return $thing;
	}

	protected	$_cache;
}

// XXX TODO Possibility to skip a source, or to invalidate
class proxy
	extends h\object_public
{
	public		function __construct()
	{
		$this->_name = h\string('');
		$this->_sources = h\collection();
		parent::__construct();
	}

	public		function get_by_id($id)
	{
		$thing = $this->_sources['cache']->get_by_id($id);

		if(!is_null($thing))
			return $thing;

		$thing = $this->_source['reference']->get_by_id($id);
		$this->_source['cache']->cache[$thing->id] = $thing;

		return $thing;
	}
	
	protected	function _call($method_name, $arguments)
	{
		foreach($this->_sources as $source)
		{
			try
			{
				return call_user_func_array(array($source, $method_name), $arguments);
			}
			catch(h\exception_method_not_exists $e)
			{
				// This method doesn't exist, pass away to the next source
				// XXX should log that for debug?
			}
		}

		parent::_call($method_name, $arguments);
	}

	protected	$_sources;
	protected	$_name;
}

