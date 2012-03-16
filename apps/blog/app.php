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

	protected		function &_get_model()
	{
		$db = h\db\open($this->config['db']);
		$model = new story_source($db) ;
		return $model ;
	}

	public		function run()
	{
		$this->do_routing() ;
		$this->render() ;
		return $this ;
	}

	private		function do_routing()
	{
		$path = h\string($this->request->uri->path) ;
		$this->_template = null ;

		if($path->is_equal(h\string('/')))
		{
		}
		elseif($path->is_equal(h\string('/stories')))
		{
			$this->_template = 'list' ;
			$this->_resource = h\c(array('type' => 'stories')) ;
			$this->_resource['stories'] = $this->model->get_all() ;
		}
		elseif($path->is_equal(h\string('/stories/')))
			$this->redirect_to(h\string('/stories')) ;
		else
		{
			$re = new h\regex('^/stories/(.+)$') ;

			$this->_resource = h\c(array('type' => 'story')) ;

			if($re->match($path))
			{
				$title = $re->get_result(1) ;
				$title = h\string(urldecode($path->slice($title[0][0], $title[0][1]))) ;

				$this->_resource['title'] = $title ;
				$this->_resource['type'] = 'story' ;
				$this->_resource['stories'] = $this->model->get_by_title($title) ;

				$this->_template = 'entry' ;
			}
		}
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
			, 'application/rss+xml' => '\horn\lib\rss'
			) ;

		$doc = new $types[(string) $type] ;

		return $doc ;
	}

	public		function render()
	{
		$mime_type = static::desired_mime_type($this->request) ;
		$doc = $this->get_canvas_by_mime_type($mime_type) ;

		$doc->canvas->title = h\string('My new blog') ;
		$doc->register('story', '\horn\apps\story_html_renderer') ;

		if(is_null($this->_resource))
			$this->not_found() ;
		else
		{
			$doc->render($this->_resource) ;
			$this->response->body->content = $doc ;
			$this->response->header['Content-type'] = sprintf('%s;encoding=%s', $mime_type, 'utf-8') ;
		}
	}
}


