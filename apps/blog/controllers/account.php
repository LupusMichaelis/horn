<?php
/** account management application controller helper
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

namespace horn\apps\blog;
use \horn\lib as h;

h\import('lib/collection');
h\import('lib/text');
h\import('lib/regex');

h\import('lib/controller');

h\import('lib/time/date_time');
h\import('lib/text');

h\import('apps/models/account');
h\import('apps/views/account');

class account_controller
	extends h\crud_controller
{
	public		function __construct(h\component\context $context)
	{
		parent::__construct($context, new account_resource($this));
	}
}

class accounts_controller
	extends h\crud_controller
{
	public		function __construct(h\component\context $context)
	{
		parent::__construct($context, new accounts_resource($this));
	}
}

class account_resource
	extends h\resource
{
	const		name = 'account';
	const		target_class = '\horn\apps\blog\account';// account::class;

	const		not_found = '';
	const		conflict = 'Account already exists';

	public		function __construct(h\crud_controller $ctrl)
	{
		parent::__construct($ctrl, h\text(self::name), h\text(self::target_class));
	}

	public		function made_of_http_request_uri()
	{
		$name = $this->ctrl->get_segments()['name'];
		$name = h\text($name);
		$account = $this->get_resource_model()->get_by_name($name);

		if(!$this->is_managed($account))
			throw $this->_exception_ex('\horn\lib\http\not_found', static::not_found);

		return $account;
	}

	public		function made_of_http_request_post_data()
	{
		$post = $this->ctrl->get_post_data();
		$name = h\text($post['account_name']);
		$account = $this->get_resource_model()->get_by_name($name);
		return $account;
	}

	public		function create_from_http_request_post_data()
	{
		$account = $this->create_bare();
		$this->update_from_http_request_post_data($account);
		return $account;
	}

	public		function update_from_http_request_post_data($account)
	{
		$post = $this->ctrl->get_post_data();

		if(!isset($post['account_name']) || !isset($post['account_email']))
			throw $this->_exception('Incorrect POST');

		$account->name = h\text($post['account_name']);
		$account->email = h\text($post['account_email']);
		$account->modified = h\today();
		$this->get_resource_model()->update($account);
	}

	public		function delete($account)
	{
		$name = $this->ctrl->get_segments()['name'];
		$name = h\text($name);
		return $this->get_resource_model()->delete_by_name($name);
	}

	public		function uri_of($account)
	{
		$uri = h\text::format('/accounts/%s', rawurlencode($account->name));
		return $uri;
	}

	public		function uri_of_parent()
	{
		return h\text('/accounts');
	}
}

class accounts_resource
	extends h\resource
{
	const		name = 'accounts';
	const		target_class = '\horn\apps\blog\accounts';// account::class;

	const		not_found = '';
	const		conflict = 'Account already exists';

	public		function __construct(h\crud_controller $ctrl)
	{
		parent::__construct($ctrl, h\text(self::name), h\text(self::target_class));
	}

	public		function create_bare(/*$howmany*/)
	{
		$howmany = (int)func_get_arg(0);
		$bare = parent::create_bare();
		while($howmany--)
			$bare->push(new account);
		return $bare;
	}

	public		function made_of_http_request_uri()
	{
		$accounts = $this->get_resource_model()->get_all();

		if(! $this->is_managed($accounts))
			throw $this->_exception_ex('\horn\lib\http\not_found', static::not_found);

		return $accounts;
	}

	public		function made_of_http_request_post_data()
	{
		$post = $this->ctrl->get_post_data();
		$accounts = $this->create_bare(count($post['account_name']));
		for($idx = 0; $idx < $accounts->count(); ++$idx)
		{
			$name = h\text($post['account_name'][$idx]);
			$accounts[$idx]->assign($this->get_resource_model()->get_by_name($name));
		}
		return $accounts;
	}

	public		function create_from_http_request_post_data()
	{
		$post = $this->ctrl->get_post_data();
		$accounts = $this->create_bare(count($post['account_name']));

		for($idx = 0; $idx < $accounts->count(); ++$idx)
		{
			$accounts[$idx]->name = h\text($post['account_name'][$idx]);
			$accounts[$idx]->email = h\text($post['account_email'][$idx]);
			$accounts[$idx]->modified = h\today();
			$this->get_resource_model()->insert($accounts[$idx]);
		}

		return $accounts;
	}

	public		function update_from_http_request_post_data($account)
	{
		throw $this->_exception('Unsupported operation');
		return null;
	}

	public		function delete($account)
	{
		throw $this->_exception('Unsupported operation');
		return null;
	}

	public		function uri_of($account)
	{
		$uri = h\text::format('/accounts/%s', rawurlencode($account->name));
		return $uri;
	}

	public		function uri_of_parent()
	{
		return h\text('/');
	}
}
