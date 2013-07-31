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

class routing
	extends base
{
	protected		function do_before(context $ctx)
	{
		return $this->do_routing($ctx);
	}

	protected		function do_after(context $ctx)
	{
	}

	private			function do_routing(context $ctx)
	{
		$path = $ctx->in->uri->path;

		$routes = $this->configuration['routes'];
		$ctrl = null;
		$matches = array();
		$segments = \horn\lib\collection();
		foreach($routes as $route_pattern => $route_ctrl)
		{
			$pattern = sprintf('@^%s$@', strtr($route_pattern, '@', '\@'));
			if(1 === preg_match($pattern, $path, $matches))
			{
				$ctrl = $route_ctrl;
				$route = $route_pattern;
				foreach($matches as $key => $value)
					if(is_string($key))
						$segments[$key] = $value;
				break;
			}
		}
		$ctx->segments = $segments;

		$http_method = $ctx->in->method;
		if(is_null($ctrl))
		{
			$ctx->out->status = 'HTTP/1.1 404 Not Found';
			$msg = sprintf('Non-supported method \'%s\'', $http_method, $path);
			$ctx->error_handling['status'] = false;
			$ctx->error_handling['messages'][] = $msg;
			return false;
		}

		$ctx->route = $route;
		return true;
	}
}

