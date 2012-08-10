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

class story_html_renderer
	extends h\object_public
{
	protected	$_canvas ;

	public		function __construct(\domelement $canvas)
	{
		$this->_canvas = $canvas ;
		parent::__construct() ;
	}

	public		function full(stories $stories)
	{
		$canvas = $this->canvas ;
		$od = $canvas->ownerDocument ;

		$div = $canvas->appendChild($od->createElement('div')) ;
		foreach($stories as $story)
		{
			$div->appendChild($od->createElement('h2', $story->title)) ;
			$meta = $div->appendChild($od->createElement('p')) ;
			$meta->appendChild($od->createElement('span', $story->created->date)) ;
			$meta->appendChild($od->createElement('span', $story->modified->date)) ;
			$link = $meta->appendChild($od->createElement('a', 'go')) ;
			$link->setAttribute('href', $this->link($story)) ;
			$div->appendChild($od->createElement('p', $story->description)) ;
		}

		return $div ;
	}

	public		function collection(stories $stories)
	{
		$canvas = $this->canvas ;

		$od = $canvas->ownerDocument ;
		$ul = $canvas->appendChild($od->createElement('ul')) ;
		foreach($stories as $story)
		{
			$li = $ul->appendChild($od->createElement('li')) ;
			$a = $li->appendChild($od->createElement('a', $story->title)) ;
			$a->setAttribute('href', $this->link($story)) ;
		}

		return $ul ;
	}

	public		function summary(story $story)
	{
		$canvas = $this->canvas ;

		$od = $canvas->ownerDocument ;
		$div = $canvas->appendChild($od->createElement('div')) ;
		$a = $div->appendChild($od->createElement('a', $story->title)) ;
		$a->setAttribute('href', $this->link($story));

		return $div ;
	}

	public		function link(story $story)
	{
		$link_renderer = new story_link_renderer ;
		return $link_renderer->link($story) ;
	}
}

class story_rss_renderer
	extends h\object_public
{
	protected	$_canvas ;

	public		function __construct(\domelement $canvas)
	{
		$this->_canvas = $canvas ;
		parent::__construct() ;
	}

	public		function node(\domelement $canvas, story $story)
	{
		$canvas = $this->canvas ;

		$od = $canvas->ownerDocument ;
		$i = $od->createElement('item') ;
		$i->setAttribute('rdf:about', story_link_renderer($story)) ;
		$l = array
			( 'title' => $story->title
			, 'link' => story_link_renderer::link($story)
			, 'description' => $story->description
			) ;
		foreach($l as $t => $c)
		{
			$e = $od->createElement($t, $c) ;
			$i->appendChild($e) ;
		}

		return $canvas->appendChild($i) ;
	}
}

class story_link_renderer
	extends h\object_public
{
	/*
	public		function __construct(h\router $router)
	{
		$this->_canvas = $canvas ;
		parent::__construct() ;
	}
	*/

	public		function link(story $story)
	{
		return '/stories/'.urlencode($story->title) ;
	}
}

