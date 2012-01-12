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

require_once 'horn/lib/collection.php' ;
require_once 'horn/lib/string.php' ;

require_once 'horn/lib/app.php' ;
require_once 'horn/lib/db/connect.php' ;

require_once 'horn/lib/time/date_time.php' ;
require_once 'horn/lib/string.php' ;

require_once 'horn/apps/blog/model.php' ;
require_once 'horn/apps/blog/view.php' ;

class blog
	extends h\app
{
	protected		function &_get_model()
	{
		$db = h\db\open($this->config['db']);
		$model = new blog_model($db) ;
		return $model ;
	}

	public		function run()
	{
		$this->prepare_renderer() ;
		return $this ;
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
			( 'text/html' => array('\horn\lib\html', '\horn\apps\render_post_html')
			, 'application/rss+xml' => array('\horn\lib\rss', '\horn\apps\render_post_rss')
			) ;

		$doc = new $types[$type][0] ;
		$doc->title = h\string('My new blog') ;
		$doc->register('post', $types[$type][1]) ;

		foreach($this->model->posts as $post)
			$doc->render('post', $post) ;

		$this->response->body->content = $doc ;
		//$this->response->set_content_type($type, 'utf-8') ;
	}
}


