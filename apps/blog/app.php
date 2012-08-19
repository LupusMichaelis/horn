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

namespace horn\apps ;
use \horn\lib as h ;

h\import('lib/collection') ;
h\import('lib/string') ;
h\import('lib/regex') ;

h\import('lib/app') ;
h\import('lib/db/connect') ;

h\import('lib/time/date_time') ;
h\import('lib/string') ;

h\import('apps/blog/model') ;
h\import('apps/blog/view') ;

class blog
	extends h\app
{
	private			$_model ;

	protected	function &_get_model()
	{
		if(is_null($this->_model))
			$this->_model = new story_source($this->db) ;

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
			$title = $this->request->body->get(h\string('story_title')) ;
			$story = $this->model->get_by_title(h\string($title)) ;

			if($story instanceof story)
				$this->_throw('Story already exists') ;

			$story = story::create
					( $this->request->body->get(h\string('story_title'))
					, $this->request->body->get(h\string('story_description'))
					, $this->request->body->get(h\string('story_created'))
					, $this->request->body->get(h\string('story_modified'))
					) ;

			$this->model->insert($story) ;
			$this->redirect_to_created('/stories/'.\urlencode($story->title)) ;
			return true ;
		}
		elseif(h\string('edit')->is_equal($action))
		{
			$title = $this->request->body->get(h\string('story_key')) ;
			$story = $this->model->get_by_title(h\string($title)) ;

			$story->assign(story::create
					( $this->request->body->get(h\string('story_title'))
					, $this->request->body->get(h\string('story_description'))
					, $this->request->body->get(h\string('story_created'))
					, $this->request->body->get(h\string('story_modified'))
					)
				) ;

			$this->model->update($story) ;
			$this->redirect_to('/stories/'.\urlencode($story->title)) ;
			return true ;
		}
		elseif(h\string('delete')->is_equal($action))
		{
			$title = $this->request->body->get(h\string('story_key')) ;
			$story = $this->model->get_by_title(h\string($title)) ;
			$this->model->delete($story) ;
			$this->redirect_to('/stories') ;
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
			$this->resource['type'] = '\horn\apps\blog\stories' ;
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
				$title = $re->get_result(1) ;
				$title = h\string(urldecode($path->slice($title[0][0], $title[0][1]))) ;

				$this->resource['type'] = '\horn\apps\blog\story' ;
				$this->resource['title'] = $title ;
				$this->resource['model'] = $this->model->get_by_title($title) ;

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
		$doc->canvas->title = h\string('My new blog') ;
		$mimetype = $this->response->header['Content-type']->head(
			$this->response->header['Content-type']->search(';') - 1
			) ;

		if(h\string('text/html')->is_equal($mimetype))
		{
			$doc->register('\horn\apps\blog\story', '\horn\apps\story_html_renderer') ;
			$doc->register('\horn\apps\blog\stories', '\horn\apps\story_html_renderer') ;
		}
		elseif(h\string('application/rss+xml')->is_equal($mimetype))
		{
			$doc->register('\horn\apps\blog\stories', '\horn\apps\story_rss_renderer') ;
		}
		else
			$this->_throw_format('Unknown mimetype \'%s\'', $mimetype) ;

	}
}


