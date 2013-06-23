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

namespace horn\lib\component ;
use \horn\lib as h;

h\import('lib/object') ;
h\import('lib/collection') ;
h\import('lib/configuration');

class context
{
}

abstract
class base
	extends		h\object_public
{
	protected	$_next_delegate;
	protected	$_configuration;

	public		function __construct(h\configuration $configuration, base $next_delegate = null)
	{
		$this->_configuration = $configuration;
		$this->_next_delegate = $next_delegate;
		parent::__construct();
	}

	protected	function &_get_next_delegate()
	{
		if($this->has_delegate())
			return $this->_next_delegate;

		$this->_throw('There is exception no more');
	}

	public		function has_delegate()
	{
		return !is_null($this->_next_delegate);
	}

	public		function do_process(context $ctx)
	{
		if(false === $this->do_before($ctx))
			return;

		if($this->has_delegate())
			$this->next_delegate->do_process($ctx);
		$this->do_after($ctx);

		return $ctx;
	}

	abstract
	protected	function do_before(context $ctx);

	abstract
	protected	function do_after(context $ctx);
}

