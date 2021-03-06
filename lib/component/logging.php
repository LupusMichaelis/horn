<?php
/** Delegate that take responsability for logging
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

namespace horn\lib\component;
use \horn\lib as h;

h\import('lib/component');

class logging
	extends base
{
	public		function do_touch(context $ctx)
	{
		@$ctx->logger = null;
	}

	protected	function do_before(context $ctx)
	{
		$ctx->logger = h\collection();
	}

	protected	function do_after(context $ctx)
	{
		if(0 === count($ctx->logger))
			return;

		$fp = fopen($this->configuration['log']['filename'], 'a');
		foreach($ctx->logger as $message)
			fputs($fp, $message);
		fclose($fp);
	}
}

