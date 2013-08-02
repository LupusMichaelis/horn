<?php
/** Exception hierarchy for 4xx and 5xx HTTP errors
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
use \horn\lib as h;

h\import('lib/object');

class error
	extends h\exception
{
}

class client_error
	extends error
{
}

class server_error
	extends error
{
}

class bad_request
	extends client_error
{
	const code = 400;
	const message = 'Bad Request';
}

class unauthorized
	extends client_error
{
	const code = 401;
	const message = 'Unauthorized';
}

class payment_required
	extends client_error
{
	const code = 402;
	const message = 'Payment Required';
}

class forbidden
	extends client_error
{
	const code = 403;
	const message = 'Forbidden';
}

class not_found
	extends client_error
{
	const code = 404;
	const message = 'Not Found';
}

class method_not_allowed
	extends client_error
{
	const code = 405;
	const message = 'Method Not Allowed';
}

class not_acceptable
	extends client_error
{
	const code = 406;
	const message = 'Not Acceptable';
}

class proxy_authentification_required
	extends client_error
{
	const code = 407;
	const message = 'Proxy Authentification Required';
}

class request_timeout
	extends client_error
{
	const code = 408;
	const message = 'Request Timeout';
}

class conflict
	extends client_error
{
	const code = 409;
	const message = 'Conflict';
}

class gone
	extends client_error
{
	const code = 410;
	const message = 'Gone';
}

class length_required
	extends client_error
{
	const code = 411;
	const message = 'Length Required';
}

class precondition_failed
	extends client_error
{
	const code = 412;
	const message = 'Precondition Failed';
}

class request_entity_too_large
	extends client_error
{
	const code = 413;
	const message = 'Request Entity Too Large';
}

class request_uri_too_long
	extends client_error
{
	const code = 414;
	const message = 'Request Uri Too Long';
}

class unsupported_media_type
	extends client_error
{
	const code = 415;
	const message = 'Unsupported Media Type';
}

class requested_range_not_satisfiable
	extends client_error
{
	const code = 416;
	const message = 'Requested Range Not Satisfiable';
}

class expectation_failed
	extends client_error
{
	const code = 417;
	const message = 'Expectation Failed';
}

class i_am_a_teapot
	extends client_error
{
	const code = 418;
	const message = 'I\'Am A Teapot';
}


class internal_Server_error
	extends server_error
{
	const code = 500;
	const message = 'Internal Server Error';
}

class not_implemented
	extends server_error
{
	const code = 501;
	const message = 'Not Implemented';
}

class bad_gateway
	extends server_error
{
	const code = 502;
	const message = 'Bad Gateway';
}

class service_unavailable
	extends server_error
{
	const code = 503;
	const message = 'Service Unavailable';
}

class gateway_timeout
	extends server_error
{
	const code = 504;
	const message = 'Gateway Timeout';
}

class http_version_not_supported
	extends server_error
{
	const code = 505;
	const message = 'HTTP Version Not Supported';
}

class variant_also_negotiates
	extends server_error
{
	const code = 506;
	const message = 'Variant Also Negotiates';
}
