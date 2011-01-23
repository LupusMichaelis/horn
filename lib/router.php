<?php

namespace horn\lib ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/http/request.php' ;
require_once 'horn/lib/router.php' ;

class router
	extends \horn\lib\object_public
{
	protected $_routes ;
	protected $_request ;
	protected $_response ;

	public		function __construct(\horn\lib\http\request $in, \horn\lib\http\response $out)
	{
		$this->_routes = new collection ;
		$this->_request = $in ;
		$this->_response = $out ;
	}

	public		function run()
	{
		try
		{
			$app = $this->routes[$this->request->uri] ;
			$app = new $app($this->request, $this->response) ;
			return $app->run() ;
		}
		catch(\horn\lib\exception $e)
		{
			return http\response(500, 'Internal server error') ;
		}

	}
}
