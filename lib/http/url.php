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
	public		function __construct()
	{
		parent::__construct();
		$this->port = 80;
	}

	static
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

		$slash = $meat->search('//');
		if(0 !== $slash)
			throw $this->_exception('Malformed HTTP URI: scheme specific part doesn\'t begin by \'//\'');

		$meat->behead($slash + 2);

		// XXX Must modify assignment in h\wrapper
		$host = $this->master->factories['host']->do_feed($meat);
		$uri->host->set_impl($host->get_impl());

		if($meat->length() && h\string(':')->is_equal($meat[0]))
		{
			$meat->behead(1); // Drop semicolon
			$uri->port = $this->master->factories['port']->do_feed($meat);
		}
		else
			$uri->port = $this->secured ? 443 : 80;

		if($meat->length() && h\string('/')->is_equal($meat[0]))
			$uri->path = $this->master->factories['absolute_path']->do_feed($meat);
		else
			throw $this->_exception('Malformed HTTP URI: no absolute path');

		if($meat->length() && h\string('?')->is_equal($meat[0]))
			$uri->search_part = $this->master->factories['search_part']->do_feed($meat);

		return $uri;
	}
}
