<?php

namespace horn\apps ;
use \horn\lib as h ;

require_once 'horn/lib/collection.php' ;
require_once 'horn/lib/string.php' ;

require_once 'horn/lib/app.php' ;
require_once 'horn/lib/render/html.php' ;
require_once 'horn/lib/render/rss.php' ;

class blog
	extends \horn\lib\app
{
	protected	$_posts ;

	public		function run()
	{
		$this->posts = new h\collection ;
		$this->posts->push(post::create('First post', 'This is a very first post')) ;
		$this->posts->push(post::create('Second post', 'This is a most recent post')) ;
		$this->posts->push(post::create('Third post', 'This is an awful last post')) ;

		$this->prepare_renderer() ;
		return $this ;
	}

	static
	public		function desired_mime_type(h\http\request $in = null)
	{
		$types = array
			( 'html' => 'text/html'
			, 'rss' => 'application/rss+xml'
			) ;
		$suffix = 'html' ;
		if(!is_null($in))
		{
			$path = h\string($in->uri->path) ;
			$offset = $path->search('.') ;
			$offset > -1 and $suffix = $path->tail(++$offset) ;
		}
		return $types[(string) $suffix] ;
	}

	public		function prepare_renderer()
	{
		$type = static::desired_mime_type($this->request) ;
		$types = array
			( 'text/html' => array('\horn\lib\html', '\horn\apps\render_post_html')
			, 'application/rss+xml' => array('\horn\lib\rss', '\horn\apps\render_post_rss')
			) ;

		$doc = new $types[$type][0] ;
		$doc->title = h\string('My new blog') ;
		$doc->register('post', $types[$type][1]) ;

		foreach($this->posts as $post)
			$doc->render('post', $post) ;

		$this->response->body->content = $doc ;
		//$this->response->set_content_type($type, 'utf-8') ;
	}
}

class post
	extends h\object_public
{
	public		$title ;
	public		$description ;
	public		$created ;
	public		$modified ;

	// public	$owner ;

	static
	public		function create($title, $description)
	{
		$new = new static ;
		$new->title = $title ;
		$new->description = $description ;
		return $new ;
	}
}

function render_post_html(\domelement $canvas, post $post)
{
	$od = $canvas->ownerDocument ;
	$e = $od->createElement('p', $post->title) ;
	return $canvas->appendChild($e) ;
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
	return '/post/'.urlencode($post->title) ;
}


