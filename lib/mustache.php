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

class processor
	extends h\object_public
{
	public		function do_process(parser $parser, context $context)
	{
	}
}

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

		if($element instanceof tag\section || $element instanceof tag\inverted)
			$element->name->is_equal($section_name) and ++$depth;
		elseif($element instanceof tag\close)
		{
			$element->name->is_equal($section_name) and --$depth;

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

		if($element instanceof tag\raw)
			$output[] = $element->content;
		elseif($element instanceof tag\variable)
		{
			$variable_name = $element->name;
			if(isset($context->$variable_name))
			{
				$variable = $context->$variable_name;
				$output[] = escape($variable);
			}
		}
		elseif($element instanceof tag\section)
		{
			$variable_name = $element->name;
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
		elseif($element instanceof tag\inverted)
		{
			$variable_name = $element->name;
			$sub_parser_stack = render_extract_section($parser_stack, $variable_name);

			if(!isset($context->$variable_name) || !$context->$variable_name)
				$output[] = render_template_recursive($sub_parser_stack);
		}
		elseif($element instanceof tag\unescaped)
		{
			$variable_name = $element->name;
			if(isset($context->$variable_name))
			{
				$variable = $context->$variable_name;
				$output[] = (string) $variable;
			}
		}
		elseif($element instanceof tag\close)
		{
			//break;
		}
	}

	return implode('', $output);
}

function parse(h\string $template)
{
	$parser = new parser;
	return $parser->do_parse($template);
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

