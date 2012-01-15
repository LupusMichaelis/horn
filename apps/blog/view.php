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

function render_story_html(\domelement $canvas, story $story)
{
	$od = $canvas->ownerDocument ;
	$div = $canvas->appendChild($od->createElement('div')) ;
	$div->appendChild($od->createElement('h2', $story->title)) ;
	$meta = $div->appendChild($od->createElement('p')) ;
	$meta->appendChild($od->createElement('span', $story->created->date)) ;
	$meta->appendChild($od->createElement('span', $story->modified->date)) ;
	$link = $meta->appendChild($od->createElement('a', 'go')) ;
	$link->setAttribute('href', render_story_link($story)) ;
	$div->appendChild($od->createElement('p', $story->description)) ;

	return $div ;
}

function render_story_rss(\domelement $canvas, story $story)
{
	$od = $canvas->ownerDocument ;
	$i = $od->createElement('item') ;
	$i->setAttribute('rdf:about', render_story_link($story)) ;
	$l = array
		( 'title' => $story->title
		, 'link' => render_story_link($story)
		, 'description' => $story->description
		) ;
	foreach($l as $t => $c)
	{
		$e = $od->createElement($t, $c) ;
		$i->appendChild($e) ;
	}

	return $canvas->appendChild($i) ;
}


function render_story_link(story $story)
{
	return '/story/'.urlencode($story->title) ;
}

