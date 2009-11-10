<?php
/** MVC Application base.
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

namespace horn ;

require_once 'horn/lib/horn.php' ;
require_once 'horn/lib/object.php' ;
require_once 'horn/lib/collection.php' ;

require_once 'horn/lib/mvc/controller.php' ;
require_once 'horn/lib/mvc/view.php' ;

/** \todo Security issues:
 *  For now, I check file existing with is_dir and file_exists. But this bullshit don't test for local files only. And they
 *  are no function to do so.
 */

abstract
class application
	extends object_public
{
	protected	$_name ;
	protected	$_path ;

	protected	$_linker ;

	protected	$_controller ;
	protected	$_model ;
	protected	$_view ;

	protected	$_dbcon ;

	public		function __construct()
	{
		parent::__construct() ;

		$this->model = null ;
		$this->view = null ;
	}

	public		function run($get, $post)
	{
		/*
		$path = './' . $this->name .'/' ;

		if(!is_dir($path))
			$this->_throw_format('Controller for application \'%s\' doesn\'t exist.', $this->name) ;

		$class_name = require_once $path . 'controller.php' ;
		$this->_controller = new $class_name($this) ;
		*/

		return $this->_controller
			? $this->_controller->process($get, $post)
			: $this->_throw('Blurp') ;
	}

	public		function __tostring()
	{
		return (string) $this->_view ;
	}

	/*
	abstract
	protected	function _set_model($model = null) ;

	abstract
	protected	function _set_view(view $view = null) ;
	*/
}


