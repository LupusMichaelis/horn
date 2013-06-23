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

class http_cors
	extends base
{
	private		$http_verbs = array('POST', 'GET', 'DELETE', 'PUT');
	protected	function do_before(context $ctx)
	{
		$this->do_populate_head($ctx);
		return $this->do_answer_option($ctx);
	}

	private		function do_populate_head(context $ctx)
	{
		$cors_headers = h\c(array
			( 'Access-Control-Allow-Credentials' => 'true'
			, 'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE'
			, 'Access-Control-Allow-Headers' => 'Content-Type'
			, 'Access-Control-Allow-Origin' =>
				h\string::format('https://%s', $this->configuration['front_base_url'])
			)
		);
		$ctx->out->header->join($cors_headers);
	}

	private		function do_answer_option(context $ctx)
	{
		/// @see http://www.html5rocks.com/en/tutorials/cors/
		if('OPTIONS' === $ctx->in->method)
		{
			if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			{
				$requested_method = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'];
				if(!in_array($requested_method, $this->http_verbs))
				{
					$ctx->out->status = 'HTTP/1.1 405 Method Not Allowed';
					$msg = sprintf('Non-supported method \'%s\' on \'%s\'', $requested_method, $uri);
					$ctx->out->body->status = false;
					$ctx->out->body->messages[] = $msg;
					return false;
				}

				return true;
			}
		}
	}


	protected	function do_after(context $ctx)
	{
	}
}

