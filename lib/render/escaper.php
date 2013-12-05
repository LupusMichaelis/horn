<?php
/** Rendering strategy
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

namespace horn\lib\render;
use \horn\lib as h;

h\import('lib/object');

class html_escaper_helper
	extends h\object_public
{
	private		$escapers = array();
	protected	$_charset;

	public		function __construct(h\string $charset)
	{
		$this->_charset = clone $charset;
		parent::__construct();

		$this->escapers['text'] = new h\escaper\html\text($this->_charset);
		$this->escapers['attribute'] = new h\escaper\html\attribute($this->_charset);
	}

	private		function copy_and_convert(h\string $text)
	{
		return $text->to_converted($this->charset);
	}

	public		function t($text)
	{
		if(! $text instanceof h\string)
			$text = h\string($text);

		return $this->escapers['text']->do_escape($text);
	}

	public		function a($text)
	{
		if(! $text instanceof h\string)
			$text = h\string($text);

		return $this->escapers['attribute']->do_escape($text);
	}
}

class template_engine
	extends h\object_public
{
}
