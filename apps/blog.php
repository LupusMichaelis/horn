<?php

namespace horn\apps ;
use \horn\lib as h ;

require_once 'horn/lib/app.php' ;
require_once 'horn/lib/render/html.php' ;

class blog
	extends \horn\lib\app
{
	public		function run()
	{
		$this->prepare_renderer() ;
		return $this ;
	}

	static
	public		function desired_mime_type(h\http\request $in = null)
	{
		return 'text/html' ;
	}

	public		function prepare_renderer()
	{
		$type = static::desired_mime_type($this->request) ;
		$types = array('text/html' => '\horn\lib\html') ;

		$doc = new $types[$type] ;
		$doc->title = h\string('My new blog') ;

		$this->response->body->content = $doc ;
		//$this->response->set_content_type($type, 'utf-8') ;
	}
}
