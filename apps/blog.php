<?php

namespace horn\apps ;

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

	public		function prepare_renderer()
	{
		var_dump($this->request->uri) ;
		switch($this->request->uri->searchpart['format'])
		{
			case 'html':
				$this->response->body->content = new \horn\lib\html ;
				break ;
			case 'rss':
				$this->response->body->content = new \horn\lib\rss ;
				break ;
			default:
				$this->_throw_internal_error() ;
		}
	}
}
