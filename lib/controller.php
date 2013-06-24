<?php
/** blog application controller
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2011, Lupus Michaelis
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

namespace horn\lib ;
use \horn\lib as h ;

h\import('lib/collection') ;
h\import('lib/string') ;
h\import('lib/model') ;

interface http_get
{
	function do_get();
}

interface http_post
{
	function do_post();
}

interface http_put
{
	function do_put();
}

interface http_delete
{
	function do_delete();
}

class controller
{
	private		$_model;
	private		$_segments;

	public		function __construct(h\collection $segments, h\model $model)
	{
		$this->_segments = $segments;
		$this->_model = $model;
	}

	protected	function get_model()
	{
		return $this->_model;
	}

	protected	function get_segments()
	{
		return $this->_segments;
	}

	protected	function get_search_part()
	{
		static $search_part;
		if(is_null($search_part))
			$search_part = h\collection::merge($_GET);

		return $search_part;
	}

	protected	function get_post_data()
	{
		static $post_data;
		if(is_null($post_data))
			$post_data = h\collection::merge($_POST);

		return $post_data;
	}

	protected	function get_cookie_data()
	{
		static $cookie;
		if(is_null($cookie))
			$cookie = h\collection::merge($_COOKIE);

		return $cookie;
	}

	protected	function get_put_data()
	{
		static $put_data;
		if(is_null($put_data))
		{
			$put_data = file_get_contents('php://input');
			$put_data = urldecode($put_data);
			parse_str($put_data, $put_data);
			$put_data = h\collection::merge($put_data);
		}

		return $put_data;
	}
}
