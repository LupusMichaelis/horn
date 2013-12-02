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
h\import('lib/inet');
h\import('lib/inet/host');

class host
	extends		h\object\wrapper
{
	protected	function is_supported(h\object\base $impl)
	{
		return parent::is_supported($impl)
			&& ($impl instanceof h\inet\ip || $impl instanceof h\inet\host);
	}

	public		function _to_string()
	{
		return $this->_call('_to_string', array());
	}
}

class host_factory
	extends h\uri\specific_factory
{
	public function	do_feed(h\string $meat)
	{
		$host = new host;
		$impl = $this->create_impl_from_string($meat);
		$host->set_impl($impl);

		return $host;
	}

	private		function create_impl_from_string(h\string $literal)
	{
		if($literal[0]->__toString() === '[')
			return $this->create_ipv6_host_from_string($literal);
		elseif(\ctype_digit((string) $literal[0]))
			return $this->create_ipv4_host_from_string($literal);
		elseif(\ctype_alpha((string) $literal[0]))
			return $this->create_named_host_from_string($literal);

		throw $this->_exception('Can\'t find any host in URI');
	}

	private		function create_named_host_from_string(h\string $literal)
	{

		$end = $literal->search(':');
		if(-1 === $end) $end = $literal->search('/');
		if(-1 === $end) $end = $literal->length();

		$hostname = $literal->behead($end);
		$impl = new h\inet\host;
		$impl->segments = $hostname->explode('.');

		return $impl;
	}

}
