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

namespace horn\apps\blog;
use \horn\lib as h;

h\import('lib/collection');
h\import('lib/string');
h\import('lib/regex');

h\import('lib/db/connect');

h\import('lib/time/date_time');

h\import('apps/models/blog');
h\import('apps/views/blog');
h\import('apps/views/page_html');

class story_controller
	extends h\crud_controller
{
	public		function __construct(h\component\context $context)
	{
		parent::__construct($context, new story_resource($this));
	}
}

class stories_controller
	extends h\crud_controller
{
	public		function __construct(h\component\context $context)
	{
		parent::__construct($context, new stories_resource($this));
	}
}

class story_resource
	extends h\resource
{
	public		$name = 'story';
	public		$class = '\horn\apps\blog\story';// story::class;

	const		not_found = '';
	const		conflict = 'Story already exists';

	public		function of_http_request_uri()
	{
		$title = $this->ctrl->get_segments()['title'];
		$title = h\string($title);
		$story = $this->ctrl->get_model()->get_by_title($title);
		return $story;
	}

	public		function of_http_request_post_data()
	{
		$post = $this->ctrl->get_post_data();
		$title = $post[h\string('story_title')];
		$story = $this->ctrl->get_model()->get_by_title(h\string($title));
		return $story;
	}

	public		function create_from_http_request_post_data()
	{
		$story = new story;
		$this->update_from_http_request_post_data($story);
		return $story;
	}

	public		function update_from_http_request_post_data($story)
	{
		$post = $this->ctrl->get_post_data();
		$story->title = h\string($post['story_title']);
		$story->description = h\string($post['story_description']);
		$story->modified = h\today();
		$this->ctrl->get_model()->update($story);
	}

	public		function delete($story)
	{
		$post = $this->ctrl->get_post_data();
		$title = $post->get(h\string('story_title'));
		return $this->ctrl->model->delete_by_title(h\string($title));
	}

	public		function uri_of($story)
	{
		$uri = sprintf('/stories/%s', rawurlencode($story->title));
		return $uri;
	}
}

	// $stories = $this->model->get_all();
