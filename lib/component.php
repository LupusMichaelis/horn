<?php
/** A component of a chain of responsabilities
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

namespace horn\lib\component;
use \horn\lib as h;

h\import('lib/object');
h\import('lib/collection');
h\import('lib/configuration');
h\import('lib/decorator');

function build(h\configuration $configuration, context $ctx)
{
	$component = null;
	// We build the onion from core to skin
	$components = h\c($configuration['components'])->reverse();
	foreach($components as $layer)
	{
		h\import('lib/component/'.$layer);
		$component_class = "\\horn\\lib\\component\\$layer";
		$component = new $component_class($configuration, $component);
	}

	return $component;
}

class context
{
}

abstract
class base
	extends		h\decorator
{
	protected	$_configuration;

	public		function __construct(h\configuration $configuration, base $next = null)
	{
		$this->_configuration = $configuration;
		parent::__construct($next);
	}

	public		function do_process(context $ctx)
	{
		if(false === $this->do_before($ctx))
			return; // The component failed, thus we shan't continue in inconsistant state

		if($this->has_next())
			$this->next->do_process($ctx);
		$this->do_after($ctx);

		return $ctx;
	}

	abstract
	protected	function do_before(context $ctx);

	abstract
	protected	function do_after(context $ctx);
}

