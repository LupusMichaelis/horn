<?php
/** Model holder
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

h\import('lib/object');
h\import('lib/services');

class model_data
	extends h\object_public
{
	public		function __construct(h\model $parent)
	{
		$this->_model = $parent;
		$this->_cache = h\collection();
		parent::__construct();
	}

	protected	$_name;

	protected	$_cache;
	protected	$_model;
}

class model
	extends h\object_public
{
	/// TODO readonly
	protected	$_data;

	public		function __construct(h\service_provider $services)
	{
		$this->_data = h\collection();
		$this->_services = $services;
		parent::__construct();
	}

	public		function get_data(h\string $data_name)
	{
		return $this->_data[$data_name];
	}

	protected	function add_data(model_data $data)
	{
		$this->_data[$data::name] = $data;
	}

	protected	$_services;
}

