<?php
/** Mustache templating engine
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2013, Lupus Michaelis
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

namespace horn\lib\mustache;
use \horn\lib as h;

h\import('lib/object') ;
h\import('lib/mustache/tag') ;
h\import('lib/mustache/processor') ;

interface escaper
{
	function do_escape($string);
}

// XXX
h\import('lib/render');
class html_escaper
	extends h\render\html_escaper
	implements escaper
{
	public		function do_escape($any)
	{
		return $this->a($any);
	}
}

class null_escaper
	extends h\object_public
	implements escaper
{
	public		function do_escape($any)
	{
		return $any;
	}
}

function parse($template)
{
	if(! $template instanceof h\string)
		$template = h\string($template);

	$parser = new parser;
	return $parser->do_parse($template);
}

function process($template, $context)
{
	if(! $template instanceof h\string)
		$template = h\string($template);

	$parser = new parser;
	$escaper = new html_escaper(h\string('UTF-8'));
	$processor = new processor($parser, $escaper);
	return $processor->do_process($template, $context);
}

