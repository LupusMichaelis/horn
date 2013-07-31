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

namespace horn\lib;
use \horn\lib as h;

h\import('lib/object');
h\import('lib/collection');
h\import('lib/string');
h\import('lib/model');

////////////////////////////////////////////////////////////////////////////////
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

////////////////////////////////////////////////////////////////////////////////
abstract
class controller
	extends h\object_public
{
	protected	$_context;

	public		function __construct(h\component\context $context)
	{
		$this->_context = $context;
	}

	public		function get_model()
	{
		return $this->context->model;
	}

	public		function get_segments()
	{
		return $this->context->segments;
	}

	public		function get_search_part()
	{
		// XXX that should've belong in a searchpart object
		$searchpart = array();
		if(0 === $this->context->in->uri->searchpart->search('?'))
			parse_str($this->context->in->uri->searchpart->tail(1), $searchpart);

		return h\c($searchpart);
	}

	public		function get_post_data()
	{
		return $this->context->in->body->content;
	}

	public		function get_cookie_data()
	{
		return $this->context->in->head->cookies;
	}

	public		function get_put_data()
	{
		return $this->context->in->body->post;
	}

	public		function ok()
	{
		$this->status(200, 'OK');
	}

	public		function no_content()
	{
		$this->status(204, 'Not found');
	}

	public		function forbidden()
	{
		$this->status(403, 'Forbidden');
	}

	public		function not_found()
	{
		$this->status(404, 'Not found');
	}

	public		function http_conflict()
	{
		$this->status(409, 'Conflict');
	}

	public		function redirect_to_created($to)
	{
		$this->status(201, 'Created');
		$this->location($to);
	}

	public		function location($uri)
	{
		$this->context->out->head['Location'] = sprintf
			( '%s://%s%s'
			, 'http' // $this->context->in->scheme
			, $this->context->in->head['host']
			, $uri
			);
	}

	public		function redirect_to($to)
	{
		$this->status(301, 'Moved Permanently');
		$this->location($to);
	}

	public		function redirect_to_updated($to)
	{
		$this->ok();
		$this->location($to);
	}

	public		function status($code, $message)
	{
		$this->context->out->status = sprintf('%s %s %s'
				, $this->context->in->version, $code, $message);
	}
}

////////////////////////////////////////////////////////////////////////////////
abstract
class resource
	extends h\object_public
{
	protected	$_ctrl;

	public		function __construct(h\crud_controller $ctrl)
	{
		$this->_ctrl = $ctrl;
		parent::__construct();
	}

	abstract public function of_http_request_uri();
	abstract public function of_http_request_post_data();
	abstract public function create_from_http_request_post_data();
	abstract public function update_from_http_request_post_data($story);
	abstract public function delete($story);
	abstract public function uri_of($story);
}

abstract
class crud_controller
	extends controller
	implements http_get, http_post
{
	protected	$_resource;
	protected	$_action;

	public		function __construct(h\component\context $context, h\resource $resource)
	{
		$this->_resource = $resource;
		$this->_action = h\string('read');
		parent::__construct($context);

		foreach(array('edit', 'delete') as $action)
			if($this->get_search_part()->has_key($action))
			{
				$this->action = h\string($action);
				break;
			}
		$this->context->template_action = $this->action;
	}

	public		function do_get()
	{
		return $this->do_read();
	}

	public		function do_post()
	{
		if($this->create_verb->is_equal($this->action))
			return $this->do_create();
		elseif($this->update_verb->is_equal($this->action))
			return $this->do_update();
		elseif($this->delete_verb->is_equal($this->action))
			return $this->do_delete();
	}

	// XXX This should be configured or set in context
	protected		function &_get_create_verb()
	{
		$s = h\string('add');
		return $s;
	}

	protected		function &_get_read_verb()
	{
		$s = h\string('read');
		return $s;
	}

	protected		function &_get_update_verb()
	{
		$s = h\string('edit');
		return $s;
	}

	protected		function &_get_delete_verb()
	{
		$s = h\string('delete');
		return $s;
	}

	public		function do_create()
	{
		$name = $this->resource->name;
		$class = $this->resource->class;
		$$name = $this->resource->of_http_request_post_data();

		if($$name instanceof $class)
		{
			$this->http_conflict();
			return array(false, null, array($this->resource->conflict));
		}

		$this->action = $this->read_verb;

		$$name = $this->resource->create_from_http_request_post_data();
		return array(true, compact($name));
	}

	public		function do_read()
	{
		$name = $this->resource->name;
		$class = $this->resource->class;
		$$name = $this->resource->of_http_request_uri();

		if(! $$name instanceof $class)
		{
			$this->not_found();
			return array(false, null, array($this->resource->not_found));
		}

		return array(true, compact($name));
	}

	public		function do_update()
	{
		$name = $this->resource->name;
		$class = $this->resource->class;
		$$name = $this->resource->of_http_request_uri();

		$copy = clone $$name;
		$this->resource->update_from_http_request_post_data($copy);
		$$name->assign($copy);

		$uri = $this->resource->uri_of($$name);
		$this->redirect_to($uri);

		$this->action = $this->read_verb;

		return array(true, compact($name));
	}

	public		function do_delete()
	{
		$name = $this->resource->name;
		$class = $this->resource->class;
		$$name = $this->resource->of_http_request_uri();

		$this->resource->delete($$name);

		$uri = $this->resource->uri_of_parent();
		$this->redirect_to($uri);

		$this->action = $this->read_verb;

		return array(true);
	}
}
