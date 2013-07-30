<?php
/** user management application controller helper
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2011, Lupus Michaelis
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

namespace horn\apps\user ;
use \horn\lib as h ;

h\import('lib/collection') ;
h\import('lib/string') ;
h\import('lib/regex') ;

h\import('lib/app') ;
h\import('lib/controller') ;

h\import('lib/time/date_time') ;
h\import('lib/string') ;

h\import('apps/models/user') ;
h\import('apps/views/user') ;

class controller
	extends h\crud_controller
{
	protected	function &_get_model()
	{
		$s = $this->context->models->users ;
		return $s ;
	}

	public function do_create()
	{
		$name = $this->get_search_part(h\string('account_name')) ;
		$account = $this->model->get_by_name(h\string($name)) ;

		if($account instanceof account)
		{
			header('HTTP/1.1 409 Conflict');
			return array(false, null, array('Account already exists')) ;
		}

		$account = new account;
		$account->name = $this->get_post_data(h\string('account_name'));
		$account->email = $this->get_post_data(h\string('account_email'));
		$account->created = $account->modified = h\today();

		return array(true, compact('account')) ;
	}

	public function do_read()
	{
		$this->resource['type'] = '\horn\apps\user\account' ;
		$account = $this->model->get_by_name($this->resource['title']) ;
		if(! $account instanceof account)
		{
			header('HTTP/1.1 404 Not found');
			return array(false);
		}

		return array(true, compact('account')) ;
	}
	/*
	protected	function get_collection()
	{
		$accounts = $this->model->get_all();
		return array(true, compact('accounts'));
	}
	*/

	public function do_update()
	{
		$name = $this->get_post_data(h\string('account_key')) ;
		$account = $this->model->get_by_name(h\string($name)) ;

		$copy = clone $account;
		$copy->name = $this->get_post_data(h\string('account_name'));
		$copy->email = $this->get_post_data(h\string('account_email'));
		$copy->modified = h\today();
		$account->assign($copy) ;

		header('HTTP/1.1 200 OK');

		return array(true, compact('account')) ;
	}

	public function do_delete();
	{
		$name = $this->get_post_data(h\string('account_key')) ;
		$account = $this->model->get_by_name(h\string($name)) ;
		$this->model->delete($account) ;

		header('HTTP/1.1 201 Created');
		// XXX header('Location:')

		return array(true, compact('account')) ;
	}
}


