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

namespace horn\lib;
use \horn\lib as h;

h\import('lib/object');

abstract
class renderer
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

class json_renderer
	extends h\renderer
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

class html_renderer
	extends h\renderer
{
	public		function do_render(h\component\context $context)
	{
		$doc = (object) array
			( 'scripts' => $this->configuration['scripts']
			, 'styles' => $this->configuration['styles']
			, 'title' => 'Cat Groomer'
			);
		$errors = $context->error_handling['messages'];
		$template = $context->template;
		$path = $this->configuration['template']['path'];
		include h\string::format($path . '%s.php', $template);
	}
}


