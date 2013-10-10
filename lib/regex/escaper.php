<?php
/** 
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

namespace horn\lib\regex;
use \horn\lib as h;

h\import('lib/escaper');

class escaper
	extends		h\object\public_
	implements	h\escaper
{
	public function		__construct(h\string $charset)
	{
		parent::__construct();
	}

	public function		do_escape(h\string $subject)
	{
		$escaped = clone $subject;
		$escaped->scalar = preg_quote($escaped->scalar);

		return $escaped;
	}

	public function		do_unescape(h\string $subject)
	{
		throw $this->_exception('Unescape is not supported');
	}

}


