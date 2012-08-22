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
h\import('lib/regex') ;

h\import('lib/time/date_time') ;
h\import('lib/string') ;

abstract
class controller
	extends h\object_public
{
	abstract
	public		function do_control() ;
}

abstract
class crud_controller
	extends controller
{
	protected	$_app ;
	protected	$_config ;
	protected	$_resource ;
	protected	$_template ;

	public		function __construct(h\app $app, $config)
	{
		$this->_app = $app ;
		$this->_config = $config ;
		$this->_resource = h\c(array('type' => null, 'model' => null)) ;
		$this->_template = h\c(array('display' => h\string('entry'), 'mode' => h\string('show'))) ;

		parent::__construct() ;
	}

	abstract
	protected	function &_get_model() ;

	abstract
	protected	function get_one() ;
	abstract
	protected	function get_collection() ;

	abstract
	protected	function create_from_http() ;
	abstract
	protected	function update_from_http() ;
	abstract
	protected	function delete_from_http() ;

	public		function do_control()
	{
		if(h\http\request::POST === $this->app->request->method)
		{
			if($this->app->request->uri->searchpart->length())
			{
				$action = $this->app->request->uri->searchpart->tail(1) ;
				if(h\collection('add', 'delete', 'edit')->has_value($action))
				{
					$this->do_action($action) ;
					return true ;
				}
			}
		}
		return false ;
	}

	private		function do_action($action)
	{
		$base = h\concatenate($this->app->config['base'], $this->config['base']) ;

		if(h\string('add')->is_equal($action))
		{
			$story = $this->create_from_http() ;
			$this->model->insert($story) ;
			$this->app->redirect_to_created($base.'/'.\urlencode($story->title)) ;
		}
		elseif(h\string('edit')->is_equal($action))
		{
			$story = $this->update_from_http() ;
			$this->model->update($story) ;
			$this->app->redirect_to($base.'/'.\urlencode($story->title)) ;
		}
		elseif(h\string('delete')->is_equal($action))
		{
			$story = $this->delete_from_http() ;
			$this->model->delete($story) ;
			$this->app->redirect_to($base) ;
		}
	}

	public		function set_model()
	{
		if(h\string('itemise')->is_equal($this->template['display']))
			$this->resource['model'] = $this->get_collection() ;
		elseif(h\string('entry')->is_equal($this->template['display']))
			$this->resource['model'] = $this->get_one() ;
	}

	public		function do_routing()
	{
		$path = h\string($this->app->request->uri->path) ;
		$base = h\concatenate($this->app->config['base'], $this->config['base']) ;

		// Remove file suffix to have the actual path to the resource
		$dot = $path->search('.') ;
		$path = -1 < $dot ?  $path->head(--$dot) : $path ;

		if(0 !== $path->search($base))
			return false ;

		if($path->is_equal($base))
			$this->template['display'] = h\string('itemise') ;
		elseif($path->is_equal(h\concatenate($base, '/')))
			$this->app->redirect_to($base) ;
		else
		{
			$re = new h\regex('^'.$base.'/(.+)$') ;

			if($re->match($path))
			{
				$title = $re->get_result(1) ;
				$title = h\string(urldecode($path->slice($title[0][0], $title[0][1]))) ;
				$this->resource['title'] = $title ;
				$this->template['display'] = h\string('entry') ;
			}
			else
				return false ;
		}

		if($this->app->request->uri->searchpart->length())
		{
			$action = $this->app->request->uri->searchpart->tail(1) ;
			if(h\collection('delete', 'add', 'edit')->has_value($action))
				$this->template['mode'] = $action ;
		}

		return true ;
	}

	abstract
	protected	function prepare_render() ;

	public		function do_render()
	{
		$this->prepare_render() ;

		// XXX need an actual state that means the model rendering must not be done
		if(!is_null($this->_resource['model']))
			$this->app->response->body->content->render($this->template, $this->resource) ;
		else
			$this->app->not_found() ;
	}
}



