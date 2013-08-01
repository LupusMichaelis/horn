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

namespace horn\apps\blog;
use \horn\lib as h;

h\import('lib/collection');
h\import('lib/string');

h\import('lib/render/html');
h\import('lib/render/rss');

class story_html_renderer
	extends h\object_public
{
	protected	$_canvas;

	public		function __construct(\domelement $canvas)
	{
		$this->_canvas = $canvas;
		parent::__construct();
	}

	public		function entry(story $story, $mode)
	{
		if($mode == 'show')
			return $this->entry_show($story);
		elseif($mode == 'edit')
			return $this->entry_edit($story);
		elseif($mode == 'delete')
			return $this->entry_delete($story);

		throw $this->_exception_format('Unknown mode \'%s\'', $mode);
	}

	private		function form_node($action, $method)
	{
		$od = $this->canvas->ownerDocument;

		$form = $od->createElement('form');
		$form->setAttribute('action', $action);
		$form->setAttribute('method', $method);
		$form->setAttribute('accept-charset', 'utf-8');
		//$form->setAttribute('enctype', 'multipart/form-data');
		$form->appendChild($od->createElement('div'));

		return $form;
	}

	private		function form_story_node(story $story, $action, $method)
	{
		$od = $this->canvas->ownerDocument;
		$form = $this->form_node($this->link($story, array($action => null)), $method);
		$div = $form->firstChild;

		$input = $div->appendChild($od->createElement('input'));
		$input->setAttribute('type', 'submit');

		$div->appendChild($od->createElement('label', 'Title'));
		$input = $div->appendChild($od->createElement('input'));
		$input->setAttribute('value', $story->title);
		$input->setAttribute('name', 'story.title');

		$div->appendChild($od->createElement('label', 'Description'));
		$input = $div->appendChild($od->createElement('textarea', $story->description));
		$input->setAttribute('name', 'story.description');

		return $form;
	}

	private		function entry_add(story $story)
	{
		$canvas = $this->canvas;
		$od = $canvas->ownerDocument;

		$form = $canvas->appendChild($this->form_story_node($story, 'add', h\http\request::POST));

		return $form;
	}

	private		function entry_edit(story $story)
	{
		$canvas = $this->canvas;
		$od = $canvas->ownerDocument;

		$form = $canvas->appendChild($this->form_story_node
				($story, 'edit', h\http\request::POST));
		$input = $form->firstChild->appendChild($od->createElement('input'));
		$input->setAttribute('type', 'hidden');
		$input->setAttribute('value', $story->title);
		$input->setAttribute('name', 'story.key');

		return $form;
	}

	private		function entry_delete(story $story)
	{
		$canvas = $this->canvas;
		$od = $canvas->ownerDocument;

		$canvas->appendChild($od->createElement('div', 'Please confirm removing.'));

		$form = $canvas->appendChild($this->form_node
				( $this->link($story, array('delete' => null))
				, h\http\request::POST));
		$div = $form->firstChild;

		$input = $div->appendChild($od->createElement('input'));
		$input->setAttribute('type', 'hidden');
		$input->setAttribute('value', $story->title);
		$input->setAttribute('name', 'story.key');

		$input = $div->appendChild($od->createElement('input'));
		$input->setAttribute('type', 'submit');
		$input->setAttribute('value', 'Delete');

		return $div;
	}

	private		function entry_show(story $story)
	{
		$canvas = $this->canvas;
		$od = $canvas->ownerDocument;

		$div = $canvas->appendChild($od->createElement('div'));

		$div->appendChild($od->createElement('h2'))
			->appendChild($od->createTextNode($story->title));
		$meta = $div->appendChild($od->createElement('p'));
		$meta->appendChild($od->createElement('span'
				, $story->created->format(h\date::FMT_YYYY_MM_DD)));
		$meta->appendChild($od->createElement('span'
				, $story->modified->format(h\date::FMT_YYYY_MM_DD)));
		$meta->appendChild($this->action_node($story, 'edit'));
		$meta->appendChild($this->action_node($story, 'delete'));
		$div->appendChild($od->createElement('p', $story->description));

		return $div;
	}

	public		function itemise(stories $stories, $mode)
	{
		if($mode->is_equal(h\string('add')))
			$this->entry_add(new story);

		$canvas = $this->canvas;

		$od = $canvas->ownerDocument;
		$ul = $canvas->appendChild($od->createElement('ul'));
		$li = $ul->appendChild($od->createElement('li'));
		$a = $li->appendChild($od->createElement('a', 'Add'));
		$a->setAttribute('href', '?add');

		$ul = $canvas->appendChild($od->createElement('ul'));
		foreach($stories as $story)
		{
			$li = $ul->appendChild($od->createElement('li'));
			$a = $li->appendChild($od->createElement('a', $story->title));
			$a->setAttribute('href', $this->link($story));

			$li->appendChild($od->createEntityReference('nbsp'));
			$a = $li->appendChild($this->action_node($story, 'edit'));

			$li->appendChild($od->createEntityReference('nbsp'));
			$a = $li->appendChild($this->action_node($story, 'delete'));
		}

		return $ul;
	}

	private		function action_node(story $story, $action)
	{
		$od = $this->canvas->ownerDocument;
		$a = $od->createElement('a', $action);
		$a->setAttribute('href', $this->link($story, array($action => null)));

		return $a;
	}

	public		function summary(story $story)
	{
		$canvas = $this->canvas;

		$od = $canvas->ownerDocument;
		$div = $canvas->appendChild($od->createElement('div'));
		$a = $div->appendChild($od->createElement('a', $story->title));
		$a->setAttribute('href', $this->link($story));

		return $div;
	}

	public		function link(story $story, $params = null)
	{
		$link_renderer = new story_link_renderer;
		return $link_renderer->link($story, $params);
	}
}

class story_rss_renderer
	extends h\object_public
{
	protected	$_canvas;

	public		function __construct(\domelement $canvas)
	{
		$this->_canvas = $canvas;
		parent::__construct();
	}

	public		function itemise(stories $stories, $mode)
	{
		$this->canvas->appendChild($this->node($stories[0]));
	}

	private		function node(story $story)
	{
		$linker = new story_link_renderer;

		$od = $this->_canvas->ownerDocument;
		$i = $od->createElement('item');
		$i->setAttribute('rdf:about', $linker-> link($story));
		$l = array
			( 'title' => $story->title
			, 'link' => $linker->link($story)
			, 'description' => $story->description
			);
		foreach($l as $t => $c)
		{
			$e = $od->createElement($t, $c);
			$i->appendChild($e);
		}

		return $i;
	}
}

class story_link_renderer
	extends h\object_public
{
	/*
	public		function __construct(h\router $router)
	{
		$this->_canvas = $canvas;
		parent::__construct();
	}
	*/

	public		function link(story $story, $params = null)
	{
		$searchpart = '';
		if(!\is_null($params))
		{
			$searchpart = array();
			foreach($params as $name => $value)
			{
				$param = \urlencode($name);
				if(!\is_null($value))
					$param .= '='.\urlencode($value);
				$searchpart[] = $param;
			}
			$searchpart = '?'.implode('&', $searchpart);
		}

		return 'http://horn.localhost'.'/fakeroot/stories/'.\urlencode($story->title).$searchpart;
	}
}

