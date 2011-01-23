<?php

namespace horn\lib ;

require_once 'horn/lib/object.php' ;

class router
	extends \horn\lib\object_public
{
	public $routes = array() ;

	public function process(http\request $request)
	{
		$app = $this->routes[$request->uri] ;
		$app->process($request) ;

		return $app ;
	}
}
