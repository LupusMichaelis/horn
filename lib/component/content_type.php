<?php
/** Delegate that take responsability for http request input (request) and
 *  output (responce)
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
h\import('lib/text');

class content_type
	extends base
{
	public		function do_touch(context $ctx)
	{
		@$ctx->out = null;
	}

	protected	function do_before(context $ctx)
	{
		$this->do_deduce_content_type($ctx);
		$this->do_select_renderer($ctx);
		return true;
	}

	protected	function do_after(context $ctx)
	{
		$ctx->out->body->content = $ctx->renderer->do_render($ctx);
	}

	private		function do_deduce_content_type(context $ctx)
	{
		$ctx->out->head['Content-type'] = h\text::format
			( '%s; charset=%s'
			, $this->configuration['content_type']['mime_type']
			, $this->configuration['content_type']['encoding']
			);
	}

	private		function do_select_renderer(context $ctx)
	{
		$renderer = $this->configuration['content_type']['engine'];
		$ctx->renderer = new $renderer($this->configuration);
	}
}

