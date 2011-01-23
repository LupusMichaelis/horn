<?php

namespace horn\apps ;

require_once 'horn/lib/object.php' ;

class info
	extends \horn\lib\object_public
{
	protected	$_request ;
	protected	$_response ;

	public		function __construct(\horn\lib\http\request $in, \horn\lib\http\response $out)
	{
		$this->_request = $in ;
		$this->_response = $out ;
	}

	public		function run()
	{
		ob_start() ;
		phpinfo() ;
		$this->_response->body->content = ob_get_contents() ;
		ob_end_clean() ;
		return $this->_response ;
	}
}
