<?php 
/** 
 *
 *  \project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  \copyright	2009, Lupus Michaelis
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

namespace horn\lib\inet;
use \horn\lib as h;

import('lib/http/url');
import('lib/inet/host');
import('lib/string');
import('lib/collection');
import('lib/regex');
import('lib/regex-defs');

/** \brief		HTTP handling
  *
  * \bug		HTTPS not handle
  */
class webproxy
	extends		object_protected
{
	protected	$_url;
	protected	$_socket;

	/** \brief
	 *	\todo		Test what kind of URL is privide
	 */
	static public function from_url(h\http\url $url)
	{
		$new = new self;
		$new->url = $url;
		$new->socket = curl_init($url);
		return $new;
	}

	public		function _clone()
	{
		$new = new self;
		$new->url = clone $this->url;
		$new->socket = $this->copy_socket();

		return $new;
	}

	protected	function _set_socket($socket)
	{
		$this->_socket = is_resource($socket)
			? curl_copy_handle($socket)
			: null;
	}

	public		function close()
	{
		if(is_resource($this->socket))
			curl_close($this->socket);
	}

	public		function __destruct()
	{
		$this->close();
	}

	public			function refresh()
	{
		curl_close($this->socket);
		$this->_socket = curl_init($this->url);
	}

	public		function open()
	{
		curl_exec($con);
	}

	public		function ping()
	{
		curl_setopt($this->socket, CURLOPT_NOBODY, true);
		curl_setopt($this->socket, CURLOPT_FOLLOWLOCATION, false);
		curl_exec($this->socket);
		if(curl_errno($this->socket))
			return curl_error($this->socket);
		else
			return curl_getinfo($this->socket, CURLINFO_HTTP_CODE);
	}
}
