<?php

namespace horn\apps ;

require_once 'horn/lib/horn.php' ;

\horn\lib\import('lib/app') ;

class info
	extends \horn\lib\app
{
	public		function run()
	{
		ob_start() ;
		phpinfo() ;
		//$this->_response->status = '200 Ok' ;
		$this->_response->body->content = ob_get_contents() ;
		ob_end_clean() ;
		return $this ;
	}
}
