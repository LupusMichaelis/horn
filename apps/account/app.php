<?php
/** account application controller helper
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

namespace horn\apps ;
use \horn\lib as h ;

h\import('lib/collection') ;
h\import('lib/string') ;
h\import('lib/regex') ;

h\import('lib/app') ;
h\import('lib/db/connect') ;

h\import('lib/time/date_time') ;
h\import('lib/string') ;

h\import('apps/account/model') ;
h\import('apps/account/view') ;

class account_controller
	extends h\app
{
	private			$_model ;

	protected	function &_get_model()
	{
		if(is_null($this->_model))
			$this->_model = new account_source($this->db) ;

		return $this->_model ;
	}

	protected	function do_control()
	{
		if(h\http\request::POST === $this->request->method)
		{
			if($this->request->uri->searchpart->length())
			{
				$action = $this->request->uri->searchpart->tail(1) ;
				if(h\collection('add', 'delete', 'edit')->has_value($action))
					return $this->do_action($action) ;
			}
		}
		return false ;
	}

	private		function do_action($action)
	{
		if(h\string('add')->is_equal($action))
		{
			$name = $this->request->body->get(h\string('account_name')) ;
			$account = $this->model->get_by_name(h\string($name)) ;

			if($account instanceof account)
				$this->_throw('Story already exists') ;

			$account = account::create
					( $this->request->body->get(h\string('account_name'))
					, $this->request->body->get(h\string('account_email'))
					, $this->request->body->get(h\string('account_created'))
					, $this->request->body->get(h\string('account_modified'))
					) ;

			$this->model->insert($account) ;
			$this->redirect_to_created('/accounts/'.\urlencode($account->name)) ;
			return true ;
		}
		elseif(h\string('edit')->is_equal($action))
		{
			$name = $this->request->body->get(h\string('account_key')) ;
			$account = $this->model->get_by_name(h\string($name)) ;

			$account->assign(account::create
					( $this->request->body->get(h\string('account_name'))
					, $this->request->body->get(h\string('account_email'))
					, $this->request->body->get(h\string('account_created'))
					, $this->request->body->get(h\string('account_modified'))
					)
				) ;

			$this->model->update($account) ;
			$this->redirect_to('/accounts/'.\urlencode($account->name)) ;
			return true ;
		}
		elseif(h\string('delete')->is_equal($action))
		{
			$name = $this->request->body->get(h\string('account_key')) ;
			$account = $this->model->get_by_name(h\string($name)) ;
			$this->model->delete($account) ;
			$this->redirect_to('/accounts') ;
			return true ;
		}

		return false ;
	}

	protected		function set_view()
	{
		parent::set_view() ;
		$this->prepare_render() ;

		$path = h\string($this->request->uri->path) ;
		$base = h\string($this->config['base']) ;

		$dot = $path->search('.') ;
		$path = -1 < $dot ?  $path->head(--$dot) : $path ;

		if(0 !== $path->search($base))
			return false ;

		if($path->is_equal($base))
		{
			$this->resource['type'] = '\horn\apps\account\accounts' ;
			$this->resource['model'] = $this->model->get_all() ;

			$this->template['display'] = 'itemise' ;
		}
		elseif($path->is_equal(h\concatenate($base, '/')))
		{
			$this->redirect_to($base) ;
		}
		else
		{
			$re = new h\regex('^'.$base.'/(.+)$') ;

			if($re->match($path))
			{
				$name = $re->get_result(1) ;
				$name = h\string(urldecode($path->slice($name[0][0], $name[0][1]))) ;

				$this->resource['type'] = '\horn\apps\account\account' ;
				$this->resource['name'] = $name ;
				$this->resource['model'] = $this->model->get_by_name($name) ;

				$this->template['display'] = 'entry' ;
			}
			else
				return false ;
		}

		if($this->request->uri->searchpart->length())
		{
			$action = $this->request->uri->searchpart->tail(1) ;
			if(h\collection('delete', 'add', 'edit')->has_value($action))
				$this->template['mode'] = $action ;
		}

		return true ;
	}

	private	function prepare_render()
	{
		$doc = $this->response->body->content ;
		$doc->canvas->title = h\string('My new account') ;
		$mimetype = $this->response->header['Content-type']->head(
			$this->response->header['Content-type']->search(';') - 1
			) ;

		if(h\string('text/html')->is_equal($mimetype))
		{
			$doc->register('\horn\apps\account\account', '\horn\apps\account_html_renderer') ;
			$doc->register('\horn\apps\account\accounts', '\horn\apps\account_html_renderer') ;
		}
		elseif(h\string('application/rss+xml')->is_equal($mimetype))
		{
			$doc->register('\horn\apps\account\accounts', '\horn\apps\account_rss_renderer') ;
		}
		else
			$this->_throw_format('Unknown mimetype \'%s\'', $mimetype) ;

	}
}


