<?php 
/** 
 *
 *  \project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  \copyright	2009, Lupus Michaelis
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
use \horn\lib as h;

h\import('lib/string');
h\import('lib/collection');
h\import('lib/regex');
h\import('lib/regex-defs');

/* Before you mess this, please read http://www.w3.org/TR/uri-clarification/
 */

/** \see http://www.ietf.org/rfc/rfc2396.txt
 */
abstract
class uri
	extends		h\object_public
{
	abstract public function _is_relative();

	/** lowalpha	= "a" | "b" | "c" | "d" | "e" | "f" | "g" | "h" | "i" |
		"j" | "k" | "l" | "m" | "n" | "o" | "p" | "q" | "r" | "s" | "t" | "u" | "v" |r
		"w" | "x" | "y" | "z" */
	const		lowalpha	= 'abcdefghijklmnopqrstuvwxyz';

	/** upalpha		= "A" | "B" | "C" | "D" | "E" | "F" | "G" | "H" | "I" | "J" | "K" | "L" |
		"M" | "N" | "O" | "P" | "Q" | "R" | "S" | "T" | "U" | "V" | "W" | "X" | "Y" | "Z" */
	const		upalpha		= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/** alpha		= lowalpha | upalpha */
	const		alpha		= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/** digit		= "0" | "1" | "2" | "3" | "4" | "5" | "6" | "7" | "8" | "9" */
	const		digit		= '0123456789';

	/** alphanum	= alpha | digit */
	const		alphanum	= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	/** reserved    = ";" | "/" | "?" | ":" | "@" | "&" | "=" | "+" | "$" | "," */
	const		reserved	= ';/?:@&=+$,';

	/** mark        = "-" | "_" | "." | "!" | "~" | "*" | "'" | "(" | ")" */
	const		mark		= '-_.!~*\'()';

	/** unreserved	= alphanum | mark */
	const		unreserved	= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_.!~*\'()';

	/** hex         = digit | "A" | "B" | "C" | "D" | "E" | "F" | "a" | "b" | "c" | "d" | "e" | "f"*/
	const		hex			= '0123456789ABCDEFabcdef';

	/** escaped     = "%" hex hex */
	const		escaped		= '%';

	/** uric = reserved | unreserved | escaped
	  */
	const		uric		= ';/?:@&=+$,abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_.!~*\'()';


	public		function __construct()
	{
		$this->_scheme = h\string('');
		$this->literal = h\string('');

		parent::__construct();

		$this->parse();
	}

	public		function _to_string()
	{
		return (string) $this->literal;
	}

	protected	function get()
	{
	}

	protected	function set(h\string $literal)
	{
		$this->literal->assign($literal);
	}

	private		$literal;
	protected	$_scheme;
	protected	$_scheme_specific_part;
}

/** \brief URL describes in RFC 1738
  * \code
  * <scheme> : <scheme-specific-part>
  * <scheme> := [a-z.+-]+
  * \endcode
  *
  */
abstract
class url
	extends uri
{
	const ERR_MALFORMED				= 'URL\'s not valid.';

	const ERR_SCHEME_NO				= 'Scheme not found.';
	const ERR_SCHEME_BAD			= 'Malformed scheme.';
	const ERR_SCHEME_NOT_SUPPORTED	= 'Scheme is not supported.';

	protected		$_locator;

	/** \brief		This method must implement a way to reduce the
	 *				processed literal to a canonical literal string. 
	 *				For example, if the literal contain HTTP, it must be
	 *				lowcased
	 *  \return	boolean		Normalization status
	 */
	public		function normalize()
	{
		if($this->scheme instanceof h\string)
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
			throw $this->_exception(self::ERR_SCHEME_NO);

		$this->scheme = $this->literal->head($scheme_sep_pos - 1);
		$this->locator = $this->literal->tail($scheme_sep_pos + 1);

		$this->is_scheme_supported();
	}

	abstract protected function is_scheme_supported();
}

class path
{
	protected	$_literal;
	protected	$_nodes;

	public		function __construct(h\string $source)
	{
		$this->literal = $source;
		$this->nodes = new collection;
	}

	public		function __tostring()
	{
		return (string) $this->literal;
	}

	protected	function parse()
	{
		$re = new regex("^".RE_PATH."$");
		$re->match($this->literal);

		$this->nodes = $this->literal->explode('/');
	}
}
