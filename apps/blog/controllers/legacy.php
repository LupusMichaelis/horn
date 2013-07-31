<?php
/** blog application legacy link controller helper
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

namespace horn\apps\blog;
use \horn\lib as h;

class legacy_controller
	extends h\controller
{
	protected	function &_get_model()
	{
		$s = $this->context->models->stories;
		return $s;
	}

	public		function do_control()
	{
		$path = h\string($this->app->request->uri->path);
		$legacy = $this->model->get_by_legacy_path($path);

		if(! $legacy instanceof story)
		{
			header('HTTP/1.1 404 Document not found');
			return array(false);
		}

		$this->context->router->redirect_to($this->uri_to($legacy));
		return array(true);
	}

}

