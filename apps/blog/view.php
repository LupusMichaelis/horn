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

function render_post_html(\domelement $canvas, post $post)
{
	$od = $canvas->ownerDocument ;
	$div = $canvas->appendChild($od->createElement('div')) ;
	$div->appendChild($od->createElement('h2', $post->title)) ;
	$meta = $div->appendChild($od->createElement('p')) ;
	$meta->appendChild($od->createElement('span', (string) $post->created)) ;
	$meta->appendChild($od->createElement('span', (string) $post->modified)) ;
	$div->appendChild($od->createElement('p', $post->description)) ;

	return $div ;
}

function render_post_rss(\domelement $canvas, post $post)
{
	$od = $canvas->ownerDocument ;
	$i = $od->createElement('item') ;
	$i->setAttribute('rdf:about', render_post_link($post)) ;
	$l = array
		( 'title' => $post->title
		, 'link' => render_post_link($post)
		, 'description' => $post->description
		) ;
	foreach($l as $t => $c)
	{
		$e = $od->createElement($t, $c) ;
		$i->appendChild($e) ;
	}

	return $canvas->appendChild($i) ;
}


function render_post_link(post $post)
{
	return '/story/'.urlencode($post->title) ;
}

