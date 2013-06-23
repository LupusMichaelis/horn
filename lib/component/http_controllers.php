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

namespace horn\lib\component ;
use \horn\lib as h;

h\import('lib/component') ;

class http_controllers
	extends base
{
	protected		function do_before(context $ctx)
	{
		$interfaces = array
			( 'GET' => 'http_get'
			, 'POST' => 'http_post'
			, 'PUT' => 'http_put'
			, 'DELETE' => 'http_delete'
			);

		$http_method = $ctx->in->method;

		if(!isset($interfaces[$http_method]))
		{
			$ctx->out->status = 'HTTP/1.1 405 Method Not Allowed';
			$msg = sprintf('Non-supported method \'%s\' on \'%s\'', $http_method, $uri);
			$ctx->out->body['status'] = false;
			$ctx->out->body['messages'][] = $msg;
			return false;
		}

		$controller_class = $this->configuration['routes'][$ctx->route];

		if(!class_exists($controller_class))
		{
			$msg = sprintf('Undefined controller class \'%s\'', $controller_class);
			$ctx->out->body['status'] = false;
			$ctx->out->body['messages'][] = $msg;
			return false;
		}

		$ctrl = new $controller_class($ctx->segments, $ctx->model);
		if(! $ctrl instanceof $interfaces[$http_method])
		{
			$ctx->out->status = 'HTTP/1.1 405 Method Not Allowed';
			$msg = sprintf('Non-supported method \'%s\' on \'%s\'', $http_method, $uri);
			$ctx->out->body['status'] = false;
			$ctx->out->body['messages'][] = $msg;
			return false;
		}

		$result = $ctrl->{"do_$http_method"}();
		$ctx->out->body['status'] = $result[0];
		isset($result[1]) && $ctx->out->body['results']->join($result[1]);
		isset($result[2]) && $ctx->out->body['messages']->join($result[2]);

		return true;
	}

	protected		function do_after(context $ctx)
	{
	}
}
