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
	private			$_template ;
	private			$_resource ;
	private			$_model ;

	public		function __construct(h\http\request $in, h\http\response $out, $config)
	{
		$this->_resource = h\c(array('type' => null, 'model' => null)) ;
		$this->_template = h\c(array('display' => null, 'mode' => h\string('show'))) ;
		parent::__construct($in, $out, $config) ;
	}

	protected	function &_get_model()
	{
		if(is_null($this->_model))
		{
			$db = h\db\open($this->config['db']);
			$this->_model = new story_source($db) ;
		}

		return $this->_model ;
	}

	public		function run()
	{
		$this->do_control() ;
		$this->set_view() ;
		$this->render() ;
		return $this ;
	}

	private		function do_control()
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
		if($action->is_equal(h\string('add')))
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
		elseif($action->is_equal(h\string('edit')))
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
		elseif($action->is_equal(h\string('delete')))
		{
			$title = $this->request->body->get(h\string('story_key')) ;
			$story = $this->model->get_by_title(h\string($title)) ;
			$this->model->delete($story) ;
			$this->redirect_to('/stories') ;
			return true ;
		}

		return false ;
	}

	private		function set_view()
	{
		$path = h\string($this->request->uri->path) ;
		$base = h\string($this->config['base']) ;

		$dot = $path->search('.') ;
		$path = -1 < $dot ?  $path->head(--$dot) : $path ;

		if(0 !== $path->search($base))
			return false ;

		if($path->is_equal($base))
		{
			$this->_resource['type'] = '\horn\apps\blog\stories' ;
			$this->_resource['model'] = $this->model->get_all() ;

			$this->_template['display'] = 'itemise' ;
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

				$this->_resource['type'] = '\horn\apps\blog\story' ;
				$this->_resource['title'] = $title ;
				$this->_resource['model'] = $this->model->get_by_title($title) ;

				$this->_template['display'] = 'entry' ;
			}
			else
				return false ;
		}

		if($this->request->uri->searchpart->length())
		{
			$action = $this->request->uri->searchpart->tail(1) ;
			if(h\collection('delete', 'add', 'edit')->has_value($action))
				$this->_template['mode'] = $action ;
		}

		return true ;
	}

	static
	public		function desired_mime_type(h\http\request $in = null)
	{
		$types = array
			( 'html' => h\string('text/html')
			, 'rss' => h\string('application/rss+xml')
			) ;
		$suffix = 'html' ;
		if(!is_null($in))
		{
			$path = h\string($in->uri->path) ;
			$offset = $path->search('.') ;
			$offset > -1 and $suffix = $path->tail(++$offset) ;
		}
		return $types[(string) $suffix] ;
	}

	protected	function get_canvas_by_mime_type(h\string $type)
	{
		$types = array
			( 'text/html' => '\horn\lib\page_html'
			, 'application/rss+xml' => '\horn\lib\feed_rss'
			) ;

		$doc = new $types[(string) $type] ;

		return $doc ;
	}

	public		function render()
	{
		$mime_type = static::desired_mime_type($this->request) ;
		$doc = $this->get_canvas_by_mime_type($mime_type) ;

		$doc->canvas->title = h\string('My new blog') ;

		if(h\string('text/html')->is_equal($mime_type))
		{
			$doc->register('\horn\apps\blog\story', '\horn\apps\story_html_renderer') ;
			$doc->register('\horn\apps\blog\stories', '\horn\apps\story_html_renderer') ;
		}
		elseif(h\string('application/rss+xml')->is_equal($mime_type))
		{
			$doc->register('\horn\apps\blog\stories', '\horn\apps\story_rss_renderer') ;
		}

		if(is_null($this->_resource['model']))
			$this->not_found() ;
		else
		{
			$doc->render($this->_template, $this->_resource) ;
			$this->response->body->content = $doc ;
			$this->response->header['Content-type'] = sprintf('%s;encoding=%s', $mime_type, 'utf-8') ;
		}
	}
}


