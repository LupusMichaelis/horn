<?php
/** Renderers
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

namespace horn\lib\render;
use \horn\lib as h;

h\import('lib/object');

abstract
class base
	extends h\object_public
{
	protected	$_configuration;

	public		function __construct(h\configuration $configuration)
	{
		$this->_configuration = $configuration;
		parent::__construct();
	}

	abstract
	public		function do_render(h\component\context $context);
}

class json
	extends base
{
	public		function do_render(h\component\context $context)
	{
		return json_encode(array
				( 'status'	=> $context->error_handling['status']
				, 'results'	=> $context->results
				, 'errors'	=> $context->error_handling['messages']
				)
		);
	}
}

h\import('lib/render/escaper');
h\import('lib/render/strategy');

class html
	extends base
{
	protected	$_strategy;
	public		function __construct(h\configuration $configuration)
	{
		parent::__construct($configuration);
		$this->init_strategy();
	}

	private		function init_strategy()
	{
		//$this->configuration['template']['path'];
		$this->_strategy = new php_include_strategy;
		$this->strategy->escaper = new h\render\html_escaper(h\string('UTF-8'));
		$this->strategy->path = $this->configuration['template']['path'];
	}

	public		function do_render(h\component\context $context)
	{
		$view_context = (object) array
			( 'errors' => $context->error_handling['messages']
			, 'doc' => (object) array
				( 'scripts' => $this->configuration['scripts']
				, 'styles' => $this->configuration['styles']
				, 'title' => $this->configuration['title']
				)
			, 'results' => $context->results
			, 'params' => array
				( 'resource' => $context->results->key()
				, 'action' => $context->template_action
				, 'type' => 'html'
				)
			);
		$this->strategy->do_render($context->template_name, $view_context);
	}
}

class php_include_strategy
	extends h\object_public
{
	protected	$_escaper;
	protected	$_path;

	public		function template_for(h\string $resource, h\string $action, h\string $type)
	{
		return h\string::format('%s-%s.%s', $resource, $action, $type);
	}

	public		function do_render(h\string $name, $c)
	{
		$e = $this->escaper;
		include h\string::format('%s/%s.php', $this->path, $name);
	}

	public		function r($resource, $action, $type, $context)
	{
		foreach(array('resource', 'action', 'type') as $var)
			$$var instanceof h\string or $$var = h\string($$var);

		$template_file = $this->template_for($resource, $action, $type);
		return $this->do_render($template_file, $context);
	}
}


