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

h\import('lib/string');
h\import('lib/collection');
h\import('lib/regex');
h\import('lib/regex-defs');
h\import('lib/uri');

function create_generic()
{
	$factory = new h\uri\factory;
	$factory->do_register_factory(h\string('scheme')
			, new h\uri\scheme_factory($factory));
	$factory->do_register_factory(h\string('http')
			, new h\http\uri_factory($factory));
	$factory->do_register_factory(h\string('host')
			, new h\uri\host_factory($factory));
	$factory->do_register_factory(h\string('port')
			, new h\uri\port_factory($factory));
	$factory->do_register_factory(h\string('relative_path')
			, new h\uri\path_factory($factory));
	$factory->do_register_factory(h\string('absolute_path')
			, new h\uri\path_factory($factory));

	return $factory;
}

abstract
class specific_factory
	extends h\object_public
{
	protected	$_master;

	public		function __construct(factory $master_factory)
	{
		$this->_master = $master_factory;
		parent::__construct();
	}

	abstract
	public		function do_feed(h\string $meat);
}

/** \see http://www.ietf.org/rfc/rfc2396.txt
 */
class factory
	extends		h\object_public
{
	/** lowalpha	= "a" | "b" | "c" | "d" | "e" | "f" | "g" | "h" | "i" |
		"j" | "k" | "l" | "m" | "n" | "o" | "p" | "q" | "r" | "s" | "t" | "u" | "v" |r
		"w" | "x" | "y" | "z" */
	const		lowalpha	= 'a-z';

	/** upalpha		= "A" | "B" | "C" | "D" | "E" | "F" | "G" | "H" | "I" | "J" | "K" | "L" |
		"M" | "N" | "O" | "P" | "Q" | "R" | "S" | "T" | "U" | "V" | "W" | "X" | "Y" | "Z" */
	const		upalpha		= 'A-Z';

	/** alpha		= lowalpha | upalpha */
	const		alpha		= 'a-zA-Z';

	/** digit		= "0" | "1" | "2" | "3" | "4" | "5" | "6" | "7" | "8" | "9" */
	const		digit		= '\d';

	/** alphanum	= alpha | digit */
	const		alphanum	= 'a-zA-Z\d';

	/** reserved    = ";" | "/" | "?" | ":" | "@" | "&" | "=" | "+" | "$" | "," */
	const		reserved	= ';/?:@&=+$,';

	/** mark        = "-" | "_" | "." | "!" | "~" | "*" | "'" | "(" | ")" */
	const		mark		= '-_.!~*\'()';

	/** unreserved	= alphanum | mark */
	const		unreserved	= 'a-zA-Z\d\-_.!~*\'()';

	/** hex         = digit | "A" | "B" | "C" | "D" | "E" | "F" | "a" | "b" | "c" | "d" | "e" | "f"*/
	const		hex			= '\da-fA-F';

	/** escaped     = "%" hex hex */
	const		escaped		= '%[\da-fA-F]{2}';

	/** uric = reserved | unreserved | escaped
	  */
	const		uric		= '';

	/** scheme      = alpha *( alpha | digit | "+" | "-" | "." )
	  */
	const		scheme		= '[a-zA-Z][\w\d+.-]*';

	public		function __construct()
	{
		$this->_factories = new h\collection;
		parent::__construct();
	}

	public		function create_base()
	{
		if(!isset($this->_base_uri))
			throw $this->_exception('There is no base URI provided');

		return clone $this->base_uri;
	}

	public		function create(h\string $literal, h\uri\absolute $base = null)
	{
		// Try to find a scheme, meaning we have an absolutea. If not, we'll try to guess
		// what's the URI related to.

		$base = is_null($base) ? $this->create_base() : clone $base;
		$scheme = $this->create_scheme($literal, $base);
		if(is_null($scheme) && is_null($base->scheme))
			throw $this->_exception_no_scheme($literal);

		$base->hierarchical_part->path = $this->create_relative_part($literal, $base);

		return $base;
	}

	public		function create_relative_part(h\string $literal, h\uri\absolute $base = null)
	{
		$scheme_sep_pos = $literal->search(':');
		if(-1 === $scheme_sep_pos)
			return $this->create_relative($literal, $base);

		$scheme = $literal->head($scheme_sep_pos);
		$scheme_specific_part = $literal->tail($scheme_sep_pos + 1);
		if(h\regex_match(static::scheme, $scheme))
			return $this->create_absolute_path($literal);

		if($this->factories->has_key($scheme))
			return $this->factories[$scheme]->do_feed($scheme_specific_part);

		throw $this->_exception_format('Scheme \'%s\' not supported', $scheme);
	}

	public		function create_scheme(h\string $literal, h\uri\absolute $base = null)
	{
		return $this->factories['scheme']->do_feed($literal);
	}

	public		function create_absolute_path(h\string $literal, h\uri\absolute $base = null)
	{
		return $this->factories['absolute_path']->do_feed($literal);
	}

	public		function create_relative(h\string $literal, h\uri\absolute $base)
	{
		$relative_path = $this->factories['relative_path']->do_feed($literal);
		return $relative_path;
	}

	public		function do_register_factory(h\string $entity, specific_factory $factory)
	{
		$this->factories[$entity] = $factory;
	}

	protected		$_base_uri;
	protected		$_factories;
}

