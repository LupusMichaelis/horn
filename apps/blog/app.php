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
		$this->prepare_renderer() ;
		return $this ;
	}

	private		function do_routing()
	{
		$path = h\string($this->request->uri->path) ;
		$this->_resource = null ;

		if($path->is_equal(h\string('/')))
		{
		}
		elseif($path->is_equal(h\string('/stories')))
		{
			$this->_resource = $this->model->get_all() ;
		}
		else
		{
			$re = new h\regex('^/stories/(.+)$') ;

			if($re->match($path))
			{
				$title = $re->get_result(1) ;
				$title = h\string(urldecode($path->slice($title[0][0], $title[0][1]))) ;
				$this->_resource = $this->model->get_by_title($title) ;
			}
		}
	}

	static
	public		function desired_mime_type(h\http\request $in = null)
	{
		$types = array
			( 'html' => 'text/html'
			, 'rss' => 'application/rss+xml'
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

	public		function prepare_renderer()
	{
		$type = static::desired_mime_type($this->request) ;
		$types = array
			( 'text/html' => array('\horn\lib\html', '\horn\apps\render_story_html')
			, 'application/rss+xml' => array('\horn\lib\rss', '\horn\apps\render_story_rss')
			) ;

		$doc = new $types[$type][0] ;
		$doc->title = h\string('My new blog') ;
		$doc->register('story', $types[$type][1]) ;

		if($this->_resource instanceof h\string)
			$doc->render('story', $this->_resource) ;
		elseif($this->_resource instanceof h\collection)
			foreach($this->_resource as $story)
				$doc->render('story', $story) ;
		else
			$this->not_found() ;

		$this->response->body->content = $doc ;
		$this->response->header['Content-type'] = sprintf('%s;encoding=%s', $type, 'utf-8') ;
	}
}


