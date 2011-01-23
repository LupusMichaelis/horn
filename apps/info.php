<?php

namespace horn\apps ;

require_once 'horn/lib/app.php' ;
require_once 'horn/lib/router.php' ;
require_once 'horn/lib/http/message.php' ;

class info
	extends \horn\lib\app
{
	public		function run()
	{
		ob_start() ;
		phpinfo() ;
		$this->_response->body->content = ob_get_contents() ;
		ob_end_clean() ;
		return $this->_response ;
	}
}
