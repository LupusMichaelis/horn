<?php
/**
 *	Object coherent handling.
 *	\see object_base
 *
 *	object_public, object_protected and object_private are defined becasue you can't
 *	change the
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

namespace horn\lib;
use horn\lib as h;

h\import('lib/exception');
h\import('lib/object/base');
h\import('lib/object/regular');
h\import('lib/object/public');
h\import('lib/object/protected');
h\import('lib/object/wrapper');

// XXX For compatibility sake

class object_public extends h\object\public_
{
	public		function __construct()
	{
		parent::__construct();
	}
}

class object_protected extends h\object\protected_
{
	protected		function __construct()
	{
		parent::__construct();
	}
}
