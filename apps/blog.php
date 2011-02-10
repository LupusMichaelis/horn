<?php

namespace horn\apps ;
use \horn\lib as h ;

require_once 'horn/lib/collection.php' ;
require_once 'horn/lib/string.php' ;

require_once 'horn/lib/app.php' ;
require_once 'horn/lib/render/html.php' ;

class blog
	extends \horn\lib\app
{
	protected	$_posts ;

	public		function run()
	{
		$this->posts = new h\collection ;
		$this->posts->push(post::create('First post', 'This is a very first post')) ;

		$this->prepare_renderer() ;
		return $this ;
	}

	static
	public		function desired_mime_type(h\http\request $in = null)
	{
		$types = array
			( 'html' => 'text/html'
			, 'rss' => 'application/application/rss+xml'
			) ;
		$path = h\string($in->uri->path) ;
		return $types[(string) $path->tail(1)] ;
	}

	public		function prepare_renderer()
	{
		$type = static::desired_mime_type($this->request) ;
		$types = array('text/html' => '\horn\lib\html') ;

		$doc = new $types[$type] ;
		$doc->title = h\string('My new blog') ;
		$doc->register('post', '\horn\apps\render_post') ;

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

function render_post(\domelement $canvas, post $post)
{
	$od = $canvas->ownerDocument ;
	$e = $od->createElement('p', $post->title) ;
	return $canvas->appendChild($e) ;
}
