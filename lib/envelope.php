<?php
/** Envelope pattern.
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2009, Lupus Michaelis
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

/** \package	horn
 */
namespace horn\lib ;

import('lib/object') ;

/**
 */
class envelope
	extends object_public
{
	/**
	 */
	public		function __construct(object_base $content)
	{
		parent::__construct() ;
		$this->attach($content) ;
	}

	/**
	 */
	final
	public		function __call($method, $args)
	{
		if(method_exists($this->_content, $method))
			call_user_method_array($method, $this->_content, $args) ;
		else
			$this->_throw_unknown_method() ;
	}

	/**
	 */
	public		function attach(object_base $content)
	{
		$this->_content = $content ;
	}

	/** 
	 *	\see parent::_set
	 */
	protected	function _set($name, $value)
	{
		$this->_content->_set($name, $value) ;
	}

	protected	$content ;
}


