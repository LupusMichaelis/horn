<?php
/** blog application story controller helper
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

class story_controller
	extends h\crub_controller
{
	protected	function &_get_model()
	{
		$s = $this->app->models->stories;
		return $s;
	}

	public		function do_create()
	{
		$post = $this->get_post_data();

		$title = $post->get(h\string('story_title'));
		$story = $this->model->get_by_title(h\string($title));

		if($story instanceof story)
		{
			header('HTTP/1.1 409 Conflict');
			return array(false, null, array('Story already exists'))
		}

		$story = new story;
		$story->title = $post->get(h\string('story_title'));
		$story->description = $post->get(h\string('story_description'));

		return array(true, compact('story'));
	}

	public		function do_read()
	{
		$this->resource['type'] = '\horn\apps\blog\story';
		$story = $this->model->get_by_title($this->resource['title']);
		return array(true, compact('story'));

		/* Read a collection
		$this->resource['type'] = '\horn\apps\blog\stories';
		$stories = $this->model->get_all();
		return array(true, compact('stories'));
		*/
	}

	public		function do_update()
	{
		$post = $this->get_post_data();
		$title = $post->get(h\string('story_key'));
		$story = $this->model->get_by_title(h\string($title));

		$copy = clone $story;
		$copy->title = $post->get(h\string('story_title'));
		$copy->description = $post->get(h\string('story_description'));
		$copy->modified = h\today();

		$story->assign($copy);

		return array(true, compact('story'));
	}

	public		function do_delete()
	{
		$post = $this->get_post_data();
		$title = $post->get(h\string('story_key'));
		$this->model->delete_by_title(h\string($title));

		return array(true);
	}
}

/*
	protected	function uri_to($resource)
	{
		$base = h\concatenate($this->app->config['base'], $this->config['base']);
		return $base.'/'.\urlencode($resource->title);
	}

	protected	function prepare_render()
	{
		$doc = $this->app->response->body->content;
		$doc->canvas->title = h\string('My new blog');
		$mimetype = $this->app->response->header['Content-type']->head(
			$this->app->response->header['Content-type']->search(';') - 1
			);

		if(h\string('text/html')->is_equal($mimetype))
		{
			$doc->register('\horn\apps\blog\story', '\horn\apps\blog\story_html_renderer');
			$doc->register('\horn\apps\blog\stories', '\horn\apps\blog\story_html_renderer');
		}
		elseif(h\string('application/rss+xml')->is_equal($mimetype))
		{
			$doc->register('\horn\apps\blog\stories', '\horn\apps\blog\story_rss_renderer');
		}
		else
			$this->_throw_format('Unknown mimetype \'%s\'', $mimetype);

	}
*/

