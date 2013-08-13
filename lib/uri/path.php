<?php
/**
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

namespace horn\lib\uri;
use \horn\lib as h;

h\import('lib/object');
h\import('lib/uri');

class path
	extends		h\object\wrapper
{
	protected	function is_supported(h\object\base $impl)
	{
		return parent::is_supported($impl)
			&& ($impl instanceof h\uri\absolute_path
					|| $impl instanceof h\uri\net_path
					|| $impl instanceof h\uri\empty_path
					);
	}
}

class net_path
	extends h\object\public_
{
	protected	$_authority;
	protected	$_path;

	public		function __construct()
	{
		$this->_authority = new h\uri\authority;
		$this->_path = new path; // abs, net
		parent::__construct();
	}

	public		function _to_string()
	{
		return h\string('//')
			->append($this->authority->_to_string())
			->append($this->path->_to_string());
	}
}

class absolute_path
	extends		h\object\public_
{
	protected	$_segments;

	public		function __construct()
	{
		$this->_segments = new h\collection;
		parent::__construct();
	}

	public		function _to_string()
	{
		return $this->_segments->implode('/')->prepend(h\string('/'));
	}
}

class empty_path
	extends		h\object\public_
{
	public		function _to_string()
	{
		return h\string('');
	}
}

class path_factory
	extends h\uri\specific_factory
{
	public function	do_feed(h\string $meat)
	{
		$path = new path;

		if(0 === $meat->search(h\string('//')))
			$impl = $this->do_create_net_path($meat);
		elseif(0 === $meat->search(h\string('/')))
			$impl = $this->do_create_absolute_path($meat);
		elseif(0 === $meat->search(h\string('.')))
			$impl = $this->do_create_relative_path($meat);
		else
			throw $this->_exception('No path');

		$path->set_impl($impl);


		return $path;
	}

	public	function do_create_net_path(h\string $meat)
	{
		$impl = new net_path;

		$slashes = $meat->behead(2);
		if(!h\string('//')->is_equal($slashes))
			throw $this->_exception('Not a net path');

		$impl->authority->assign($this->master->factories['authority']->do_feed($meat));

		if(0 === $meat->search(h\string('/')))
			$impl->path = $this->do_feed($meat);
		else
			$impl->path->set_impl(new empty_path);

		return $impl;
	}

	public	function do_create_absolute_path(h\string $meat)
	{
		$impl = new absolute_path;

		$re = new h\regex(RE_PATH);
		if(!$re->match($meat))
			throw $this->_exception('???');

		$path = $meat->behead($re->get_matches()[0][1]);
		$impl->segments = $path->explode('/');

		return $impl;
	}

	public	function do_create_relative_path(h\string $meat)
	{
		$impl = new relative_path;

		$re = h\regex(RE_PATH);
		if($re->match($meat))
		{
			$path = $re->get_result(0);
		}

		return $impl;
	}
}

