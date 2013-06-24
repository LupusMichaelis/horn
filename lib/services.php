<?php
/** Service providers
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2013, Lupus Michaelis
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
h\import('lib/db');

function get_service_provider($configuration)
{
	static $services;

	if(is_null($services))
		$services = new service_provider($configuration);

	return $services;
}

class service_exception
	extends exception
{
}

class service_provider
{
	const		exception_class = '\horn\lib\service_exception';

	private		$_configuration = array();
	private		$_cons = array();

	public		function __construct(h\collection $configuration)
	{
		$this->_configuration = $configuration;
	}

	public		function get($key)
	{
		if(!isset($this->_configuration[$key]))
			$this->_throw_format('Unknown service \'%s\'', $key);

		if(!isset($this->_cons[$key]) || is_null($this->_cons[$key]))
		{
			$cfg = &$this->_configuration[$key];
			switch($cfg['type'])
			{
				default:
					$this->_throw_format('Unknown type \'%s\' for service \'%s\'', $cfg['type'], $key);
				case 'mysql':
				case 'maria':
					$con = h\db\open($cfg);
					break;
				case 'facebook':
				case 'fb':
					$con = new \facebook($cfg);
					break;
				case 'memcache':
				case 'memcached':
					$con = new memcache;
					$con->connect($cfg);
					break;
			}
			$this->_cons[$key] = $con;
		}

		return $this->_cons[$key];
	}

}

