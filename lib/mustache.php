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

class parser
	extends h\object_public
{
}

class processor
	extends h\object_public
{
	public		function do_process(parser $parser, context $context)
	{
	}
}

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

const MUSTACHE_BEGIN = 0;
const MUSTACHE_RAW = 1;
const MUSTACHE_VARIABLE = 2;
const MUSTACHE_SECTION = 3;
const MUSTACHE_INVERTED = 4;
const MUSTACHE_UNESCAPED = 5;
const MUSTACHE_COMMENT = 6;
const MUSTACHE_PARTIALS = 7;
const MUSTACHE_SET_DELIMITER = 8;
const MUSTACHE_CLOSE = 9;
const MUSTACHE_END = 10;

function escape($s)
{
	return htmlentities($s, ENT_QUOTES | ENT_XML1);
}

function render_extract_section(h\collection $parser_stack, h\string $section_name)
{
	$depth = 1;
	$sub_parser_stack = h\collection();
	do
	{
		$element = $parser_stack->shift();
		$sub_parser_stack[] = $element;

		if(in_array($element['context'], array(MUSTACHE_SECTION, MUSTACHE_INVERTED)))
			$element['name']->is_equal($section_name) and ++$depth;
		elseif(MUSTACHE_CLOSE === $element['context'])
		{
			$element['name']->is_equal($section_name) and --$depth;

		if(0 === $depth)
			break;
		}
	} while(!is_null($element));

	return $sub_parser_stack;
}

function render_template(h\collection $parser_stack, $context = array())
{
	return render_template_recursive(clone $parser_stack, $context);
}

function render_template_recursive(h\collection $parser_stack, $context = array())
{
	$output = array();
	while(0 < $parser_stack->count())
	{
		$sub_parser_stack = null;
		$element = $parser_stack->shift();

		if(MUSTACHE_RAW === $element['context'])
			$output[] = $element['content'];
		elseif(MUSTACHE_VARIABLE === $element['context'])
		{
			$variable_name = $element['name'];
			if(isset($context->$variable_name))
			{
				$variable = $context->$variable_name;
				$output[] = escape($variable);
			}
		}
		elseif(MUSTACHE_SECTION === $element['context'])
		{
			$variable_name = $element['name'];
			$sub_parser_stack = render_extract_section($parser_stack, $variable_name);

			if(isset($context->$variable_name) && $context->$variable_name)
			{
				$variable = $context->$variable_name;
				if(is_object($variable))
					$output[] = render_template_recursive($sub_parser_stack, $variable);
				elseif(is_array($variable))
					foreach($variable as $var)
						$output[] = render_template($sub_parser_stack, $var);
				else
					$output[] = render_template_recursive($sub_parser_stack, $variable);
			}
		}
		elseif(MUSTACHE_INVERTED === $element['context'])
		{
			$variable_name = $element['name'];
			$sub_parser_stack = render_extract_section($parser_stack, $variable_name);

			if(!isset($context->$variable_name) || !$context->$variable_name)
				$output[] = render_template_recursive($sub_parser_stack);
		}
		elseif(MUSTACHE_UNESCAPED === $element['context'])
		{
			$variable_name = $element['name'];
			if(isset($context->$variable_name))
			{
				$variable = $context->$variable_name;
				$output[] = (string) $variable;
			}
		}
		elseif(MUSTACHE_CLOSE === $element['context'])
		{
			//break;
		}
	}

	return implode('', $output);
}

function parse_mustache(h\string $template)
{
	$begin = 0;
	$end = 0;
	$opening_delimiter = '{{';
	$ending_delimiter = '}}';
	$mustache_context = MUSTACHE_RAW;

	$parser_stack = h\collection();

	$parser_stack[] = array('context' => MUSTACHE_BEGIN);

	do
	{
		// Open tag ////////////////////////////////////////////////////////////////////
		$end = $template->search($opening_delimiter, $begin);
		if(-1 === $end)
			$end = $template->length();

		$parser_stack[] = array
			( 'context' => MUSTACHE_RAW
			, 'content' => $template->slice($begin, $end)
			);
		$begin = $end;

		if($template->length() === $begin)
			break;

		// Close tag ///////////////////////////////////////////////////////////////////
		$begin += strlen($opening_delimiter);
		$end = $template->search($ending_delimiter, $begin);

		$first = $template[$begin];
		++$begin;
		if($first->is_equal(h\string('#')))
		{
			$parser_stack[] = array
				( 'context' => MUSTACHE_SECTION
				, 'name' => $template->slice($begin, $end)
				);
		}
		elseif($first->is_equal(h\string('^')))
		{
			$parser_stack[] = array
				( 'context' => MUSTACHE_INVERTED
				, 'name' => $template->slice($begin, $end)
				);
		}
		elseif($first->is_equal(h\string('/')))
		{
			$parser_stack[] = array
				( 'context' => MUSTACHE_CLOSE
				, 'name' => $template->slice($begin, $end)
				);
		}
		elseif($first->is_equal(h\string('!')))
		{
			$parser_stack[] = array
				( 'context' => MUSTACHE_COMMENT
				, 'content' => $template->slice($begin, $end)
				);
		}
		elseif($first->is_equal(h\string('{')))
		{
			if('}' !== $template[++$end])
				throw 'Ill-formed';

			$parser_stack[] = array
				( 'context' => MUSTACHE_UNESCAPED
				, 'name' => $template->slice($begin, $end - 1)->trimmed()
				);
		}
		elseif($first->is_equal(h\string('&')))
		{
			$parser_stack[] = array
				( 'context' => MUSTACHE_UNESCAPED
				, 'name' => $template->slice($begin, $end)->trimmed()
				);
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
			$parser_stack[] = array
				( 'context' => MUSTACHE_VARIABLE
				, 'name' => $template->slice($begin - 1, $end)->trimmed()
				);
		}

		$begin = $end = $end + strlen($ending_delimiter);
	} while(true);

	$parser_stack[] = array('context' => MUSTACHE_END);

	return $parser_stack;
}


