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

class authority
	extends		h\object\public_
{
	protected	$_user;
	protected	$_password;
	protected	$_host;
	protected	$_port;

	public		function __construct()
	{
		$this->_user		= h\text('');
		$this->_password	= h\text('');
		$this->_host		= new h\uri\host;
		parent::__construct();
	}

	public		function _to_string()
	{
		$authority = $this->_host->_to_string();
		is_null($this->port)
			or $authority->append(h\text::format(':%d', $this->port));
		if(0 < $this->password->length())
		{
			if(0 < $this->user->length())
				$authority->prepend(h\text::format('%s:%s@'
							, $this->user , $this->password));
			else
				throw $this->_exception('Malformed authority: password but no username');

		}
		elseif(0 < $this->user->length())
			$authority->prepend(h\text::format('%s@', $this->user));

		return $authority;
	}
}

/** hierarchical part
 */
class hierarchical_part
	extends		h\object\public_
{
	public		function __construct()
	{
		$this->_authority = new authority;
		$this->_path = new path;
		$this->_query = new query;
	}

	public		function _to_string()
	{
		$s = $this->_path->_to_string();
		if($this->_query->count())
			$s->append(h\text('?'))->append($this->_query->_to_string());

		return $s;
	}

	protected	function _set_path($path)
	{
		if($path instanceof h\path)
			$this->_path->assign($path);
		elseif($path instanceof h\collection)
			$this->_path->segments = $path;
		else
			throw $this->_exception('XXX');

	}

	protected	function &_get_authority()
	{
		$authority = $this->_path->authority;
		return $authority;
	}

	protected	function _set_authority($value)
	{
		$this->_path->authority = $value;
	}

	protected	$_authority;
	protected	$_path;
	protected	$_query;
}

/**
  */
class authority_factory
	extends h\uri\specific_factory
{
	public		$secured = false;
	public		function	do_feed(h\text $meat)
	{
		$authority = new authority;

		// XXX Must modify assignment in h\wrapper
		$host = $this->master->factories['host']->do_feed($meat);
		$authority->host->set_impl($host->get_impl());

		$port = $this->master->factories['port']->do_feed($meat);
		$authority->port = $port;

		return $authority;
	}
}

/**
  */
class hierarchical_part_factory
	extends h\uri\specific_factory
{
	public function	do_feed(h\text $meat)
	{
		$hierarchical_part = new hierarchical_part;
		$hierarchical_part->path->set_impl($this->master->factories['path']->do_feed($meat)->get_impl());

		return $hierarchical_part;
	}
}

