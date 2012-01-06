<?php

namespace horn\lib ;

require_once 'horn/lib/object.php' ;
require_once 'horn/lib/http/message.php' ;

function render(http\response $out)
{
	ob_start() ;
	echo $out->body->content ;

	header($out->status) ;
	foreach($out->header as $name => $value)
		header("$name: $value") ; // XXX escape

	ob_end_flush() ;
}

function run(http\request $in, http\response $out, $config)
{
	setlocale(LC_ALL, $config['locale']) ;

	$routing = &$config['routing'];
	ksort($routing) ;
	foreach($routing as $key => $value)
		if($key === 0)
			$main = new $value($in, $out, $config) ;
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

	protected	$_config ;

	protected	$_data_proxy ;

	abstract
	public		function run() ;

	public		function __construct(http\request $in, http\response $out, $config)
	{
		$this->_config = $config ;
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


