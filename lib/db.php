<?php
/** Provide generic facility to connect a data source
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

namespace horn\lib\db;
use horn\lib as h;

h\import('lib/db/connect');
h\import('lib/inet/url');

class url_db
	extends h\inet\url
{
	protected	$_space;
	protected	$_table;

    public   function is_scheme_supported(h\text $candidate)
    {
        return \string('mysql')->is_equal($candidate);
    }

	protected	function parse()
	{
		parent::parse();

        // XXX ???
		$this->space = new h\text($this->path);
		$this->space = $this->space->tail(1);
	}
}
