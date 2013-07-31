<?php 
/** 
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2009, Lupus Michaelis
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

namespace horn\lib;

import('lib/string');
import('lib/collection');
import('lib/regex');
import('lib/regex-defs');

class uri
	extends		object_public
{
	protected	$_literal;

	public		function __construct(string $literal)
	{
		parent::__construct();

		$this->literal = $literal;
		$this->parse();
	}

	public		function __tostring()
	{
		return $this->literal->__tostring();
	}
}

class urn extends uri
{
}

/** \brief URL describes in RFC 1738
  * \code
  * <scheme> : <scheme-specific-part>
  * <scheme> := [a-z.+-]+
  * \endcode
  *
  */
class url extends uri
{
	const ERR_MALFORMED				= 'URL\'s not valid.';

	const ERR_SCHEME_NO				= 'Scheme not found.';
	const ERR_SCHEME_BAD			= 'Malformed scheme.';
	const ERR_SCHEME_NOT_SUPPORTED	= 'Scheme is not supported.';

	protected		$_scheme;
	protected		$_locator;

	/** \brief		This method must implement a way to reduce the
	 *				processed literal to a canonical literal string. 
	 *				For example, if the literal contain HTTP, it must be
	 *				lowcased
	 *  \return	boolean		Normalization passed good
	 */
	public		function normalize()
	{
		if($this->scheme instanceof string)
			$this->scheme->lowcase();
		else
			return false;

		$this->sync_literal();
		return true;
	}

	public		function sync_literal()
	{
		$this->literal->reset();
		$this->literal->glue($this->scheme, ':', $this->location);
	}

	protected	function parse()
	{
		$scheme_sep_pos = $this->literal->search(':');
		if($scheme_sep_pos < 0)
			throw new exception(self::ERR_SCHEME_NO);

		$this->scheme = $this->literal->head($scheme_sep_pos - 1);
		$this->locator = $this->literal->tail($scheme_sep_pos + 1);

		$this->is_scheme_supported();
	}

	protected function is_scheme_supported()
	{
		return true;
	}
}



