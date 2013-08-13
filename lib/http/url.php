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
namespace horn\lib\http;
use horn\lib as h;

h\import('lib/inet/url');

/**
 *	\code
 *	(https|http)://<user>:<pass>@<host>:<port>/<path>?<searchpart>
 *	\endcode
 */
class url
	extends		h\inet\url
{
	const		default_port = 80;
	const		default_secured_port = 443;

	public		function __construct()
	{
		parent::__construct();
		$this->port = static::default_port;
	}

	public		function is_scheme_supported(h\string $scheme)
	{
		return in_array($scheme->to_lower(), array('http', 'https'));
	}
}

class uri_factory
	extends h\uri\specific_factory
{
	public			$secured = false;

	public function	do_feed(h\string $meat)
	{
		$uri = new url;
		$uri->scheme = h\string($this->secured ? 'https' : 'http');
		$uri->hierarchical_part = $this->master->factories['hierarchical_part']->do_feed($meat);
		// XXX check authority is present and throw an error if not
		$uri->query = $this->master->factories['query']->do_feed($meat);
		$uri->fragment = $this->master->factories['fragment']->do_feed($meat);

		return $uri;
	}
}
