<?php
/** Mustache templating engine, processor
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
h\import('lib/mustache/parser');

class processor
	extends h\object_public
{
	protected	$_escaper;
	protected	$_parser;

	public		function __construct(parser $parser, escaper $escaper)
	{
		$this->_parser = $parser;
		$this->_escaper = $escaper;
		parent::__construct();
	}

	public		function do_process(h\text $template, /*context*/ $context)
	{
		$parsed = $this->parser->do_parse($template);
		return $this->render_template($parsed, $context);
	}

	private		function render_template(h\collection $parser_stack, $context = array())
	{
		return $this->render_template_recursive(clone $parser_stack, $context);
	}

	private		function render_extract_section(h\collection $parser_stack, h\text $section_name)
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

	private		function render_template_recursive(h\collection $parser_stack, $context = array())
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
					$output[] = $this->escaper->do_escape($variable);
				}
			}
			elseif($element instanceof tag\section)
			{
				$variable_name = $element->name;
				$sub_parser_stack = $this->render_extract_section($parser_stack, $variable_name);

				if(isset($context->$variable_name) && $context->$variable_name)
				{
					$variable = $context->$variable_name;
					if(is_object($variable))
						$output[] = $this->render_template_recursive($sub_parser_stack, $variable);
					elseif(is_array($variable))
						foreach($variable as $var)
							$output[] = $this->render_template($sub_parser_stack, $var);
					else
						$output[] = $this->render_template_recursive($sub_parser_stack, $variable);
				}
			}
			elseif($element instanceof tag\inverted)
			{
				$variable_name = $element->name;
				$sub_parser_stack = $this->render_extract_section($parser_stack, $variable_name);

				if(!isset($context->$variable_name) || !$context->$variable_name)
					$output[] = $this->render_template_recursive($sub_parser_stack);
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
}

