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
h\import('lib/text');
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
	const		name = 'story';
	const		target_class = '\horn\apps\blog\story';// story::class;

	const		not_found = '';
	const		conflict = 'Story already exists';

	public		function __construct(h\crud_controller $ctrl)
	{
		parent::__construct($ctrl, h\text(self::name), h\text(self::target_class));
	}

	public		function made_of_http_request_uri()
	{
		$title = $this->ctrl->get_segments()['title'];
		$title = h\text($title);
		$story = $this->get_resource_model()->get_by_title($title);

		if(!$this->is_managed($story))
			throw $this->_exception_ex('\horn\lib\http\not_found', static::not_found);

		return $story;
	}

	public		function made_of_http_request_post_data()
	{
		$post = $this->ctrl->get_post_data();
		$title = h\text($post['story_title']);
		$story = $this->get_resource_model()->get_by_title($title);
		return $story;
	}

	public		function create_from_http_request_post_data()
	{
		$story = $this->create_bare();
		$this->update_from_http_request_post_data($story);
		return $story;
	}

	public		function update_from_http_request_post_data($story)
	{
		$post = $this->ctrl->get_post_data();

		if(!isset($post['story_title']) || !isset($post['story_description']))
			throw $this->_exception('Incorrect POST');

		$story->title = h\text($post['story_title']);
		$story->description = h\text($post['story_description']);
		$story->modified = h\today();
		$this->get_resource_model()->update($story);
	}

	public		function delete($story)
	{
		$title = $this->ctrl->get_segments()['title'];
		$title = h\text($title);
		return $this->get_resource_model()->delete_by_title($title);
	}

	public		function uri_of($story)
	{
		$uri = h\text::format('/stories/%s', rawurlencode($story->title));
		return $uri;
	}

	public		function uri_of_parent()
	{
		return h\text('/stories');
	}
}

class stories_resource
	extends h\resource
{
	const		name = 'stories';
	const		target_class = '\horn\apps\blog\stories';// story::class;

	const		not_found = '';
	const		conflict = 'Story already exists';

	public		function __construct(h\crud_controller $ctrl)
	{
		parent::__construct($ctrl, h\text(self::name), h\text(self::target_class));
	}

	public		function create_bare(/*$howmany*/)
	{
		$howmany = (int)func_get_arg(0);
		$bare = parent::create_bare();
		while($howmany--)
			$bare->push(new story);
		return $bare;
	}

	public		function made_of_http_request_uri()
	{
		$stories = $this->get_resource_model()->get_all();
		if(! $this->is_managed($stories))
			throw $this->_exception_ex('\horn\lib\http\not_found', static::not_found);

		return $stories;
	}

	public		function made_of_http_request_post_data()
	{
		$post = $this->ctrl->get_post_data();
		$stories = $this->create_bare(count($post['story_title']));
		for($idx = 0; $idx < $stories->count(); ++$idx)
		{
			$title = h\text($post['story_title'][$idx]);
			$stories[$idx]->assign($this->get_resource_model()->get_by_title($title));
		}
		return $stories;
	}

	public		function create_from_http_request_post_data()
	{
		$post = $this->ctrl->get_post_data();
		$stories = $this->create_bare(count($post['story_title']));

		for($idx = 0; $idx < $stories->count(); ++$idx)
		{
			$stories[$idx]->title = h\text($post['story_title'][$idx]);
			$stories[$idx]->description = h\text($post['story_description'][$idx]);
			$stories[$idx]->modified = h\today();
			$this->get_resource_model()->insert($stories[$idx]);
		}

		return $stories;
	}

	public		function update_from_http_request_post_data($story)
	{
		throw $this->_exception('Unsupported operation');
		return null;
	}

	public		function delete($story)
	{
		throw $this->_exception('Unsupported operation');
		return null;
	}

	public		function uri_of($story)
	{
		$uri = h\text::format('/stories/%s', rawurlencode($story->title));
		return $uri;
	}

	public		function uri_of_parent()
	{
		return h\text('/');
	}
}
