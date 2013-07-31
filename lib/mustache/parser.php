<?php
/** Mustache templating engine, parser
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

h\import('lib/object');

abstract
class tag
	extends h\object_public
{
}

class begin
	extends tag
{
}

class raw
	extends tag
{
	public		$content;
}

class variable
	extends tag
{
	public		$name;
}

class section
	extends tag
{
	public		$name;
}

class inverted
	extends tag
{
	public		$name;
}

class unescaped
	extends tag
{
	public		$name;
}

class comment
	extends tag
{
}

class partial
	extends tag
{
	public		$name;
}

class set_delimiter
	extends tag
{
	public		$delimiter;
}

class close
	extends tag
{
	public		$name;
}

class end
	extends tag
{
}

class parser
	extends h\object_public
{
	const		OPENING_DELIMITER = '{{';
	const		CLOSING_DELIMITER = '}}';

	public		function do_parse(h\string $template)
	{
		$begin = 0;
		$end = 0;
		$opening_delimiter = self::OPENING_DELIMITER;
		$closing_delimiter = self::CLOSING_DELIMITER;

		$parser_stack = h\collection();
		$parser_stack[] = new tag\begin;

		do
		{
			// Open tag ////////////////////////////////////////////////////////////////////
			$end = $template->search($opening_delimiter, $begin);
			if(-1 === $end)
				$end = $template->length();

			$element = new tag\raw;
			$element->content = $template->slice($begin, $end);
			$parser_stack[] = $element;

			$begin = $end;

			if($template->length() === $begin)
				break;

			// Close tag ///////////////////////////////////////////////////////////////////
			$begin += strlen($opening_delimiter);
			$end = $template->search($closing_delimiter, $begin);

			$first = $template[$begin];
			++$begin;
			if($first->is_equal(h\string('#')))
			{
				$element =  new tag\section;
				$element->name = $template->slice($begin, $end);
				$parser_stack[] = $element;
			}
			elseif($first->is_equal(h\string('^')))
			{
				$element = new tag\inverted;
				$element->name = $template->slice($begin, $end);
				$parser_stack[] = $element;
			}
			elseif($first->is_equal(h\string('/')))
			{
				$element = new tag\close;
				$element->name = $template->slice($begin, $end);
				$parser_stack[] = $element;
			}
			elseif($first->is_equal(h\string('!')))
			{
				$element = new tag\comment;
				$element->content = $template->slice($begin, $end);
				$parser_stack[] = $element;
			}
			elseif($first->is_equal(h\string('{')))
			{
				if('}' !== $template[++$end])
					throw 'Ill-formed';

				$element = new tag\unescaped;
				$element->name = $template->slice($begin, $end - 1)->trimmed();
				$parser_stack[] = $element;
			}
			elseif($first->is_equal(h\string('&')))
			{
				$element = new tag\unescaped;
				$element->name = $template->slice($begin, $end - 1)->trimmed();
				$parser_stack[] = $element;
			}
			elseif($first->is_equal(h\string('>')))
			{
				throw 'TODO';
			}
			elseif($first->is_equal(h\string('=')))
			{
				throw 'TODO';
			}
			else
			{
				$element = new tag\variable;
				$element->name = $template->slice($begin - 1, $end)->trimmed();
				$parser_stack[] = $element;
			}

			$begin = $end = $end + strlen($closing_delimiter);
		} while(true);

		$parser_stack[] = new tag\end;

		return $parser_stack;
	}
}
