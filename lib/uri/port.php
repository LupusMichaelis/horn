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
use horn\lib as h;

h\import('lib/inet/url');


class port_factory
	extends h\uri\specific_factory
{
	public function	do_feed(h\string $meat)
	{
		for($end_port = 0; \is_numeric((string) $meat[$end_port]); ++$end_port)
			/* */;

		$port = $meat->behead($end_port);
		$port = $port->as_integer();

		if(1 > $port || $port > 65535)
			throw $this->_exception_format('Specified port \'%d\' is incorrect', $port);

		return $port;
	}
}
