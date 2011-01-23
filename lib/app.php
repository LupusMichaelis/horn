<?php

namespace horn\lib ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/http/message.php' ;

function app(\horn\lib\http\request $in, \horn\lib\http\response $out, $routing)
{
	ksort($routing) ;
	foreach($routing as $key => $value)
		if($key === 0)
			$main = new $value($in, $out) ;
		elseif(400 < $key && $key < 600) // XXX refine that
			$main->add_error_handler($key, $value) ;
		else
			$main->add_route($key, $value) ;

	return $main ;
}

abstract
class app
	extends object_public
{
	protected	$_request ;
	protected	$_response ;

	abstract
	public		function run() ;

	public		function __construct(\horn\lib\http\request $in, \horn\lib\http\response $out)
	{
		$this->_request = $in ;
		$this->_response = $out ;

		parent::__construct() ;
	}

	public		function not_found()
	{
		$this->status('404', 'Not found') ;
	}

	public		function status($code, $message)
	{
		$this->response->status = sprintf('%s %s %s', $this->request->version, $code, $message) ;
	}
}


