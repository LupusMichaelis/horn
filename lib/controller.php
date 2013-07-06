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

h\import('lib/object') ;
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
	extends h\object_public
{
	protected	$_context;

	public		function __construct(h\component\context $context)
	{
		$this->_context = $context;
	}

	protected	function get_model()
	{
		return $this->context->model;
	}

	protected	function get_segments()
	{
		return $this->context->segments;
	}

	protected	function get_search_part()
	{
		return $this->context->in->uri->searchpart;
	}

	protected	function get_post_data()
	{
		return $this->context->in->body->iterate();
	}

	protected	function get_cookie_data()
	{
		return $this->context->in->head->cookies;
	}

	protected	function get_put_data()
	{
		return $this->context->in->body->post;
	}
}
