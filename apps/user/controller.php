<?php
/** user management application controller helper
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

namespace horn\apps\user ;
use \horn\lib as h ;

h\import('lib/collection') ;
h\import('lib/string') ;
h\import('lib/regex') ;

h\import('lib/app') ;
h\import('lib/controller') ;

h\import('lib/time/date_time') ;
h\import('lib/string') ;

h\import('apps/user/model') ;
h\import('apps/user/view') ;

class controller
	extends h\crud_controller
{
	protected	$_model ;

	public		function __construct(h\app $app, $config)
	{
		parent::__construct($app, $config) ;
	}

	protected	function &_get_model()
	{
		$s = $this->app->models->users ;
		return $s ;
	}

	protected	function get_one()
	{
		$this->resource['type'] = '\horn\apps\user\account' ;
		$one = $this->model->get_by_name($this->resource['title']) ;
		return $one ;
	}

	protected	function get_collection()
	{
		$this->resource['type'] = '\horn\apps\user\accounts' ;
		return $this->model->get_all() ;
	}

	protected	function uri_to($resource)
	{
		$base = h\concatenate($this->app->config['base'], $this->config['base']) ;
		return $base.'/'.\urlencode($resource->name) ;
	}

	protected	function create_from_http()
	{
		$name = $this->app->request->body->get(h\string('account_name')) ;
		$account = $this->model->get_by_name(h\string($name)) ;

		if($account instanceof account)
			$this->_throw('Story already exists') ;

		$account = new account;
		$account->name = $this->app->request->body->get(h\string('account_name'));
		$account->email = $this->app->request->body->get(h\string('account_email'));
		$account->created = $account->modified = h\today();

		return $account ;
	}

	protected	function update_from_http()
	{
		$name = $this->app->request->body->get(h\string('account_key')) ;
		$account = $this->model->get_by_name(h\string($name)) ;

		$copy = clone $account;
		var_dump($copy);
		$copy->name = $this->app->request->body->get(h\string('account_name'));
		$copy->email = $this->app->request->body->get(h\string('account_email'));
		$copy->modified = h\today();
		$account->assign($copy) ;

		return $account ;
	}

	protected	function delete_from_http()
	{
		$name = $this->app->request->body->get(h\string('account_key')) ;
		$account = $this->model->get_by_name(h\string($name)) ;
		$this->model->delete($account) ;

		return $account ;
	}

	protected	function prepare_render()
	{
		$doc = $this->app->response->body->content ;
		$doc->canvas->title = h\string('My new account') ;
		$mimetype = $this->app->response->header['Content-type']->head(
			$this->app->response->header['Content-type']->search(';') - 1
			) ;

		if(h\string('text/html')->is_equal($mimetype))
		{
			$doc->register('\horn\apps\user\account', '\horn\apps\user\account_html_renderer') ;
			$doc->register('\horn\apps\user\accounts', '\horn\apps\user\account_html_renderer') ;
		}
		elseif(h\string('application/rss+xml')->is_equal($mimetype))
		{
			$doc->register('\horn\apps\user\accounts', '\horn\apps\user\account_rss_renderer') ;
		}
		else
			$this->_throw_format('Unknown mimetype \'%s\'', $mimetype) ;
	}
}


