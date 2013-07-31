<?php
/**
 *	HTTP request handling
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

namespace horn\lib\http;
use horn\lib as h;

h\import('lib/object');
h\import('lib/string');
h\import('lib/exception');
h\import('lib/http/message');

const		POST = 'POST';
const		GET = 'GET';
const		PUT = 'PUT';
const		DELETE = 'DELETE';
const		OPTIONS = 'OPTIONS';

function create_native()
{
	$native = new request;
	$native->head['host'] = h\string($_SERVER['HTTP_HOST']);
	$native->head['cookie'] = h\collection::merge($_COOKIE);

	$native->method = validate_http_method($_SERVER['REQUEST_METHOD']);
	$native->uri = new uri(h\string($_SERVER['REQUEST_URI']));
	$native->version = h\string($_SERVER['SERVER_PROTOCOL']);

	$native->body = new body;

	if(PUT === $native->method)
	{
		$put_data = file_get_contents('php://input');
		$put_data = urldecode($put_data);
		parse_str($put_data, $put_data);
		$put_data = h\collection::merge($put_data);
		$native->body->content = $put_data;
	}
	elseif(POST === $native->method)
		$native->body->content = h\collection::merge($_POST, $_FILES);

	return $native;
}

function validate_http_method($candidate)
{
	static $methods = array
		( 'POST' => POST
		, 'GET' => GET
		, 'PUT' => PUT
		, 'DELETE' => DELETE
		, 'OPTIONS' => OPTIONS
		);
	if(isset($methods[strtoupper($candidate)]))
		return $methods[strtoupper($candidate)];

	throw new h\exception(h\string::format('Method verb \'%s\' is not supported', $candidate));
}

