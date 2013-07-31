<?php
/** Mustache templating engine, tags
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

namespace horn\lib\mustache\tag;
use \horn\lib as h;

h\import('lib/object');

abstract
class base
	extends h\object_public
{
}

class begin
	extends base
{
}

class raw
	extends base
{
	public		$content;
}

class variable
	extends base
{
	public		$name;
}

class section
	extends base
{
	public		$name;
}

class inverted
	extends base
{
	public		$name;
}

class unescaped
	extends base
{
	public		$name;
}

class comment
	extends base
{
}

class partial
	extends base
{
	public		$name;
}

class set_delimiter
	extends base
{
	public		$delimiter;
}

class close
	extends base
{
	public		$name;
}

class end
	extends base
{
}

