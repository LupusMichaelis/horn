<?php
/** An object to provide configuration facilities
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

namespace horn\lib ;
use \horn\lib as h;

h\import('lib/collection') ;
h\import('lib/decorator') ;

function make_configuration(/* ... */)
{
	$files = func_get_args();
	$files = h\c($files);

	$composed_configuration = null;
	foreach($files as $file)
	{
		$file_configuration = /*require*/ $file;
		$file_configuration = h\c($file_configuration);

		$composed_configuration = new composed_configuration($file_configuration, $composed_configuration);
	}

	$configuration = new configuration($composed_configuration);

	return $configuration;
}

class configuration
	extends collection
{
	protected	$_composed;

	public		function __construct(composed_configuration $composed)
	{
		$this->_composed = $composed;

		$values = clone $composed->payload;
		while($composed->has_next())
		{
			$composed = $composed->next;
			$values->join($composed->payload) ;
		}
		parent::__construct();
		$this->join($values);
	}

	protected	function _clone()
	{
		return new static($this->_composed);
	}
}

class composed_configuration
	extends decorator
{
	protected	$_payload;

	public		function __construct(h\collection $payload, h\composed_configuration $next=null)
	{
		$this->_payload = clone $payload;
		parent::__construct($next);
	}

	public		function get($key)
	{
		if(isset($this->_payload[$key]))
			return $this->_payload[$key];

		return $this->next->get($key);
	}
}



