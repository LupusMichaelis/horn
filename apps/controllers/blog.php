<?php
/** blog application controller helper
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

namespace horn\apps\blog ;
use \horn\lib as h ;

h\import('lib/collection') ;
h\import('lib/string') ;
h\import('lib/regex') ;

h\import('lib/app') ;
h\import('lib/db/connect') ;

h\import('lib/time/date_time') ;

h\import('apps/models/blog') ;
h\import('apps/views/blog') ;
h\import('apps/views/page_html') ;

class portal_controller
	extends h\controller
{
	protected	$_model ;

	public		function __construct(h\app $app, $config)
	{
		parent::__construct($app, $config) ;
	}

	protected	function &_get_model()
	{
		$s = $this->app->models->stories ;
		return $s ;
	}

	public		function do_control()
	{
		return true ;
	}

	public		function do_render()
	{
		$this->prepare_render() ;
	}

	protected	function prepare_render()
	{
		$doc = $this->app->response->body->content ;
		$doc->canvas->title = h\string('My new blog') ;
		$mimetype = $this->app->response->header['Content-type']->head(
			$this->app->response->header['Content-type']->search(';') - 1
			) ;

		if(h\string('text/html')->is_equal($mimetype))
		{
			$doc->register('\horn\apps\blog\story', '\horn\apps\blog\story_html_renderer') ;
			$doc->register('\horn\apps\blog\stories', '\horn\apps\blog\story_html_renderer') ;
			$doc->register('\horn\apps\blog\account', '\horn\apps\blog\account_html_renderer') ;
			$doc->register('\horn\apps\blog\accounts', '\horn\apps\blog\account_html_renderer') ;
		}
		else
			$this->_throw_format('Unknown mimetype \'%s\'', $mimetype) ;

	}

}

class legacy_controller
	extends h\controller
{
	protected	$_model ;

	public		function __construct(h\app $app, $config)
	{
		parent::__construct($app, $config) ;
	}

	protected	function &_get_model()
	{
		$s = $this->app->models->stories ;
		return $s ;
	}

	public		function do_control()
	{
		$path = h\string($this->app->request->uri->path) ;
		$legacy = $this->model->get_by_legacy_path($path) ;

		if(! $legacy instanceof story)
			return false ;

		$this->app->redirect_to($this->uri_to($legacy)) ;

		return true ;
	}

	// XXX Remove that
	protected	function uri_to($resource)
	{
		$base = h\concatenate($this->app->config['base'], $this->config['base']) ;
		return $base.'/'.\urlencode($resource->title) ;
	}

	public		function do_render()
	{

	}

}

class story_controller
	extends h\crud_controller
{
	protected	$_model ;

	public		function __construct(h\app $app, $config)
	{
		parent::__construct($app, $config) ;
	}

	protected	function &_get_model()
	{
		$s = $this->app->models->stories ;
		return $s ;
	}

	protected	function create_from_http()
	{
		$title = $this->app->request->body->get(h\string('story_title')) ;
		$story = $this->model->get_by_title(h\string($title)) ;

		if($story instanceof story)
			$this->_throw('Story already exists') ;

		$story = new story;
		$story->title = $this->app->request->body->get(h\string('story_title'));
		$story->description = $this->app->request->body->get(h\string('story_description'));

		return $story ;
	}

	protected	function update_from_http()
	{
		$title = $this->app->request->body->get(h\string('story_key')) ;
		$story = $this->model->get_by_title(h\string($title)) ;

		$copy = clone $story;
		$copy->title = $this->app->request->body->get(h\string('story_title'));
		$copy->description = $this->app->request->body->get(h\string('story_description'));
		$copy->modified = h\today();

		$story->assign($copy);

		return $story ;
	}

	protected	function delete_from_http()
	{
		$title = $this->app->request->body->get(h\string('story_key')) ;
		$story = $this->model->get_by_title(h\string($title)) ;

		return $story ;
	}

	protected	function get_one()
	{
		$this->resource['type'] = '\horn\apps\blog\story' ;
		return $this->model->get_by_title($this->resource['title']) ;
	}

	protected	function get_collection()
	{
		$this->resource['type'] = '\horn\apps\blog\stories' ;
		return $this->model->get_all() ;
	}

	protected	function uri_to($resource)
	{
		$base = h\concatenate($this->app->config['base'], $this->config['base']) ;
		return $base.'/'.\urlencode($resource->title) ;
	}

	protected	function prepare_render()
	{
		$doc = $this->app->response->body->content ;
		$doc->canvas->title = h\string('My new blog') ;
		$mimetype = $this->app->response->header['Content-type']->head(
			$this->app->response->header['Content-type']->search(';') - 1
			) ;

		if(h\string('text/html')->is_equal($mimetype))
		{
			$doc->register('\horn\apps\blog\story', '\horn\apps\blog\story_html_renderer') ;
			$doc->register('\horn\apps\blog\stories', '\horn\apps\blog\story_html_renderer') ;
		}
		elseif(h\string('application/rss+xml')->is_equal($mimetype))
		{
			$doc->register('\horn\apps\blog\stories', '\horn\apps\blog\story_rss_renderer') ;
		}
		else
			$this->_throw_format('Unknown mimetype \'%s\'', $mimetype) ;

	}

}
