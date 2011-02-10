<?php

namespace horn\lib ;

require_once 'horn/lib/app.php' ;
require_once 'horn/lib/http/message.php' ;

class simple_router
	extends app
{
	private		$_routes ;

	public		function __construct(http\request $in, http\response $out)
	{
		$this->_routes = new collection ;
		parent::__construct($in, $out) ;
	}

	public		function add_route($path, $class_name)
	{
		$this->_routes[$path] = $class_name ;
	}

	public		function run()
	{
		try
		{
			$app = $this->_routes[$this->request->uri] ;
			$app = new $app($this->request, $this->response) ;
			$app->run() ;
		}
		catch(\horn\lib\exception $e)
		{
			//$this->status(500, 'Internal Server Error') ;
			//$this->response->body->content = dump($e) ;
			throw $e ;
		}

	}
}
