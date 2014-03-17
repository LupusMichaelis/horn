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

abstract
class absolute
	extends		h\object_public
{
	public		function __construct()
	{
		$this->_scheme = new h\uri\scheme;
		$this->_scheme_specific_part = new h\uri\scheme_specific_part;

		parent::__construct();
	}

	abstract
	protected function is_scheme_supported(h\string $scheme);

	public	function _to_string()
	{
		$literal = h\string::format('%s:%s', $this->scheme, $this->scheme_specific_part);

		return $literal;
	}

	protected	function _set_scheme(h\string $scheme)
	{
		if(!$this->is_scheme_supported($scheme))
			throw $this->_exception('Scheme is not supported');

		$this->_scheme->assign($scheme);
	}

	protected	$_scheme;
	protected	$_scheme_specific_part;
	protected	$_search;
	protected	$_fragment;
}
