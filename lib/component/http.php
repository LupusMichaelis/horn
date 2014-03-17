<?php
/** Delegate that take responsability for http request input (request) and
 *  output (responce)
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

namespace horn\lib\component;
use \horn\lib as h;

h\import('lib/component');
h\import('lib/http');
h\import('lib/uri');

class http
	extends base
{
	public		function do_touch(context $ctx)
	{
		@$ctx->in = null;
		@$ctx->out = null;
	}

	protected	function do_before(context $ctx)
	{
		// create http_request, http_responce
		$ctx->in = h\http\create_native();
		$ctx->out = new h\http\response;

		return true;
	}

	protected	function do_after(context $ctx)
	{
		$this->do_render($ctx);
	}

	private		function do_render(context $ctx)
	{
		$this->do_render_head($ctx);
		$this->do_render_body($ctx);
	}

	private		function do_render_head(context $ctx)
	{
		header($ctx->out->status);
		foreach($ctx->out->head as $name => $value)
			// TODO: escape name and values to avoid header injection
			header(sprintf('%s: %s', $name, $value instanceof h\object\base ? $value->_to_string() : (string) $value));
	}

	private		function do_render_body(context $ctx)
	{
		print $ctx->out->body->content;
	}
}
