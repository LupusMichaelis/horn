<?php
/** database class definition
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2011, Lupus Michaelis
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
namespace horn\lib\sql;
use \horn\lib as h;

h\import('lib/object');
h\import('lib/collection');

h\import('lib/sql/select');

class forge
	extends h\object_public
{
	public		function __construct(\mysqli $dbcon)
	{
		parent::__construct();
		$this->_handler = $dbcon;

		if($this->_handler->connect_errno)
			throw $this->_exception($this->_handler->connect_error);
	}

	public		function select(/* $fields = array() */)
	{
		$fields = func_get_args();
		$fields = ! func_num_args()
			? h\collection()
			: h\collection($fields);
		return new select($fields);
	}


	public		function expression($content)
	{
		return new expression($content);
	}

	private		$_handler;
}

