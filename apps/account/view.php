<?php
/** 
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

namespace horn\apps ;
use \horn\lib as h ;

h\import('lib/collection') ;
h\import('lib/string') ;

h\import('lib/render/html') ;
h\import('lib/render/rss') ;

class account_html_renderer
	extends h\object_public
{
	protected	$_canvas ;

	public		function __construct(\domelement $canvas)
	{
		$this->_canvas = $canvas ;
		parent::__construct() ;
	}

	public		function entry(account $account, $mode)
	{
		if($mode == 'show')
			return $this->entry_show($account) ;
		elseif($mode == 'edit')
			return $this->entry_edit($account) ;
		elseif($mode == 'delete')
			return $this->entry_delete($account) ;

		$this->_throw_format('Unknown mode \'%s\'', $mode) ;
	}

	private		function form_node($action, $method)
	{
		$od = $this->canvas->ownerDocument ;

		$form = $od->createElement('form') ;
		$form->setAttribute('action', $action) ;
		$form->setAttribute('method', $method) ;
		$form->setAttribute('accept-charset', 'utf-8') ;
		//$form->setAttribute('enctype', 'multipart/form-data') ;
		$form->appendChild($od->createElement('div')) ;

		return $form ;
	}

	private		function form_account_node(account $account, $action, $method)
	{
		$od = $this->canvas->ownerDocument ;
		$form = $this->form_node($this->link($account, array($action => null)), $method) ;
		$div = $form->firstChild ;

		$input = $div->appendChild($od->createElement('input')) ;
		$input->setAttribute('type', 'submit') ;

		$div->appendChild($od->createElement('label', 'Title')) ;
		$input = $div->appendChild($od->createElement('input')) ;
		$input->setAttribute('value', $account->name) ;
		$input->setAttribute('name', 'account.name') ;

		$div->appendChild($od->createElement('label', 'Created')) ;
		$input = $div->appendChild($od->createElement('input')) ;
		$input->setAttribute('value', $account->created->date) ;
		$input->setAttribute('name', 'account.created') ;

		$div->appendChild($od->createElement('label', 'Modified')) ;
		$input = $div->appendChild($od->createElement('input')) ;
		$input->setAttribute('value', $account->modified->date) ;
		$input->setAttribute('name', 'account.modified') ;

		$div->appendChild($od->createElement('label', 'Email')) ;
		$input = $div->appendChild($od->createElement('textarea', $account->email)) ;
		$input->setAttribute('name', 'account.email') ;

		return $form ;
	}

	private		function entry_add(account $account)
	{
		$canvas = $this->canvas ;
		$od = $canvas->ownerDocument ;

		$form = $canvas->appendChild($this->form_account_node($account, 'add', h\http\request::POST)) ;

		return $form ;
	}

	private		function entry_edit(account $account)
	{
		$canvas = $this->canvas ;
		$od = $canvas->ownerDocument ;

		$form = $canvas->appendChild($this->form_account_node
				($account, 'edit', h\http\request::POST)) ;
		$input = $form->firstChild->appendChild($od->createElement('input')) ;
		$input->setAttribute('type', 'hidden') ;
		$input->setAttribute('value', $account->name) ;
		$input->setAttribute('name', 'account.key') ;

		return $form ;
	}

	private		function entry_delete(account $account)
	{
		$canvas = $this->canvas ;
		$od = $canvas->ownerDocument ;

		$canvas->appendChild($od->createElement('div', 'Please confirm removing.')) ;

		$form = $canvas->appendChild($this->form_node
				( $this->link($account, array('delete' => null))
				, h\http\request::POST)) ;
		$div = $form->firstChild ;

		$input = $div->appendChild($od->createElement('input')) ;
		$input->setAttribute('type', 'hidden') ;
		$input->setAttribute('value', $account->name) ;
		$input->setAttribute('name', 'account.key') ;

		$input = $div->appendChild($od->createElement('input')) ;
		$input->setAttribute('type', 'submit') ;
		$input->setAttribute('value', 'Delete') ;

		return $div ;
	}

	private		function entry_show(account $account)
	{
		$canvas = $this->canvas ;
		$od = $canvas->ownerDocument ;

		$div = $canvas->appendChild($od->createElement('div')) ;

		$div->appendChild($od->createElement('h2'))
			->appendChild($od->createTextNode($account->name)) ;
		$meta = $div->appendChild($od->createElement('p')) ;
		$meta->appendChild($od->createElement('span', $account->created->date)) ;
		$meta->appendChild($od->createElement('span', $account->modified->date)) ;
		$meta->appendChild($this->action_node($account, 'edit')) ;
		$meta->appendChild($this->action_node($account, 'delete')) ;
		$div->appendChild($od->createElement('p', $account->email)) ;

		return $div ;
	}

	public		function itemise(accounts $accounts, $mode)
	{
		if($mode->is_equal(h\string('add')))
			$this->entry_add(new account) ;

		$canvas = $this->canvas ;

		$od = $canvas->ownerDocument ;
		$ul = $canvas->appendChild($od->createElement('ul')) ;
		$li = $ul->appendChild($od->createElement('li')) ;
		$a = $li->appendChild($od->createElement('a', 'Add')) ;
		$a->setAttribute('href', '?add') ;

		$ul = $canvas->appendChild($od->createElement('ul')) ;
		foreach($accounts as $account)
		{
			$li = $ul->appendChild($od->createElement('li')) ;
			$a = $li->appendChild($od->createElement('a', $account->name)) ;
			$a->setAttribute('href', $this->link($account)) ;

			$li->appendChild($od->createEntityReference('nbsp')) ;
			$a = $li->appendChild($this->action_node($account, 'edit')) ;

			$li->appendChild($od->createEntityReference('nbsp')) ;
			$a = $li->appendChild($this->action_node($account, 'delete')) ;
		}

		return $ul ;
	}

	private		function action_node(account $account, $action)
	{
		$od = $this->canvas->ownerDocument ;
		$a = $od->createElement('a', $action) ;
		$a->setAttribute('href', $this->link($account, array($action => null)));

		return $a ;
	}

	public		function summary(account $account)
	{
		$canvas = $this->canvas ;

		$od = $canvas->ownerDocument ;
		$div = $canvas->appendChild($od->createElement('div')) ;
		$a = $div->appendChild($od->createElement('a', $account->name)) ;
		$a->setAttribute('href', $this->link($account));

		return $div ;
	}

	public		function link(account $account, $params = null)
	{
		$link_renderer = new account_link_renderer ;
		return $link_renderer->link($account, $params) ;
	}
}

class account_rss_renderer
	extends h\object_public
{
	protected	$_canvas ;

	public		function __construct(\domelement $canvas)
	{
		$this->_canvas = $canvas ;
		parent::__construct() ;
	}

	public		function itemise(accounts $accounts, $mode)
	{
		$this->canvas->appendChild($this->node($accounts[0])) ;
	}

	private		function node(account $account)
	{
		$linker = new account_link_renderer ;

		$od = $this->_canvas->ownerDocument ;
		$i = $od->createElement('item') ;
		$i->setAttribute('rdf:about', $linker-> link($account)) ;
		$l = array
			( 'name' => $account->name
			, 'link' => $linker->link($account)
			, 'email' => $account->email
			) ;
		foreach($l as $t => $c)
		{
			$e = $od->createElement($t, $c) ;
			$i->appendChild($e) ;
		}

		return $i ;
	}
}

class account_link_renderer
	extends h\object_public
{
	/*
	public		function __construct(h\router $router)
	{
		$this->_canvas = $canvas ;
		parent::__construct() ;
	}
	*/

	public		function link(account $account, $params = null)
	{
		$searchpart = '' ;
		if(!\is_null($params))
		{
			$searchpart = array() ;
			foreach($params as $name => $value)
			{
				$param = \urlencode($name) ;
				if(!\is_null($value))
					$param .= '='.\urlencode($value) ;
				$searchpart[] = $param ;
			}
			$searchpart = '?'.implode('&', $searchpart) ;
		}

		return 'http://horn.localhost'.'/accounts/'.\urlencode($account->name).$searchpart ;
	}
}

