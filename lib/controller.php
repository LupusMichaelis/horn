<?php
/** blog application controller
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

namespace horn\lib;
use \horn\lib as h;

h\import('lib/object');
h\import('lib/collection');
h\import('lib/string');
h\import('lib/model');
h\import('lib/http/error');
h\import('lib/http/url');

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

	public		function get_post_data()
	{
		return $this->context->in->body->content;
	}

	public		function get_cookie_data()
	{
		return $this->context->in->head['cookie'];
	}

	public		function get_put_data()
	{
		return $this->context->in->body->content;
	}

	private		function location($uri)
	{
		$to = new h\http\url(h\string::format
				( '%s://%s%s'
				, 'http'//$this->context->in->scheme
				, 'blog.localhost'//$this->context->in->host
				, $uri
				));
		h\http\response_methods::location($this->context->out, $to);
	}

	public		function redirect_to_created($to)
	{
		h\http\response_methods::status($this->context->out, 201, 'Created');
		$this->location($to);
	}

	public		function redirect_to($to)
	{
		h\http\response_methods::status($this->context->out, 301, 'Moved Permanently');
		$this->location($to);
	}

	public		function redirect_to_updated($to)
	{
		h\http\response_methods::ok($this->context->out);
		$this->location($to);
	}

}

////////////////////////////////////////////////////////////////////////////////
abstract
class resource
	extends h\object_public
{
	protected	$_ctrl;

	protected	$_class;
	protected	$_name;

	public		function __construct(h\crud_controller $ctrl
			, h\string $name, h\string $class)
	{
		$this->_ctrl = $ctrl;
		$this->_class = $class;
		$this->_name = $name;
		parent::__construct();
	}

	public		function delete_from_http_request_uri()
	{
		$thing = $this->made_of_http_request_uri();
		$this->delete($thing);
		return $thing;
	}

	public		function is_managed($managed)
	{
		$class = (string) $this->class;
		return $managed instanceof $class;
	}

	public		function create_bare()
	{
		$class = (string) $this->class;
		$bare = new $class;
		return $bare;
	}

	abstract public function made_of_http_request_uri();
	abstract public function made_of_http_request_post_data();
	abstract public function create_from_http_request_post_data();
	abstract public function update_from_http_request_post_data($one);

	abstract public function uri_of($managed);
	abstract public function uri_of_parent();

	protected	function get_resource_model()
	{
		// XXX This shouldn't be
		$mapping = array('stories' => h\string('story'), 'accounts' => h\string('account'));
		$name = (string) $this->_name;
		if(isset($mapping[$name]))
			$name = $mapping[$name];
		else
			$name = h\string($name);
		// /XXX

		return $this->ctrl->get_model()->get_data($name);
	}
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

		foreach(array('edit', 'delete', 'add') as $action)
			if($context->in->uri->search->has_key($action))
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

		throw $this->_exception_format('Unknown action \'%s\'', $this->action);
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
		$name = (string)$this->resource->name;
		$$name = $this->resource->made_of_http_request_post_data();

		if($$name instanceof $this->resource->class)
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
		$name = (string)$this->resource->name;

		if($this->create_verb->is_equal($this->action))
			$$name = $this->resource->create_bare(1);
		else
			$$name = $this->resource->made_of_http_request_uri();

		return array(true, compact($name));
	}

	public		function do_update()
	{
		$name = (string)$this->resource->name;
		$$name = $this->resource->made_of_http_request_uri();

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
		$name = (string)$this->resource->name;
		$$name = $this->resource->made_of_http_request_uri();

		$this->resource->delete_from_http_request_uri();

		$uri = $this->resource->uri_of_parent();
		$this->redirect_to($uri);

		$this->action = $this->read_verb;

		return array(true);
	}
}
